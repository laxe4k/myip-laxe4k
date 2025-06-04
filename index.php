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
    "X-UA-Compatible" => "IE=edge",
    "url" => "https://myip.laxe4k.com",
    "theme-color" => "#1a73e8"
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
    } else if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
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
$countryCode = ($ipGeoData && $ipGeoData->status === 'success' && isset($ipGeoData->countryCode)) ? $ipGeoData->countryCode : null;
$hostname = 'N/A';
if ($lookupIP !== 'N/A') {
    $resolved = gethostbyaddr($lookupIP);
    if ($resolved && $resolved !== $lookupIP) {
        $hostname = $resolved;
    }
}

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
    <link rel="canonical" href="<?php echo htmlspecialchars($meta['url']); ?>" />
    <meta property="og:title" content="<?php echo htmlspecialchars($meta['title']); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($meta['description']); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo htmlspecialchars($meta['url']); ?>" />
    <meta property="og:image" content="/assets/img/web-app-manifest-512x512.png" />
    <meta name="theme-color" content="<?php echo htmlspecialchars($meta['theme-color']); ?>" />
    <?php foreach ($meta as $name => $content): ?>
        <?php if (!in_array($name, ['charset', 'viewport', 'X-UA-Compatible', 'title', 'language'])): ?>
            <meta name="<?php echo htmlspecialchars($name); ?>" content="<?php echo htmlspecialchars($content); ?>">
        <?php endif; ?>
    <?php endforeach; ?>

    <title><?php echo htmlspecialchars($meta['title']); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
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
                    <div class="value-action-group">
                        <span class="value" id="ipv4-value"><?php echo htmlspecialchars($ipv4_server); ?><span class="loader" id="ipv4-loader"></span></span>
                        <button class="copy-btn" data-clipboard-target="#ipv4-value" title="Copier IPv4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" />
                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="info-item">
                    <span class="label">Mon IPv6 :</span>
                    <div class="value-action-group">
                        <span class="value" id="ipv6-value"><?php echo htmlspecialchars($ipv6_server); ?><span class="loader" id="ipv6-loader"></span></span>
                        <button class="copy-btn" data-clipboard-target="#ipv6-value" title="Copier IPv6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" />
                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="info-item">
                    <span class="label">Nom d'hôte :</span>
                    <span class="value" id="hostname-value"><?php echo htmlspecialchars($hostname); ?></span>
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
                    <span class="value">
                        <?php echo htmlspecialchars($countryDisplay); ?>
                        <?php if ($countryCode): ?>
                            <img src="https://flagcdn.com/24x18/<?php echo strtolower($countryCode); ?>.png" alt="<?php echo htmlspecialchars($countryCode); ?>" width="24" height="18" style="margin-left:4px;vertical-align:text-bottom;">
                        <?php endif; ?>
                    </span>
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
                <div class="info-item">
                    <span class="label">Heure locale :</span>
                    <span class="value" id="localtime-value"></span>
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
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000);

                    const response = await fetch(url, { signal: controller.signal });
                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const ip = await response.text();
                    const currentContent = element.innerHTML;
                    const loaderRegex = /<span class="loader".*?><\/span>/i;
                    if (loaderRegex.test(currentContent)) {
                        element.innerHTML = ip.trim();
                    } else {
                        element.textContent = ip.trim();
                    }
                } catch (error) {
                    console.warn(`Could not fetch IP from ${url}:`, error.message);
                    element.textContent = "N/A"; // Affiche "N/A" si la requête échoue
                } finally {
                    if (loaderElement) {
                        loaderElement.style.display = 'none';
                    }
                }
            };

            // Initialiser les loaders
            if (ipv4ValueEl.textContent.trim() === 'N/A' || ipv4ValueEl.textContent.includes('<span class="loader"')) {
                ipv4ValueEl.innerHTML = '<span class="loader" id="ipv4-loader" style="display:inline-block;"></span>';
            } else {
                ipv4LoaderEl.style.display = 'none';
            }
            if (ipv6ValueEl.textContent.trim() === 'N/A' || ipv6ValueEl.textContent.includes('<span class="loader"')) {
                ipv6ValueEl.innerHTML = '<span class="loader" id="ipv6-loader" style="display:inline-block;"></span>';
            } else {
                ipv6LoaderEl.style.display = 'none';
            }


            fetchIp('https://ipv4.icanhazip.com', ipv4ValueEl, document.getElementById('ipv4-loader'));
            fetchIp('https://api.ipify.org', ipv4ValueEl);
            fetchIp('https://ipv6.icanhazip.com', ipv6ValueEl, document.getElementById('ipv6-loader'));
            fetchIp('https://api64.ipify.org', ipv6ValueEl);

            const timezoneValue = <?php echo json_encode($timezone); ?>;
            const updateLocalTime = () => {
                const el = document.getElementById('localtime-value');
                if (!el) return;
                if (timezoneValue && timezoneValue !== 'N/A') {
                    const now = new Date().toLocaleString('fr-FR', { timeZone: timezoneValue });
                    el.textContent = now;
                } else {
                    el.textContent = 'N/A';
                }
            };
            updateLocalTime();
            setInterval(updateLocalTime, 1000);

            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const targetId = button.dataset.clipboardTarget;
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        let textToCopy = targetElement.textContent || targetElement.innerText;
                        // Retirer le contenu du loader si présent
                        const loaderSpan = targetElement.querySelector('.loader');
                        if (loaderSpan) {
                            textToCopy = textToCopy.replace(loaderSpan.outerHTML, '').trim();
                        }

                        if (textToCopy && textToCopy !== 'N/A' && !textToCopy.includes('<span class="loader"')) {
                            navigator.clipboard.writeText(textToCopy).then(() => {
                                // Optionnel: afficher une notification de succès
                                const originalTitle = button.title;
                                button.title = 'Copié!';
                                setTimeout(() => {
                                    button.title = originalTitle;
                                }, 1500);
                            }).catch(err => {
                                console.error('Erreur lors de la copie: ', err);
                                // Optionnel: afficher une notification d'erreur
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>