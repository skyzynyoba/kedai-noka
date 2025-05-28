<?php
// Debugging aktif untuk menangkap error saat testing
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Konfigurasi
$merchantCode = "DS23260"; // GANTI
$apiKey = "2ce9c20d5849c6cb7fed1512025c91c8"; // GANTI
$duitkuUrl = "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry";

// Ambil data JSON dari frontend
$input = json_decode(file_get_contents('php://input'), true);

// Validasi input dasar
if (!$input || !isset($input['phoneNumber']) || !isset($input['paymentAmount'])) {
    echo json_encode(['error' => 'Input tidak lengkap']);
    exit;
}

// Hitung signature
$signature = md5($merchantCode . $input['phoneNumber'] . $input['paymentAmount'] . $apiKey);

// Bangun body permintaan ke Duitku
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

// Kirim request ke Duitku
$ch = curl_init($duitkuUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    // Validasi apakah berhasil
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