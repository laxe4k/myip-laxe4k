<?php
    $title = "MyIP - Laxe4k";
    // ----------------------
    // meta
    $meta = [
        "title" => "MyIP - Laxe4k | Connaitre son adresse IP publique",
        "description" => "MyIP - Laxe4k est un site qui vous permet de connaitre votre adresse IP publique, votre nom d'hôte, votre pays, votre ville, votre latitude, votre longitude, la date et l'heure.",
        "keywords" => "myip, my ip, get ip, get ip address, get ip address php, get ip address php script, get ip address php script free, get ip address php script, français, france",
        "author" => "Laxe4k",
        "robots" => "index, follow",
        "revisit-after" => "1 days",
        "language" => "fr",
        "viewport" => "width=device-width, initial-scale=1.0",
        "charset" => "UTF-8",
        "X-UA-Compatible" => "IE=edge"
    ];
    // ----------------------
    ?>
<!DOCTYPE html>
<html lang="<?php echo $meta['language']; ?>">
<base href="/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta property="og:title" content="<?php echo $meta['title']; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.myip.laxe4k.com/">
    <meta property="og:image" content="https://www.myip.laxe4k.com/assets/img/favicon.webp">
    <meta property="og:description" content="<?php echo $meta['description']; ?>">
    <meta property="og:site_name" content="<?php echo $title; ?>">
    <meta name="description" content="<?php echo $meta['description']; ?>">
    <meta name="keywords" content="<?php echo $meta['keywords']; ?>">
    <meta name="author" content="<?php echo $meta['author']; ?>">
    <meta name="robots" content="<?php echo $meta['robots']; ?>">
    <meta name="revisit-after" content="<?php echo $meta['revisit-after']; ?>">
    <meta name="language" content="<?php echo $meta['language']; ?>">
    <meta name="viewport" content="<?php echo $meta['viewport']; ?>">
    <meta name="charset" content="<?php echo $meta['charset']; ?>">
    <meta name="X-UA-Compatible" content="<?php echo $meta['X-UA-Compatible']; ?>"> 
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="icon" type="image/webp" href="assets/img/favicon.webp">
    <link rel="apple-touch-icon" type="image/webp" href="assets/img/apple-touch-icon.webp">
    <title><?php echo $meta['title']; ?></title>
</head>
<body>
    <?php
        // récupération de l'ip du visiteur
        $ip = $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : "N/A";
        // récupération du nom d'hôte du visiteur
        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']) ? gethostbyaddr($_SERVER['REMOTE_ADDR']) : "N/A";
        // récupération du pays du visiteur
        $country = file_get_contents("http://ip-api.com/json/".$ip);
        $country = json_decode($country);
        $country = $country->country ? $country->country : "N/A";
        // récupération de la ville du visiteur
        $city = file_get_contents("http://ip-api.com/json/".$ip);
        $city = json_decode($city);
        $city = $city->city ? $city->city : "N/A";
        // récupération de la latitude et de la longitude du visiteur
        $lat = file_get_contents("http://ip-api.com/json/".$ip);
        $lat = json_decode($lat);
        $lat = $lat->lat ? $lat->lat : "N/A";
        $lon = file_get_contents("http://ip-api.com/json/".$ip);
        $lon = json_decode($lon);
        $lon = $lon->lon ? $lon->lon : "N/A";
        // récupération de la date et de l'heure du visiteur grâce à l'ip du visiteur et actualisation toutes les secondes
        $date = file_get_contents("http://ip-api.com/json/".$ip);
        $date = json_decode($date);
        $date = $date->timezone;
        $heure = file_get_contents("http://ip-api.com/json/".$ip);
        $heure = json_decode($heure);
        $heure = $heure->timezone;
        echo "<script>
        function doubleDigit(n) {
            return (n < 10 ? '0' : '') + n;
        }
        function date() {
            var date = new Date();
            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            var date = doubleDigit(day) + '/' + doubleDigit(month) + '/' + year;
            return date;
        }
        function heure() {
            var date = new Date();
            var hour = date.getHours();
            var minute = date.getMinutes();
            var second = date.getSeconds();
            var heure = doubleDigit(hour) + ':' + doubleDigit(minute) + ':' + doubleDigit(second);
            return heure;
        }
        function actualisation() {
            document.getElementById('date').innerHTML = date();
            document.getElementById('heure').innerHTML = heure();
        }
        setInterval(actualisation, 1000);
        </script>";
    ?>
    <div class="center">
        <div class="content">
            <h2><img src="assets/img/favicon.webp" alt="MyIP - Laxe4k" width="50px" height="50px"><?php echo $title; ?></h2>
            <hr>
            <h3>Adresse IP</h3>
            <p><?php echo $ip; ?></p>
            <h3>Nom d'hôte</h3>
            <p><?php echo $hostname; ?></p>
            <h3>Pays</h3>
            <p><?php echo $country; ?></p>
            <h3>Ville</h3>
            <p><?php echo $city; ?></p>
            <h3>Latitude</h3>
            <p><?php echo $lat; ?></p>
            <h3>Longitude</h3>
            <p><?php echo $lon; ?></p>
            <h3>Date</h3>
            <p id="date">00/00/0000</p>
            <h3>Heure</h3>
            <p id="heure">00:00:00</p>
        </div>
    </div>
</body>
</html>