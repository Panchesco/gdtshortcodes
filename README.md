#Good at Shortcodes

Render embedded media content via shortcodes saved in an ExpressionEngine channel entry.

- [Twitter](#twitter)
- [YouTube](#youtube)


##Installation

1. Download and unzip the package directory in the /system/expressionengine/third_party/ directory. 
2. Install the extension in CP > Add-ons > Extensions
3. Once installed, click the settings link for the extension and select the shortcodes you want to enable.
4. If you'll be embedding tweets, follow the instructions for adding Twitter Application settings. 

##Shortcodes

###Twitter

Embed a tweet in the rendered template for a channel entry field using the "link to Tweet" URL.

`[tweet https://twitter.com/goodnewsfinland/status/697118569633599488]`

Renders:

`<blockquote class="twitter-tweet" data-width="420"><p lang="en" dir="ltr">What causes the aurora borealis?<a href="https://t.co/mGJY3eBuAW">https://t.co/mGJY3eBuAW</a><br>Gorgeous photos in this post!<br><br>Photo © Tor-Ivar Næss <a href="https://t.co/b1Zm0Sp5LV">pic.twitter.com/b1Zm0Sp5LV</a></p>&mdash; EarthSky (@earthskyscience) <a href="https://twitter.com/earthskyscience/status/696755554300071937">February 8, 2016</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>`

Customize the embedded tweet by appending query string to its URL.

`[tweet https://twitter.com/goodnewsfinland/status/697118569633599488?maxwidth=420&hide_media=1&lang=fr]`

Renders:

`<blockquote class="twitter-tweet" data-cards="hidden" data-width="420" data-lang="fr"><p lang="en" dir="ltr">MT <a href="https://twitter.com/DiscoverFinland">@DiscoverFinland</a>: a gorgeous time-lapsed video by <a href="https://twitter.com/henriluoma">@henriluoma</a> of the Finnish nature &amp; Aurora Borealis <a href="https://t.co/fvSUB2oc0a">https://t.co/fvSUB2oc0a</a></p>&mdash; GoodNewsfromFinland (@goodnewsfinland) <a href="https://twitter.com/goodnewsfinland/status/697118569633599488">9 Février 2016</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>`

| Parameter | Description |Default|Options
| --- | --- | --- | --- |
| align | Specifies whether the embedded Tweet should be floated left, right, or center in the page relative to the parent element. | none | left, right, center, none
| hide_media | When set to true, t or 1 links in a Tweet are not expanded to photo, video, or link previews | false
| lang | Request returned HTML and a rendered Tweet in the specified Twitter language supported by embedded Tweets. | |
| maxwidth | maximum width of the rendered tweet  | 550 | 220 min 550 max


####Notes
- More parameters and info at the [Twitter statuses/oembed API page](https://dev.twitter.com/rest/reference/get/statuses/oembed)
- Place each shortcode on a new line to render multiple tweets.

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

####Notes
- Be certain to use the full URL, not a shortened youtu.be style URL
- Place each shortcode on a new line to render multiple videos.

