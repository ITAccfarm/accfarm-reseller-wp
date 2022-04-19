<?php

$headers = getallheaders();
$request = json_decode(file_get_contents('php://input'), true);

\Src\Services\Log::instance()->log([
    'request' => $request,
    'headers' => $headers
], 'callback');

if (empty($headers['Signature'])) {
    return;
}

$testSignature = \Src\Services\Buy::instance()->singCallbackData($request);

if (!hash_equals($testSignature, $headers['Signature'])) {
    return;
}

if (empty($request['status']) || empty($request['number'])) {
    return;
}

$orders = wc_get_orders([
    '_accfarm_order_number' => $request['number'],
]);

$order = array_pop($orders);

$accfarmOrder = $order->get_meta('_accfarm_order');

if (!empty($accfarmOrder)) {
    $accfarmOrder = json_decode($accfarmOrder, true);
}

$accfarmOrder['status'] = $request['status'];

$order->update_meta_data('_accfarm_order', json_encode($accfarmOrder));

if (!empty($request['download_link'])) {
    $order->update_meta_data('_accfarm_order_download_link', $request['download_link']);
    $order->update_meta_data('_accfarm_order_download_string', \Src\Services\Buy::instance()->getDownloadString($request['download_link']));
}

$orderStatusToSet = get_option('accfarm_reseller_hooks_order_status_on_af_callback', 'completed');

if ($order->get_status() != $orderStatusToSet) {
    $order->set_status($orderStatusToSet);
}

$order->save();

do_action('accfarm_reseller_callback_received', $order);

return;