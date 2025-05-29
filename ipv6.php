<?php
// Forcer le format JSON si demandé
if ($_GET['format'] ?? '' === 'json') {
    header('Content-Type: application/json');
}

// Fonction pour récupérer uniquement une IPv6
function getIPv6() {
    $keys = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR'
    ];

    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    return $ip;
                }
            }
        }
    }

    return '::';
}

$ip = getIPv6();

// Réponse
if ($_GET['format'] ?? '' === 'json') {
    echo json_encode(['ip' => $ip]);
} else {
    echo $ip;
}
