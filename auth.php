<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// TODO: Get authenticated
$authUrl = 'https://accounts.spotify.com/api/token';

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';

$ch = curl_init();

if (FALSE === $ch)
    throw new Exception('failed to initialize');

$curlConfig = [
    CURLOPT_URL => $authUrl,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . base64_encode($clientId.':'.$clientSecret),
        "'Content-Type':'application/x-www-form-urlencoded'",
    ],
    CURLOPT_POSTFIELDS => [ 'grant_type' => 'client_credentials' ],
];

curl_setopt_array($ch, $curlConfig);

$result = curl_exec($ch);

var_dump($result);

curl_close($ch);
