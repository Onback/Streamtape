<?php
if (isset($_GET['v'])) {
    $filelink = 'https://strtape.cloud/e/'.$_GET['v'];
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
        $info = curl_getinfo($ch);
        curl_close($ch);
        $h = str_replace("\\", "", $h);
        if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
            $srt = "https:" . $s[1];

        if (preg_match_all("/\(\'\w+\'\)\.innerHTML\s*\=\s*(.*?)\;/", $h, $m)) {
            $e1 = $m[1][count($m[1]) - 1];
            $e1 = str_replace("'", '"', $e1);
            $d = explode("+", $e1);
            $out = "";
            for ($k = 0; $k < count($d); $k++) {
                $s = trim($d[$k]);
                preg_match("/\(?\"([^\"]+)\"\)?(\.substring\((\d+)\))?(\.substring\((\d+)\))?/", $s, $p);
                if (isset($p[3]) && isset($p[5]))
                    $out .= substr(substr($p[1], $p[3]), $p[5]);
                elseif (isset($p[3]))
                    $out .= substr($p[1], $p[3]);
                else
                    $out .= $p[1];
            }
            $link = $out;
            $link .= "&stream=1";
            if ($link[0] == "/") $link = "https:" . $link;
        }
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <title>StreamTape Player</title>
    <meta name="robots" content="noindex" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    
    <!-- Styling -->
    <style type="text/css">
        body, html {
            background-color: #fff;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 40px;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 18px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        #uplay_player {
            width: 100%;
            height: 400px;
            border: none;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Title -->
        <h1>StreamTape Video Player</h1>

        <!-- Video ID Submission Form -->
        <form method="GET" action="">
            <input type="text" name="v" placeholder="Enter Video ID" required />
            <input type="submit" value="Play Video" />
        </form>

        <!-- Video Player -->
        <div id="uplay_player"></div>
    </div>

    <!-- JWPlayer Script -->
    <script src="https://ssl.p.jwpcdn.com/player/v/8.13.0/jwplayer.js"></script>
    <script>
        jwplayer.key = "64HPbvSQorQcd52B8XFuhMtEoitbvY/EXJmMBfKcXZQU2Rnn";

        var videoPlayer = jwplayer("uplay_player");
        videoPlayer.setup({
            sources: [{
                'file': '<?=$link?>',
                'type': 'video/mp4'
            }],
            width: "100%",
            height: "100%",
            controls: true,
            displaytitle: false,
            primary: "html5",
            autostart: false,
            image: '',
            tracks: [{
                file: "<?php echo $srt; ?>",
                label: "Subs",
                kind: "captions",
                default: "true",
            }],
            captions: {
                color: "#FFFF00",
                fontSize: 14,
                edgeStyle: "uniform",
                backgroundOpacity: 0,
            }
        });

        videoPlayer.addButton(
            "https://raw.githubusercontent.com/ufilestorage/img/master/download.png",
            "Download Movie",
            function () {
                window.open(videoPlayer.getPlaylistItem()["file"], "_blank").blur();
            },
            "download"
        );
    </script>
</body>
</html>
