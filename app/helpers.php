<?php

if (!function_exists('base64_url_encode')) {
    function base64_url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('base64_url_decode')) {
    function base64_url_decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

if (!function_exists('generateLinkKonfirmasi')) {
    function generateLinkKonfirmasi($lokasi)
    {
        $plain = strtoupper($lokasi);
        $b64 = base64_url_encode($plain);
        $hmac = hash_hmac('sha256', $b64, config('app.key'));
        return route('konfirmasi-kehadiran.index', ['lokasi' => "$b64.$hmac"]);
    }
}
