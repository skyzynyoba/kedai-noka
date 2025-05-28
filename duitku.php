<?php
// Konfigurasi
$merchantCode = "DS23260"; // GANTI
$apiKey = "2ce9c20d5849c6cb7fed1512025c91c8"; // GANTI
$duitkuUrl = "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry";

// Ambil data dari frontend (JSON)
$input = json_decode(file_get_contents('php://input'), true);

// Hitung signature
$signature = md5($merchantCode . $input['phoneNumber'] . $input['paymentAmount'] . $apiKey);

// Tambahkan ke data body
$input['merchantCode'] = $merchantCode;
$input['signature'] = $signature;
$input['callbackUrl'] = "https://kedainoka.online/callback.php";
$input['returnUrl'] = "https://kedainoka.online/thanks.html";

// Kirim ke Duitku
$ch = curl_init($duitkuUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    echo $response;
}
curl_close($ch);
?>