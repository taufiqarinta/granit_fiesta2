<?php
// wa_api.php - API endpoint untuk WhatsApp
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Ganti dengan base URL service WA Anda (nodejs service)
define('WA_SERVICE_BASE', 'https://report.kobin.co.id:3002');
define('WA_PROXY_DEBUG', true);

function callWaApi($path, $method = 'GET', $payload = null, $timeout = 15) {
    $url = rtrim(WA_SERVICE_BASE, '/') . $path;

    $ch = curl_init($url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_HTTPHEADER => ['X-Requested-With: XMLHttpRequest']
    ];

    if ($method === 'POST') {
        $options[CURLOPT_POST] = true;
        if ($payload !== null) {
            $options[CURLOPT_POSTFIELDS] = json_encode($payload);
            $options[CURLOPT_HTTPHEADER] = [
                'Content-Type: application/json',
                'X-Requested-With: XMLHttpRequest'
            ];
        }
    }

    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || !empty($curlError)) {
        return [
            'ok' => false,
            'http_code' => 0,
            'raw' => null,
            'json' => null,
            'error' => $curlError ?: 'Tidak dapat terhubung ke WA service',
            'url' => $url
        ];
    }

    $decoded = json_decode($response, true);
    return [
        'ok' => ($httpCode >= 200 && $httpCode < 300),
        'http_code' => $httpCode,
        'raw' => $response,
        'json' => $decoded,
        'error' => null,
        'url' => $url
    ];
}

// Cek request
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'send') {
    $number = $_POST['number'] ?? '';
    $message = $_POST['message'] ?? '';
    $destinationType = $_POST['destination_type'] ?? 'personal';
    
    if (empty($number) || empty($message)) {
        echo json_encode(['status' => false, 'error' => 'number dan message wajib diisi']);
        exit;
    }
    
    $result = callWaApi('/send', 'POST', [
        'number' => $number,
        'message' => $message,
        'destination_type' => $destinationType
    ], 30);

    if (!$result['ok']) {
        http_response_code(503);
        echo json_encode([
            'status' => false,
            'error' => $result['error'] ?: 'WA service tidak merespon',
            'http_code' => $result['http_code'],
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
        exit;
    }

    if (is_array($result['json'])) {
        echo json_encode($result['json']);
    } else {
        echo json_encode([
            'status' => false,
            'error' => 'Response WA service tidak valid',
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
    }
    exit;
}

if ($action === 'groups') {
    $result = callWaApi('/groups', 'GET', null, 15);

    if (!$result['ok']) {
        http_response_code(503);
        echo json_encode([
            'status' => false,
            'message' => 'Gagal mengambil daftar grup dari WA service',
            'error' => $result['error'] ?: 'WA service tidak merespon',
            'http_code' => $result['http_code'],
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
        exit;
    }

    if (is_array($result['json'])) {
        echo json_encode($result['json']);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Response groups tidak valid',
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
    }
    exit;
}

if ($action === 'lid-map') {
    $result = callWaApi('/lid-map', 'GET', null, 10);

    if (!$result['ok']) {
        http_response_code(503);
        echo json_encode([
            'status' => false,
            'message' => 'Gagal mengambil lid map dari WA service',
            'error' => $result['error'] ?: 'WA service tidak merespon',
            'http_code' => $result['http_code'],
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
        exit;
    }

    if (is_array($result['json'])) {
        echo json_encode($result['json']);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Response lid-map tidak valid',
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
    }
    exit;
}

if ($action === 'status') {
    $result = callWaApi('/status', 'GET', null, 10);

    if (!$result['ok']) {
        http_response_code(503);
        echo json_encode([
            'status' => false,
            'ready' => false,
            'error' => $result['error'] ?: 'WA service tidak tersedia',
            'http_code' => $result['http_code'],
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
        exit;
    }

    if (is_array($result['json'])) {
        echo json_encode($result['json']);
    } else {
        echo json_encode([
            'status' => false,
            'ready' => false,
            'error' => 'Response status tidak valid',
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
    }
    exit;
}

if ($action === 'qr') {
    $result = callWaApi('/qr', 'GET', null, 10);

    if (!$result['ok']) {
        http_response_code(503);
        echo json_encode([
            'status' => false,
            'error' => $result['error'] ?: 'Endpoint QR tidak tersedia',
            'http_code' => $result['http_code'],
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
        exit;
    }

    if (is_array($result['json'])) {
        echo json_encode($result['json']);
    } else {
        echo json_encode([
            'status' => false,
            'error' => 'Response QR tidak valid',
            'debug' => WA_PROXY_DEBUG ? [
                'url' => $result['url'] ?? null,
                'raw' => $result['raw'] ?? null
            ] : null
        ]);
    }
    exit;
}

echo json_encode(['status' => false, 'error' => 'Action tidak dikenal']);
