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
    var $version			= '2.1.2';
    var $name       	= 'Good at Shortcodes';
    var $description	= 'Render embedded content via shortcodes saved in a channel entry.';
    var $settings_exist = 'y';
    var $docs_url = 'https://github.com/Panchesco/gdtshortcodes';
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
		    		'enabled_shortcodes' => array(''),
		    		'twitter_app' => array(
		    				'consumer_key' => '',
		    				'consumer_secret' => '',
		    				'access_token' => '',
		    				'access_token_secret' => ''
		    			),
		    	);
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
	
	/**
	 * Settings Form
	 *
	 * @param   Array   Settings
	 * @return  void
	 */
	public function settings_form($current)
	{
	    ee()->load->helper('form');
	    ee()->load->library('table');
	    
	    $vars = array();
	
			$shortcodes = array(
				'instagram' => 'Instagram',
				'twitter' => "Twitter",
				'youtube' => 'YouTube',
				'vimeo' => 'Vimeo');
				
			$twitter_app = array(
				'consumer_key' => '',
				'consumer_secret' => '',
				'access_token' => '',
				'access_token_secret' => ''
			);
	    															
	    $vars['settings']['enabled_shortcodes'] =  form_multiselect('enabled_shortcodes[]',$shortcodes,$current['enabled_shortcodes']);
		  
		  foreach($twitter_app as $key => $row)
		  {
			  
			  $data = array(
			  	'name' => 'twitter_app[' . $key . ']',
			  	'value' => $current['twitter_app'][$key],
			  	'placeholder' => lang($key)
			  	);
			  
			  $vars['settings']['twitter_app'][$key] = form_input($data);
		  } 	
		    	
			return ee()->load->view('index', $vars, TRUE);
		}
	
	//-----------------------------------------------------------------------------
	
	public function save_settings()
	{

		unset($_POST['submit']);
		
		
		foreach($_POST as $key => $row)
		{
			$data['settings'][$key] = ee()->input->post($key,TRUE);
			
			if(is_array($data['settings'][$key]))
			{
				$data['settings'][$key] = array_map('trim',$data['settings'][$key]);
				} else {
				$data['settings'][$key] = trim($data['settings'][$key]);
			}
			
			
			
		}
		
		// Be certain enabled_shortcodes is array.
		if( ! ee()->input->post('enabled_shortcodes',TRUE))
		{
			$data['settings']['enabled_shortcodes'] = array();
		}
		
		
		$data['settings'] = serialize($data['settings']);
		
		ee()->db->where('class',__CLASS__);
		ee()->db->limit(1);
		
		return ee()->db->update('extensions',$data);
		
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

    if ($current < '2.1.0')
    {
        // Update to version 2.1.0
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
		if(in_array('instagram',$this->settings['enabled_shortcodes']))
		{
				$pattern = "/\[ig .*]{1}/";
				$parsed_template = preg_replace_callback($pattern,"self::embed_instagram",$parsed_template);
		}
		
		// Twitter
		if(in_array('twitter',$this->settings['enabled_shortcodes']))
		{
			// Set settings['twitter_app'] elements to variables.
			extract($this->settings['twitter_app']);
			
			$this->connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
			
			$pattern = "/\[tweet .*]{1}/";
			$parsed_template = preg_replace_callback($pattern,"self::embed_tweet",$parsed_template);
		
		}
		
		// YouTube 
		if(in_array('youtube',$this->settings['enabled_shortcodes']))
		{
				$pattern = "/\[youtube .*]{1}/";
				$parsed_template = preg_replace_callback($pattern,"self::embed_youtube",$parsed_template);
		}
		
		// Vimeo 
		if(in_array('vimeo',$this->settings['enabled_shortcodes']))
		{
				$pattern = "/\[vimeo .*]{1}/";
				$parsed_template = preg_replace_callback($pattern,"self::embed_vimeo",$parsed_template);
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
		 
		 $str = preg_replace("/\[youtube|\]/",'',$matches[0]);
		 
		 $str = html_entity_decode($str);
		 
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
	 * oEmbed a youtube video from parsed shortcode.
	 * 
	 * @return string.
	 */
	private function oembed_youtube($matches)
	{	 
		 if($matches[0])
		 {
			 
			 $url = preg_replace("/\[youtube|\s|\]/","",$matches[0]);
			 
			 $url = html_entity_decode($url);
			 
			 $endpoint = 'https://www.youtube.com/oembed?url=' . $url . '&format=json';
			 
			 $response = $this->curl_get($endpoint);
			 
			 $obj = json_decode($response);

			 if(is_object($obj))
			 {
				 	if(isset($obj->html))
				 	{
					 return $obj->html;
				 	}
				 
				 } elseif(ee()->config->item('debug') == 1 && ee()->session->userdata('group_id') == 1) {
			 	
			 		return $response;
		 		}
		 }
		 
		 return '';
	 
	 }
	
	//-----------------------------------------------------------------------------
	
	/**
	 * Embed a Vimeo video from parsed shortcode.
	 * 
	 * @return string.
	 */
	private function embed_vimeo($matches)
	{	 
		 $html = '';
		 
		 if(isset($matches[0]))
		 {
		 
		 $endpoint = 'http://vimeo.com/api/oembed.json';
		 
		 // Grab the video url from $matches[0].
		 
		 $url = trim(preg_replace("/\[vimeo|\]/","",$matches[0]));
		 
		 $url = htmlspecialchars_decode($url);
		 
		 $url = str_replace('?','&',$url);
		 
		 $endpoint.= '?url=' . $url;
		 
		 $response = $this->curl_get($endpoint);
			 
			 $obj = json_decode($response);

			 if(is_object($obj))
			 {
				 	if(isset($obj->html))
				 	{
					 return $obj->html;
				 	}
				 
				 } elseif(ee()->config->item('debug') == 1 && ee()->session->userdata('group_id') == 1) {
			 	
			 		return $response;
		 		}
		 		
		 	}
		 	
		 	return '';;
		
	 
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
				
				
				$response = $this->curl_get($url);
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
		
		/**
		 * Request via cURL.
		 * @return object
		 */
		private function curl_get($url) {
		
		$curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($curl);
    curl_close($curl);
    return $return;
    
    //-----------------------------------------------------------------------------
}
		

		// END
		

		// END
}
// END CLASS
/* End of file ext.gdtshortcodes.php */
/* Location: ./system/expressionengine/third_party/gdtshortcodes/ext.gdtshortcodes.php */