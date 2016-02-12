#Good at Shortcodes

Render embedded media content via shortcodes saved in an ExpressionEngine channel entry.

- [Instagram](#instagram)
- [Twitter](#twitter)
- [YouTube](#youtube)
- [Vimeo](#vimeo)


##Installation

1. Download and unzip the package directory into /system/user/addons/ 
2. Install the extension in CP > Add-ons Manager
3. Once installed, click the settings link for the extension and select the shortcodes you want to enable.
4. If you'll be embedding tweets, follow the instructions for adding Twitter Application settings. 

##Shortcodes

###Instagram

Embed an Instagram post .

`[ig https://www.instagram.com/p/8BDpw6tkQr]`

Customize the embedded post by appending a query string to its URL.

`[ig https://www.instagram.com/p/8BDpw6tkQr?maxwidth=420&hidecaption=1&omitscript=false]`

| Parameter | Description |Default|Options
| --- | --- | --- | --- |
| hidecaption | Render the post without a caption. | false | `false`, `true`
| maxwidth | Set the width of rendered post. | 320 | 
| omitscript | If set to true, the embed code does not include the Instgram script tag. | false | `false`, `true`

####Notes
- More parameters and info at the [Instagram Embedding API page](https://www.instagram.com/developer/embedding/#oembed)
- Place each shortcode on a new line to render multiple posts.


###Twitter

Embed a tweet in the rendered template for a channel entry field using the "link to Tweet" URL.

`[tweet https://twitter.com/goodnewsfinland/status/697118569633599488]`

Customize the embedded tweet by appending query string to its URL.

`[tweet https://twitter.com/goodnewsfinland/status/697118569633599488?maxwidth=420&hide_media=1&lang=fr]`

| Parameter | Description |Default|Options
| --- | --- | --- | --- |
| align | Specifies whether the embedded Tweet should be floated left, right, or center in the page relative to the parent element. | none | `left`, `right`, `center`, `none`
| hide_media | When set to true, t or 1 links in a Tweet are not expanded to photo, video, or link previews | false | `1`,`0`
| lang | Request returned HTML and a rendered Tweet in the specified Twitter language supported by embedded Tweets. | |
| maxwidth | maximum width of the rendered tweet  | 550 |  a number from `220` to `550` 

####Notes
- More parameters and info at the [Twitter statuses/oembed API page](https://dev.twitter.com/rest/reference/get/statuses/oembed)
- Place each shortcode on a new line to render multiple tweets.


### YouTube

Embed a YouTube video in the rendered template for a channel entry field.

`[youtube https://www.youtube.com/watch?v=1DXHE4kt3Fw]`

Customize the player by appending parameters to the YouTube URL query string.

`[youtube https://www.youtube.com/watch?v=1DXHE4kt3Fw&w=420&h=315&rel=0&class=my-class-name&controls=0]`

| Parameter | Description |Default|Options
| --- | --- | --- | --- |
| controls | Show player controls? | 1 | `1`, `0`
| class | CSS class name to assign to iframe | | 
| id | CSS id to assign to iframe |  | 
| end | time in seconds to end video|  | 
| h | iframe height attribute value |  | 
| rel | Show related videos at end of video? | 1 | `1`, `0`
| start | time in seconds to begin video|  | 
| w | iframe width attribute value |  | 

####Notes
- Be certain to use the full URL, not a shortened youtu.be style URL
- Place each shortcode on a new line to render multiple videos.


### Vimeo

Embed a Vimeo video in the rendered template for a channel entry field.

`[vimeo https://vimeo.com/154727398]`

Customize the player by appending parameters to the Vimeo URL query string.

`[vimeo https://vimeo.com/154727398?width=800&color=fc0&title=false&byline=false]`

| Parameter | Description |Default|Options
| --- | --- | --- | --- |
| byline | Show byline? | `true` | `true`, `false`
| color | Player control color | | 
| height | Player height | | 
| width | Player width |  | 


####Notes
- Place each shortcode on a new line to render multiple videos.
- More info and additional player parameters at [Vimeo API page](https://developer.vimeo.com/apis/oembed)

