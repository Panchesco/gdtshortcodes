<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package     ExpressionEngine
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2003 - 2016, EllisLab, Inc.
 * @license     http://expressionengine.com/user_guide/license.html
 * @link        http://expressionengine.com
 * @since       Version 2.0
 * @filesource
 */

/**
 * Good at Shortcodes Extension
 *
 * @package    ExpressionEngine
 * @subpackage Addons
 * @category   Extension
 * @author     Richard Whitmer
 * @link       https://github.com/panchesco
 */
 
/**
 * We're handling Twitter authentication with Twitter oAuth
 *
 * https://twitteroauth.com/	
 */

require_once("libraries/twitteroauth/autoload.php");
use Abraham\TwitterOAuth\TwitterOAuth;
//////////////////////////////////////////////////////
	
class Gdtshortcodes_ext {

    var $settings     = array();
    var $version			= '2.0.0';
    var $name       	= 'Good at Shortcodes';
    var $description	= 'Render embedded content via shortcodes saved in a channel entry.';
    var $settings_exist = 'y';
    var $docs_url = 'https://github.com/Panchesco/gdtshortcodes';
    var $shortcode_options = array(
    	'ig' => 'Instagram',
    	'youtube' => 'YouTube',
    	'tweet' => 'Twitter');
    var $connection;

    /**
     * Constructor
     *
     * @param   mixed   Settings array or empty string if none exist.
     */
    function __construct()
    {
         // Get extension settings.
				 ee()->db->select('settings');
				 ee()->db->where('class',__CLASS__);
				 $query = ee()->db->get('extensions');
				 
				 if($query->num_rows()==1)
				 {
					 $row = $query->row();
					 $this->settings = unserialize($row->settings);
				 	
				 	} else {
					 
					 $this->settings = array(
		    		'enabled_shortcodes' => array(),
		    		'twitter_consumer_key' => '',
		    		'twitter_consumer_secret' => '',
		    		'twitter_access_token' => '',
		    		'twitter_access_secret' => ''
		    	);
				 } 
				 
				 // Trim settings values. 
				 foreach($this->settings as $key => $row)
				 {
					 
					 if(is_array($row))
					 {
						 $this->settings[$key] = array_map('trim',$row);
					 } else {
						 $this->settings[$key] = trim($row);
					 }
					 
				 }
				 
    }
    
    //-----------------------------------------------------------------------------
    
		/**
		 * Activate Extension
		 *
		 * This function enters the extension into the exp_extensions table
		 *
		 * @see https://ellislab.com/codeigniter/user-guide/database/index.html for
		 * more information on the db class.
		 *
		 * @return void
		 */
		public function activate_extension()
		{

		    $data = array(
		        'class'     => __CLASS__,
		        'method'    => 'render_shortcodes',
		        'hook'      => 'template_post_parse',
		        'settings'  => serialize($this->settings),
		        'priority'  => 10,
		        'version'   => $this->version,
		        'enabled'   => 'y'
		    );
		
		    ee()->db->insert('extensions', $data);
		}
	
	//-----------------------------------------------------------------------------
	

	function settings()
	{

		   	$addons = array();
		    $all_addons = ee('Addon')->all();
		    foreach($all_addons as $name => $info)
		    {
			    $info = ee('Addon')->get($name);
			    if($info->isInstalled() && $info->get('settings_exist'))
			    {
				    $addons[$name] = $info->getName();		    
			    }
		    }
			ksort($addons);
			
			
			$settings['enabled_shortcodes'] = array('c',$this->shortcode_options,$this->settings['enabled_shortcodes']);
			$settings['twitter_consumer_key'] = array('i','',$this->settings['twitter_consumer_key']);
			$settings['twitter_consumer_secret'] = array('i','',$this->settings['twitter_consumer_secret']);
			$settings['twitter_access_token'] = array('i','',$this->settings['twitter_access_token']);
			$settings['twitter_access_secret'] = array('i','',$this->settings['twitter_access_secret']);
			

	  return $settings;
	}
	
	//-----------------------------------------------------------------------------

/**
 * Update Extension
 *
 * This function performs any necessary db updates when the extension
 * page is visited
 *
 * @return  mixed   void on update / false if none
 */
public function update_extension($current = '')
{
    if ($current == '' OR $current == $this->version)
    {
        return FALSE;
    }

    if ($current < '1.0')
    {
        // Update to version 1.0
    }

    ee()->db->where('class', __CLASS__);
    ee()->db->update(
                'extensions',
                array('version' => $this->version)
    );
 }

	//-----------------------------------------------------------------------------

/**
 * Disable Extension
 *
 * This method removes information from the exp_extensions table
 *
 * @return void
 */
public function disable_extension()
{
    ee()->db->where('class', __CLASS__);
    ee()->db->delete('extensions');
}

/**
 * Render shortcodes in parsed template.
 *
 * @return string
 */
	public function render_shortcodes()
	{
			
		$parsed_template = ee()->TMPL->template;
		

		// Instagram 
		if(in_array('ig',$this->settings['enabled_shortcodes']))
		{
				$pattern = "/\[ig .*]{1}/";
				$parsed_template = preg_replace_callback($pattern,"self::embed_instagram",$parsed_template);
		}
		
		// Twitter
		if(in_array('tweet',$this->settings['enabled_shortcodes']))
		{
			
		
			$this->connection = new TwitterOAuth($this->settings['twitter_consumer_key'],
																						$this->settings['twitter_consumer_secret'],
																						$this->settings['twitter_access_token'],
																						$this->settings['twitter_access_secret']);
			
			$pattern = "/\[tweet .*]{1}/";
			$parsed_template = preg_replace_callback($pattern,"self::embed_tweet",$parsed_template);
		
		}
		
		// YouTube 
		if(in_array('youtube',$this->settings['enabled_shortcodes']))
		{
				$pattern = "/\[youtube .*]{1}/";
				$parsed_template = preg_replace_callback($pattern,"self::embed_youtube",$parsed_template);
		}
	
		 return $parsed_template;
	
	}
	
