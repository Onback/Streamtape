<?php
$filelink = 'https://strtape.cloud/e/'. $_GET['v'] . '';
if (strpos($filelink, "strtape.cloud") !== false) {
    $useragent = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36";
    $head = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
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
        'Upgrade-Insecure-Requests: 1'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $filelink);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    $h = curl_exec($ch);
    curl_close($ch);

    // Extract video and subtitle links (use dummy for the example)
    $videoSources = [
        ['file' => 'https://example.com/video_720p.mp4', 'label' => '720p'],
        ['file' => 'https://example.com/video_1080p.mp4', 'label' => '1080p'],
        ['file' => 'https://example.com/video_480p.mp4', 'label' => '480p']
    ];

    $subtitles = [
        ['file' => 'https://example.com/subtitles_en.vtt', 'label' => 'English'],
        ['file' => 'https://example.com/subtitles_es.vtt', 'label' => 'Spanish']
    ];

    // Store video source and subtitle as a JSON object to pass to the client-side
    $videoData = json_encode($videoSources);
    $subtitleData = json_encode($subtitles);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <title>StreamTape - Watch Your Videos</title>
    <meta name="robots" content="noindex" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <style>
        body, html {
            background-color: #fff;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
        #uplay_player {
            position: relative;
            width: 100% !important;
            height: 500px !important;
            border: none;
            overflow: hidden;
        }
        #videoForm {
            text-align: center;
            margin-top: 20px;
        }
        #videoForm input, #videoForm button {
            padding: 10px;
            font-size: 16px;
            margin: 5px;
        }
    </style>
    <script src="https://cdn.rawgit.com/ufilestorage/a/master/jquery-2.2.3.min.js"></script>
    <script src="https://ssl.p.jwpcdn.com/player/v/8.13.0/jwplayer.js"></script>
    <script>jwplayer.key = "64HPbvSQorQcd52B8XFuhMtEoitbvY/EXJmMBfKcXZQU2Rnn";</script>
</head>
<body>

<!-- Form to submit video ID -->
<div id="videoForm">
    <h1>Watch Your Video</h1>
    <form action="" method="GET">
        <input type="text" name="v" placeholder="Enter Video ID" required>
        <button type="submit">Watch Now</button>
    </form>
</div>

<div id="uplay_player"></div>

<script type="text/javascript">
    // Get the video and subtitle data from the PHP backend
    var videoSources = <?=$videoData?>;
    var subtitleTracks = <?=$subtitleData?>;

    var videoPlayer = jwplayer("uplay_player");
    videoPlayer.setup({
        sources: videoSources,
        width: "100%",
        height: "100%",
        controls: true,
        displaytitle: false,
        autostart: false,
        image: '<?php echo $cover; ?>',
        tracks: subtitleTracks,
        captions: {
            color: "#FFFF00",
            fontSize: 14,
            edgeStyle: "uniform",
            backgroundOpacity: 0,
        },
        logo: {
            file: "",
            position: "top-left",
            link: ""
        }
    });

    // Resume playback from last position
    videoPlayer.on("ready", function() {
        var lastPosition = localStorage.getItem("videoPosition");
        if (lastPosition) {
            videoPlayer.seek(lastPosition);
        }
    });

    // Save current playback position when the video is paused or stopped
    videoPlayer.on("time", function(event) {
        localStorage.setItem("videoPosition", event.position);
    });

    // Custom download button for subtitles
    videoPlayer.addButton(
        "https://raw.githubusercontent.com/ufilestorage/img/master/download.png",
        "Download Subtitles",
        function() {
            window.open(subtitleTracks[0].file, "_blank").blur();
        },
        "download_subtitles"
    );

    // Speed control buttons
    videoPlayer.addButton(
        "https://raw.githubusercontent.com/ufilestorage/img/master/speed_up.png",
        "Speed Up",
        function() {
            var currentRate = videoPlayer.getPlaybackRate();
            videoPlayer.setPlaybackRate(currentRate + 0.25);
        },
        "speed_up"
    );

    videoPlayer.addButton(
        "https://raw.githubusercontent.com/ufilestorage/img/master/slow_down.png",
        "Slow Down",
        function() {
            var currentRate = videoPlayer.getPlaybackRate();
            videoPlayer.setPlaybackRate(currentRate - 0.25);
        },
        "slow_down"
    );
</script>

</body>
</html>
