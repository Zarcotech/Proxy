<?php
$url = isset($_GET['url']) ? $_GET['url'] : '';
if (!$url) {
    die('Error: No URL specified.');
}

$parsed_url = parse_url($url);

if (!isset($parsed_url['scheme']) || !in_array($parsed_url['scheme'], ['http', 'https'])) {
    die('Error: Invalid URL.');
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = getallheaders();
$proxy_headers = [];
foreach ($headers as $key => $value) {
    if ($key != 'Host' && $key != 'Cookie') {
        $proxy_headers[] = "$key: $value";
    }
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $proxy_headers);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

$response = curl_exec($ch);
if (curl_errno($ch)) {
    die('Error: ' . curl_error($ch));
}

$response_headers = curl_getinfo($ch);
http_response_code($response_headers['http_code']);
header("Content-Type: " . $response_headers['content_type']);

echo $response;

curl_close($ch);
?>
