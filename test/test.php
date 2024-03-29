<?php

$apiKey = 'vKnqLeZVR6k1AOHQzwrPP4cAxWVRTJLR';//Đây là key a đăng ký trên web thầy cho.
$apiSecret = 'ukdzzbePHfevXXmc';//Đây là key a đăng ký trên web thầy cho.
$accessTokenEndpoint = 'https://test.api.amadeus.com/v1/security/oauth2/token'; //accsess token có thể sẽ có hạn sử dụng e có thể lên postman lấy, k đc ibx a chỉ
$jsonResourceEndpoint = 'https://test.api.amadeus.com/v2/shopping/flight-offers'; //địa chỉ lấy dữ liệu json

// Gửi yêu cầu để lấy access token
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
    CURLOPT_SSL_VERIFYPEER => false, // Tắt xác thực chứng chỉ SSL
    CURLOPT_SSL_VERIFYHOST => false, // Tắt xác thực hostname
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

// Xử lý access token và gửi yêu cầu đến nguồn dữ liệu JSON
if ($err) {
    echo "CURL Error #:" . $err;
} else {
    $accessToken = json_decode($response)->access_token;

    // Gửi yêu cầu đến nguồn dữ liệu JSON với access token
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
        CURLOPT_SSL_VERIFYPEER => false, // Tắt xác thực chứng chỉ SSL
        CURLOPT_SSL_VERIFYHOST => false, // Tắt xác thực hostname
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    // Xử lý và hiển thị dữ liệu JSON trên trang web
    if ($err) {
        echo "CURL Error #:" . $err;
    } else {
        $jsonData = json_decode($response, true);
        echo "<pre>";
        print_r($jsonData);
        echo "</pre>";
    }
}

?>
//giờ chúng ta đã có chuỗi json xuất được lên trang web rồi việc của mình là tạo form để hiển thị tất cả dữ liệu lên cho thầy,
//còn việc có thể thêm, xóa, sửa dữ liệu hay không thì để a nghiên cứu thử xem sao cho đủ yêu cầu của thầy. A thầy có yc có xây dựng web site bán vé
//có thể là mình cần làm thêm chức năng book vé dựa trên cái api vừa lấy được. 
 
