<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://127.0.0.1:8000', 'cookies' => true, 'http_errors' => false]);

// Fetch login page to get CSRF token
$res = $client->get('/login/tenant');
$body = (string) $res->getBody();
$token = null;
if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $body, $m)) {
    $token = $m[1];
}

if (! $token) {
    echo "No CSRF token found\n";
    exit(1);
}

$credentials = [
    'email' => 'test.tenant@local',
    'password' => 'tenantpass'
];

$res2 = $client->post('/tenant/login', [
    'headers' => [
        'X-CSRF-TOKEN' => $token,
        'Accept' => 'application/json',
    ],
    'json' => $credentials,
]);

echo "Login response status: " . $res2->getStatusCode() . "\n";
echo "Response body: " . (string)$res2->getBody() . "\n";
// If redirect, show location
if ($res2->getStatusCode() >= 300 && $res2->getStatusCode() < 400) {
    echo "Redirect to: " . $res2->getHeaderLine('Location') . "\n";
}

$res3 = $client->get('/dashboard');
echo "\nGET /dashboard status: " . $res3->getStatusCode() . "\n";
$html = (string)$res3->getBody();
echo "Dashboard HTML snippet:\n" . substr(trim(preg_replace('/\s+/', ' ', strip_tags($html))), 0, 500) . "\n";
