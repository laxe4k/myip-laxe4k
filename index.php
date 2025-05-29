<?php
$title = "MyIP - Laxe4k";
// ----------------------
// meta
$meta = [
    "title" => "MyIP - Laxe4k | Vérifiez votre adresse IP",
    "description" => "MyIP - Laxe4k vous permet de vérifier vos adresses IPv4 et IPv6 publiques ainsi que les données de géolocalisation.",
    "keywords" => "myip, mon ip, obtenir ip, adresse ip, ipv4, ipv6, géolocalisation",
    "author" => "Laxe4k",
    "robots" => "index, follow",
    "revisit-after" => "1 days",
    "language" => "fr",
    "viewport" => "width=device-width, initial-scale=1.0",
    "charset" => "UTF-8",
    "X-UA-Compatible" => "IE=edge"
];
// ----------------------

// Get IP candidates (handling proxies/CDN) - Server-side best effort
$ipCandidates = [];

// Cloudflare
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ipCandidates = array_merge($ipCandidates, explode(',', $_SERVER['HTTP_CF_CONNECTING_IP']));
}

// X-Forwarded-For
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipCandidates = array_merge($ipCandidates, explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
}

// X-Real-IP
if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
    $ipCandidates[] = $_SERVER['HTTP_X_REAL_IP'];
}

// Client-IP
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ipCandidates[] = $_SERVER['HTTP_CLIENT_IP'];
}

// REMOTE_ADDR (always last)
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $ipCandidates[] = $_SERVER['REMOTE_ADDR'];
}

// Initialize both IPv4 and IPv6 addresses from server-side detection
$ipv4_server = "N/A";
$ipv6_server = "N/A";

foreach ($ipCandidates as $candidate) {
    $candidate = trim($candidate);
    if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        if (preg_match('/^::ffff:(\d+\.\d+\.\d+\.\d+)$/', $candidate, $matches)) {
            if ($ipv4_server === "N/A") {
                $ipv4_server = $matches[1];
            }
        } else if ($ipv6_server === "N/A") {
            $ipv6_server = $candidate;
        }
    } 
    else if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        if ($ipv4_server === "N/A") {
            $ipv4_server = $candidate;
        }
    }
    if ($ipv4_server !== "N/A" && $ipv6_server !== "N/A") {
        break;
    }
}

// Geolocation will use one of the server-detected IPs
$lookupIP = ($ipv4_server !== "N/A") ? $ipv4_server : $ipv6_server;
if ($lookupIP === "N/A" && $ipv6_server !== "N/A") { // Fallback to IPv6 if IPv4 is N/A for lookup
    $lookupIP = $ipv6_server;
}

$ipGeoData = null;
if ($lookupIP !== "N/A") {
    $jsonGeo = @file_get_contents("http://ip-api.com/json/{$lookupIP}?fields=status,message,query,country,countryCode,regionName,city,zip,isp,as,timezone");
    if ($jsonGeo) {
        $ipGeoData = json_decode($jsonGeo);
    }
}

// Extract geolocation information
$city = ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->city)) ? $ipGeoData->city : "N/A";
$region = ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->regionName)) ? $ipGeoData->regionName : "N/A";
$postalCode = ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->zip)) ? $ipGeoData->zip : "N/A";
$countryDisplay = "N/A";
if ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->country)) {
    $countryDisplay = $ipGeoData->country;
    if (isset($ipGeoData->countryCode) && !empty($ipGeoData->countryCode)) {
        $countryDisplay .= " (" . $ipGeoData->countryCode . ")";
    }
}
$isp = ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->isp)) ? $ipGeoData->isp : "N/A";
$asn = ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->as)) ? $ipGeoData->as : "N/A";
$timezone = ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->timezone)) ? $ipGeoData->timezone : "N/A";

