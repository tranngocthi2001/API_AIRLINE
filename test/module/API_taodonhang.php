<?php
$apiKey = 'vKnqLeZVR6k1AOHQzwrPP4cAxWVRTJLR';
$apiSecret = 'ukdzzbePHfevXXmc';
$accessTokenEndpoint = 'https://test.api.amadeus.com/v1/security/oauth2/token';
$jsonResourceEndpoint = 'https://api.example.com/data';

// Xác thực và nhận access token
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $accessTokenEndpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=" . $apiKey . "&client_secret=" . $apiSecret,
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/x-www-form-urlencoded"
    ),
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "CURL Error #:" . $err;
} else {
    $accessToken = json_decode($response)->access_token;

    // Gửi yêu cầu API để nhận dữ liệu JSON với access token
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $jsonResourceEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $accessToken,
            "Accept: application/json"
        ),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "CURL Error #:" . $err;
    } else {
        // Xử lý và hiển thị dữ liệu JSON
        $jsonData = json_decode($response, true);
        var_dump($jsonData); // In ra dữ liệu JSON để xem cấu trúc
        // Tiếp tục xử lý dữ liệu JSON theo cách bạn muốn
    }
}
?>
