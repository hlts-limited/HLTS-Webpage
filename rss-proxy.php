<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache');

$url = 'https://techcrunch.com/feed/';

$ctx = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'timeout' => 10,
        'header'  => "User-Agent: Mozilla/5.0 (compatible; HLTSBot/1.0)\r\n"
    ]
]);

$xml = @file_get_contents($url, false, $ctx);

if ($xml === false) {
    http_response_code(502);
    echo json_encode(['error' => 'fetch_failed']);
    exit;
}

echo json_encode(['contents' => $xml]);