// If geolocation API returns an IP, and our server-side detection missed one, update it.
// This is a secondary check. Primary IP detection for display will be client-side.
if ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->query)) {
    if (filter_var($ipGeoData->query, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && $ipv6_server === "N/A") {
        $ipv6_server = $ipGeoData->query;
    } elseif (filter_var($ipGeoData->query, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $ipv4_server === "N/A") {
        $ipv4_server = $ipGeoData->query;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($meta['language']); ?>">
<head>
    <meta charset="<?php echo htmlspecialchars($meta['charset']); ?>">
    <meta name="viewport" content="<?php echo htmlspecialchars($meta['viewport']); ?>">
    <meta http-equiv="X-UA-Compatible" content="<?php echo htmlspecialchars($meta['X-UA-Compatible']); ?>">
    <meta name="color-scheme" content="light dark">
    <!-- icon -->
    <link rel="icon" type="image/png" href="/assets/img/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg" />
    <link rel="shortcut icon" href="/assets/img/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyIP" />
    <link rel="manifest" href="/assets/img/site.webmanifest" />
    <?php foreach ($meta as $name => $content): ?>
    <?php if (!in_array($name, ['charset', 'viewport', 'X-UA-Compatible', 'title', 'language'])): ?>
    <meta name="<?php echo htmlspecialchars($name); ?>" content="<?php echo htmlspecialchars($content); ?>">
    <?php endif; ?>
    <?php endforeach; ?>
    
    <title><?php echo htmlspecialchars($meta['title']); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css?v=<?php echo time(); ?>"> 
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo htmlspecialchars($title); ?></h1>
        </header>

        <main>
            <section class="info-section">
                <h2>Vos Adresses IP</h2>
                <div class="info-item">
                    <span class="label">Mon IPv4 :</span>
                    <span class="value" id="ipv4-value"><?php echo htmlspecialchars($ipv4_server); ?><span class="loader" id="ipv4-loader"></span></span>
                </div>
                <div class="info-item">
                    <span class="label">Mon IPv6 :</span>
                    <span class="value" id="ipv6-value"><?php echo htmlspecialchars($ipv6_server); ?><span class="loader" id="ipv6-loader"></span></span>
                </div>
            </section>

            <section class="info-section">
                <h2>Détails de Géolocalisation (basés sur l'IP de connexion initiale)</h2>
                <div class="info-item">
                    <span class="label">Ville :</span>
                    <span class="value"><?php echo htmlspecialchars($city); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">État/Région :</span>
                    <span class="value"><?php echo htmlspecialchars($region); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Code Postal :</span>
                    <span class="value"><?php echo htmlspecialchars($postalCode); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Pays :</span>
                    <span class="value"><?php echo htmlspecialchars($countryDisplay); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">FAI (ISP) :</span>
                    <span class="value"><?php echo htmlspecialchars($isp); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">ASN :</span>
                    <span class="value"><?php echo htmlspecialchars($asn); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Fuseau Horaire :</span>
                    <span class="value"><?php echo htmlspecialchars($timezone); ?></span>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($meta['author']); ?>. Propulsé par ip-api.com et icanhazip.com.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ipv4ValueEl = document.getElementById('ipv4-value');
            const ipv6ValueEl = document.getElementById('ipv6-value');
            const ipv4LoaderEl = document.getElementById('ipv4-loader');
            const ipv6LoaderEl = document.getElementById('ipv6-loader');

            const fetchIp = async (url, element, loaderElement) => {
                try {
                    // Timeout pour éviter les attentes indéfinies si le service n'est pas joignable sur ce type d'IP
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 secondes timeout

                    const response = await fetch(url, { signal: controller.signal });
                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const ip = await response.text();
                    element.textContent = ip.trim();
                } catch (error) {
                    console.warn(`Could not fetch IP from ${url}:`, error.message);
                    // Conserve la valeur N/A ou celle détectée par le serveur si la requête échoue
                    element.textContent = element.textContent.replace(/<span class="loader".*?><\/span>/, '').trim() || 'N/A'; 
                } finally {
                    if (loaderElement) {
                        loaderElement.style.display = 'none';
                    }
                }
            };

            // Initialiser les loaders
            if (ipv4ValueEl.textContent.trim() === 'N/A' || ipv4ValueEl.textContent.includes('<span class="loader"')) ipv4LoaderEl.style.display = 'inline-block'; else ipv4LoaderEl.style.display = 'none';
            if (ipv6ValueEl.textContent.trim() === 'N/A' || ipv6ValueEl.textContent.includes('<span class="loader"')) ipv6LoaderEl.style.display = 'inline-block'; else ipv6LoaderEl.style.display = 'none';
            
            // Si la valeur initiale est N/A, on la remplace par le loader pour l'affichage
            if (ipv4ValueEl.textContent.trim() === 'N/A') ipv4ValueEl.innerHTML = '<span class="loader" id="ipv4-loader"></span>';
            if (ipv6ValueEl.textContent.trim() === 'N/A') ipv6ValueEl.innerHTML = '<span class="loader" id="ipv6-loader"></span>';


            fetchIp('https://ipv4.icanhazip.com', ipv4ValueEl, ipv4LoaderEl);
            fetchIp('https://ipv6.icanhazip.com', ipv6ValueEl, ipv6LoaderEl);
        });
    </script>
</body>
</html>