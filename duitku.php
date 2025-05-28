<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$merchantCode = "DS23260";
$apiKey = "2ce9c20d5849c6cb7fed1512025c91c8";
$duitkuUrl = "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry";

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['phoneNumber']) || !isset($input['paymentAmount'])) {
    echo json_encode(['error' => 'Input tidak lengkap']);
    exit;
}

$signature = md5($merchantCode . $input['phoneNumber'] . $input['paymentAmount'] . $apiKey);

$body = [
    "merchantCode" => $merchantCode,
    "paymentAmount" => $input["paymentAmount"],
    "paymentMethod" => $input["paymentMethod"],
    "merchantOrderId" => $input["merchantOrderId"],
    "productDetails" => $input["productDetails"],
    "customerVaName" => $input["customerVaName"],
    "email" => $input["email"],
    "phoneNumber" => $input["phoneNumber"],
    "callbackUrl" => "https://kedainoka.online/callback.php",
    "returnUrl" => "https://kedainoka.online/thanks.html",
    "signature" => $signature
];

$ch = curl_init($duitkuUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    file_put_contents("duitku-log.txt", "ERROR: " . curl_error($ch));
    echo json_encode(['error' => curl_error($ch)]);
} else {
    file_put_contents("duitku-log.txt", "HTTP {$httpCode}\n" . $response);
    if ($httpCode >= 200 && $httpCode < 300) {
        echo $response;
    } else {
        echo json_encode([
            'error' => 'Gagal dari Duitku',
            'httpCode' => $httpCode,
            'response' => $response
        ]);
    }
}
curl_close($ch);
?>
