<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baseUrl = 'https://api.spotify.com/v1/';

// TODO: Get authenticated
$authUrl = 'https://accounts.spotify.com/api/token';

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$accessToken = null;

$ch = curl_init();

$curlConfig = [
    CURLOPT_URL => $authUrl,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . base64_encode($clientId.':'.$clientSecret),
    ],
    CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
    CURLINFO_HEADER_OUT => true,
];

curl_setopt_array($ch, $curlConfig);

$result = curl_exec($ch);
curl_close($ch);

$result = json_decode($result);

$accessToken = $result->access_token;

$ch = curl_init();

$curlConfig = [
    CURLOPT_URL => $baseUrl . 'users/arvid.b/playlists',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . $accessToken,
    ],
];

$result = curl_exec($ch);
$info = curl_getinfo($ch);

print_r($info);

curl_close($ch);

var_dump($result);
