#Good at Shortcodes

Render embedded content via shortcodes saved in an ExpressionEngine channel entry.


##Installation

1. Download and unzip the package directory in the /system/expressionengine/third_party/ directory. 
2. Install the extension in CP > Add-ons > Extensions
3. Once installed, click the settings link for the extension and select the shortcodes you want to enable.
4. If you'll be embedding tweets, follow the instructions for adding Twitter Application settings. 

##Shortcodes

### Twitter

Embed a tweet in the rendered template for a channel entry field.

`[tweet https://twitter.com/goodnewsfinland/status/697118569633599488]`

Place each shortcode on a new line to render multiple tweets.


### YouTube

Embed a YouTube video in the rendered template for a channel entry field.

`[youtube https://www.youtube.com/watch?v=1DXHE4kt3Fw]`

Renders: 

`<iframe src="//youtube.com/embed/1DXHE4kt3Fw" frameborder="0" allowfullscreen></iframe>`

Customize the player by appending parameters to the YouTube URL query string.

`[youtube https://www.youtube.com/watch?v=1DXHE4kt3Fw&w=420&h=315&rel=0&class=my-class-name&controls=0]`

Renders:

`<iframe src="//youtube.com/embed/1DXHE4kt3Fw?rel=0&amp;controls=0" width="420" height="315" class="my-class-name" frameborder="0" allowfullscreen></iframe>`

| Parameter | Description |Default|Options
| --- | --- | --- | --- |
| controls | Show player controls? | 1 | 1, 0
| class | CSS class name to assign to iframe | | 
| class | CSS id to assign to iframe |  | 
| end | time in seconds to end video|  | 
| h | iframe height attribute value |  | 
| rel | Show related videos at end of video? | 1 | 1, 0
| start | time in seconds to begin video|  | 
| w | iframe width attribute value |  | 

Place each shortcode on a new line to render multiple videos.

<iframe width="420" height="315" src="https://www.youtube.com/embed/1DXHE4kt3Fw?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>