<?php

$downloadString = get_query_var('arppagedata');

if (empty($downloadString)) {
    wp_redirect(home_url());
    exit;
}

$orders = wc_get_orders([
    '_accfarm_order_download_string' => $downloadString
]);

$order = array_pop($orders);

$currentUser = wp_get_current_user();
$orderUser = $order->get_user();

if ($orderUser->ID !== $currentUser->ID) {
    wp_redirect(home_url());
    exit;
}

$url = $order->get_meta('_accfarm_order_download_link');

if (empty($url)) {
    wp_redirect(home_url());
    exit;
}

$content = file_get_contents($url);
header ('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($url) . '.txt');
echo $content;