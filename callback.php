<?php
// Log masuk
file_put_contents("duitku-callback.log", date("Y-m-d H:i:s") . " - " . json_encode($_POST) . PHP_EOL, FILE_APPEND);

// Data dari Duitku
$merchantOrderId = $_POST['merchantOrderId'] ?? '';
$resultCode = $_POST['resultCode'] ?? '';
$reference = $_POST['reference'] ?? '';
$amount = $_POST['amount'] ?? 0;

// Cek jika pembayaran sukses
if ($resultCode === "00") {
    // Update status di Supabase
    $data = [
        "status" => "selesai",
        "reference" => $reference
    ];

    $payload = json_encode($data);

    $ch = curl_init("https://nzhhrovqnbayfbkaalss.supabase.co/rest/v1/pesanan?merchantOrderId=eq.$merchantOrderId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "apikey: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im56aGhyb3ZxbmJheWZia2FhbHNzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDY0NTIyNDksImV4cCI6MjA2MjAyODI0OX0.koIvptV1BYsbcTqNdT7mZwK-BENpuijlidZax8XhTxs",
        "Authorization: "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im56aGhyb3ZxbmJheWZia2FhbHNzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDY0NTIyNDksImV4cCI6MjA2MjAyODI0OX0.koIvptV1BYsbcTqNdT7mZwK-BENpuijlidZax8XhTxs"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    // Log response
    file_put_contents("duitku-callback.log", "SUPABASE RESPONSE: $response" . PHP_EOL, FILE_APPEND);
}

echo 'OK';