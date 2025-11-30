<?php
require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// 1) Read secret from .env
$env = file_get_contents(__DIR__ . '/.env');
$secret = null;
if (preg_match('/^app\.JWT_SECRET\s*=\s*(.*)$/m', $env, $m)) {
    $s = trim($m[1]);
    // remove surrounding quotes if present
    if ((substr($s,0,1) === '"' && substr($s,-1) === '"') || (substr($s,0,1) === "'" && substr($s,-1) === "'")) {
        $s = substr($s,1,-1);
    }
    $secret = $s;
}
if (!$secret) {
    echo "No JWT secret found\n";
    exit(1);
}

// 2) Get user from DB (id=1)
$mysqli = new mysqli('localhost','root','','ellyso87_devostorange');
if ($mysqli->connect_errno) { echo "DB CONNECT ERR: " . $mysqli->connect_error . "\n"; exit(1); }
$res = $mysqli->query('SELECT id,email,nome,tipo FROM users WHERE id=1 LIMIT 1');
if (!$res) { echo "DB QUERY ERR: " . $mysqli->error . "\n"; exit(1); }
$user = $res->fetch_assoc();
if (!$user) { echo "No user with id=1\n"; exit(1); }

// 3) Build token
$issuedAt = time();
$expire = $issuedAt + (60*60*24);
$payload = [
    'iat' => $issuedAt,
    'exp' => $expire,
    'sub' => $user['id'],
    'email' => $user['email'],
    'tipo' => $user['tipo'],
];
$token = JWT::encode($payload, $secret, 'HS256');

echo "GENERATED_TOKEN:" . $token . "\n\n";

// 4) Call /api/users/me
$meUrl = 'http://localhost:8080/api/users/me';
$ch = curl_init($meUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
$resp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($resp === false) {
    echo "CURL ERROR: " . curl_error($ch) . "\n";
} else {
    echo "GET /api/users/me HTTP/$http\n";
    echo $resp . "\n\n";
}
curl_close($ch);

// 5) Call POST /api/movimentacoes/entrada (produto_id 1, quantidade 1)
$postUrl = 'http://localhost:8080/api/movimentacoes/entrada';
$payloadJson = json_encode(['produto_id' => 1, 'quantidade' => 1]);
$ch = curl_init($postUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token", 'Content-Type: application/json']);
$resp2 = curl_exec($ch);
$http2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($resp2 === false) {
    echo "CURL ERROR POST: " . curl_error($ch) . "\n";
} else {
    echo "POST /api/movimentacoes/entrada HTTP/$http2\n";
    echo $resp2 . "\n";
}
curl_close($ch);

?>