	//-----------------------------------------------------------------------------

	/**
	 * Embed a youtube video from parsed shortcode.
	 * 
	 * @return string.
	 */
	private function embed_youtube($matches)
	{	 
		 $html = '';
		 $q = '';
		 
		 $str = preg_replace("/\[|\]|youtube/i",'',$matches[0]);
		 
		 $parse = parse_url($str,PHP_URL_QUERY);
		 
		 parse_str($parse,$vars);
		 
		if(isset($vars['v']))
		{
			$html.= '<iframe src="//youtube.com/embed/' . $vars['v'];
			
			if(isset($vars['rel']))
			{
				$q.= '&amp;rel=' . $vars['rel'];
			}
			
			if(isset($vars['controls']))
			{
			
				$q.= '&amp;controls=' . $vars['controls'];
			}

			if(isset($vars['showinfo']))
			{
				$q.= '&amp;showinfo=' . $vars['showinfo'];
			}
			
			if(isset($vars['start']))
			{
				$q.= '&amp;start=' . $vars['start'];
			}
			
			if(isset($vars['end']))
			{
				$q.= '&amp;end=' . $vars['end'];
			}
			
			if(strlen($q)>0)
			{
				$html.= '?' . trim($q,'&amp;');
			}
			
			$html.= '"';
			
			if(isset($vars['w']))
			{
				$html.= ' width="' . $vars['w'] . '"';
			}
			
			if(isset($vars['h']))
			{
				$html.= ' height="' . $vars['h'] . '"';
			}
			
			if(isset($vars['class']))
			{
				$html.= ' class="' . $vars['class'] . '"';
			}
			
			if(isset($vars['id']))
			{
				$html.= ' id="' . $vars['id'] . '"';
			}
			
			$html.= ' frameborder="0" allowfullscreen></iframe>';
		}
		
		return $html;
	 
	 }
	
	//-----------------------------------------------------------------------------
		
	/**
	 * Embed a tweet.
	 * 
	 * Uses Twitter oembed endpoint
	 * https://dev.twitter.com/rest/reference/get/statuses/oembed
	 *
	 * Authentication handled with TwitterOAuth library.
	 * https://twitteroauth.com/
	 *
	 * @return string 
	 */
	private function embed_tweet($matches)
	{	 

		if(isset($matches[0]))
		 {
		 		
		 		// We need to get the id and options from shortcode.
		 		$url = preg_replace("/\[|\]|tweet/",'',$matches[0]);
		 		
		 		// The tweet id is the last segment of the URL.
		 		$segments = explode("/",parse_url($url)['path']);
		 		$id = end($segments);
		 		
		 		// The options are in the PHP_URL_QUERY portion of the URL. 
		 		$query_string = parse_url($url,PHP_URL_QUERY);
		 		
		 		 parse_str($query_string,$params);
		 		 
		 		 // Fold the id into the options.
		 		 $params['id'] = $id;

		 		 $response = $this->connection->get('statuses/oembed',$params);
		 		 
		 		 if(isset($response->html))
		 		 {
		 		   return $response->html;
		 		 } 		 

		 		 // If debugging enabled return errors to Super Admins
		 		 if(ee()->config->item('debug') == 1 && ee()->session->userdata('group_id') == 1)
		 		 {
			 		 
		 		 	if(isset($response->errors[0]))
		 		 	{
		 		 	  return '
		 		 	  Twitter says: ' . $response->errors[0]->code . ' ' . $response->errors[0]->message . '<br>
		 		 	  ';
		 		 	
		 		 	}
		 		 	
		 		 } 
		 }
		 
		 return '';

	 }
	
		//-----------------------------------------------------------------------------
		
		/**
		 * Embed Instagram
		 * 
		 * @return string
		 */
		 private function embed_instagram($matches)
		 {
			 
			 if(isset($matches[0]))
			 {
				 // We need to get the id and options from shortcode.
		 		$url = preg_replace("/\]|\[ig|www\./",'',$matches[0]);
		 		$url = str_replace("instagram.com",'instagr.am',$url);
		 		$url = str_replace("https://",'http://',$url);
		 		$url = str_replace("?",'&',$url);
		 		$url = preg_replace("/ {1,}/",'',$url);
		 		$url = 'https://api.instagram.com/oembed?url=' . $url;
				
				$options = array('http' => array(
        'method'  => 'GET',
				'ignore_errors' => TRUE,
        'header'  => 
            "Content-Type: application/json\r\n"
				));
				
				$context  = stream_context_create($options);
				$response = file_get_contents($url, false, $context);
				$data = json_decode($response);
				
				if(isset($data->html))
				{
					return $data->html;
					
					} elseif(ee()->config->item('debug') == 1 && ee()->session->userdata('group_id') == 1) { 
					
					// If the response wasn't the json object we expected return an error message for Super Admins
					return '
					Instagram says: ' . $response . '<br> 
					using URL ' . $url . '
					';
				}

			 }
			 
			 return '';
		 }
		 
		 
		 //-----------------------------------------------------------------------------
		

		// END
}
// END CLASS
/* End of file ext.gdtshortcodes.php */
/* Location: ./system/user/addons/gdtshortcodes/ext.gdtshortcodes.php */