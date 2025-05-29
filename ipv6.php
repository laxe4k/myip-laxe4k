<?php
declare(strict_types=1);

// Fonction pour détecter l'IPv6
function getIPv6(): string {
    $keys = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR',
    ];

    $seenIPs = [];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                // Ignorer les IPv4 mappées
                if (preg_match('/^::ffff:(\d+\.\d+\.\d+\.\d+)$/', $ip)) {
                    continue;
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && !in_array($ip, $seenIPs, true)) {
                    $seenIPs[] = $ip;
                    return $ip;
                }
            }
        }
    }

    return 'N/A'; // Retourne "N/A" si aucune IPv6 valide n'est trouvée
}

$ip = getIPv6();

// Format de sortie
$format = $_GET['format'] ?? 'txt';

switch ($format) {
    case 'json':
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ip' => $ip]);
        break;

    case 'txt':
    default:
        header('Content-Type: text/plain; charset=utf-8');
        echo $ip;
        break;
}
