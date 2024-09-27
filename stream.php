<?php
$filelink='https://strtape.cloud/e/'.$_GET['v'].'';
if (strpos($filelink,"strtape.cloud") !== false)
{
  $useragent = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
 'Accept-Language: en-US,en;q=0.5',
 'Accept-Encoding: deflate',
 'Connection: keep-alive',
 'Cache-Control: max-age=0',
 'Dnt: 1',
 'Authority: strtape.tech',
 'Sec-Fetch-Site: none',
 'Sec-Fetch-Mode: navigate',
 'Sec-Fetch-User: ?1',
 'Sec-Fetch-Dest: document',
 'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $h=str_replace("\\","",$h);
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];

  if (preg_match_all("/\(\'\w+\'\)\.innerHTML\s*\=\s*(.*?)\;/",$h,$m)) {
      $e1=$m[1][count($m[1])-1];
      $e1=str_replace("'",'"',$e1);
      $d=explode("+",$e1);
      $out="";
      for ($k=0;$k<count($d);$k++) {
          $s=trim($d[$k]);
          preg_match("/\(?\"([^\"]+)\"\)?(\.substring\((\d+)\))?(\.substring\((\d+)\))?/",$s,$p);
          if (isset($p[3]) && isset($p[5]))
              $out .=substr(substr($p[1],$p[3]),$p[5]);
          elseif (isset($p[3]))
              $out .=substr($p[1],$p[3]);
          else
              $out .=$p[1];
      }
      $link=$out;
      $link .= "&stream=1";
      if ($link[0]=="/") $link="https:".$link;
  }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8"/>
<title>StreamTape - Video Streaming</title>
<meta name="robots" content="noindex" />
<META NAME="GOOGLEBOT" CONTENT="NOINDEX" />
<meta name="referrer" content="never">
<meta name="robots" content="nofollow, noindex">
<meta http-equiv="X-UA-Compatible" content="IE=11" />
<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" id="viewport" name="viewport">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
<link href="https://cdn.rawgit.com/ufilestorage/a/master/skins/jw-logo-bar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.rawgit.com/ufilestorage/a/master/jquery-2.2.3.min.js"></script>
<script src="https://ssl.p.jwpcdn.com/player/v/8.13.0/jwplayer.js"></script>
<script>jwplayer.key="64HPbvSQorQcd52B8XFuhMtEoitbvY/EXJmMBfKcXZQU2Rnn";</script>
<style type="text/css">
    body, html {
        background-color: #ffffff; /* White background */
        margin: 0;
        padding: 0;
        font-family: 'Roboto', sans-serif; /* Using Roboto font */
    }
    h1 {
        text-align: center;
        color: #333; /* Dark color for the title */
        margin: 20px 0; /* Margin for spacing */
    }
    #uplay_player {
        position: absolute;
        width: 100% !important;
        height: 100% !important;
        border: none;
        overflow: hidden;
        top: 60px; /* Adjusted for title */
    }
</style>
</head>
<body>
<h1>Stream Your Video</h1> <!-- Added title -->
<div id="uplay_player"></div>
<script type="text/javascript">
var videoPlayer = jwplayer("uplay_player");
videoPlayer.setup({
    sources: [{'file':'<?=$link?>','type':'video/mp4'}],
    width: "100%",
    height: "100%",
    controls: true,
    displaytitle: false,
    flashplayer: "https://p.jwpcdn.com/player/v/7.12.8/jwplayer.flash.swf",
    fullscreen: "true",
    primary: "html5",
    autostart: false,
    image:'<?php echo $cover; ?>',
    advertising: {
        client: "vast",
        tag: ""
    },
    tracks: [{
        file: "<?php echo $sub; ?>",
        label: "Subs",
        kind:  "captions",
        default: "true",
    }],
    captions: {
        color: "#FFFF00",
        fontSize: 14,
        edgeStyle: "uniform",
        backgroundOpacity: 0,
    },
    logo: {
        file: "",
        logoBar: "",
        position: "top-left",
        link: ""
    },
    aboutlink: "",
    abouttext: "",
    sharing: {
        //code: encodeURI("<iframe width=\"640\" height=\"380\" src=\"empty-url\" frameborder=\"0\" scrolling=\"no\"></iframe>"),
    },
});
videoPlayer.on("ready",function() {
    jwLogoBar.addLogo(videoPlayer);
});
videoPlayer.addButton(
    "https://raw.githubusercontent.com/ufilestorage/img/master/download.png",
    "Download Movie", 
    function(){
        window.open(videoPlayer.getPlaylistItem()["file"]+"","_blank").blur();
    }, "download"
);
</script>
</body>
</html>

