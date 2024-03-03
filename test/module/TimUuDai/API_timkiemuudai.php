<?php

// Kiểm tra xem có dữ liệu được gửi từ form không
if (isset($_GET['destinationLocationCode']) && isset($_GET['departureDate']) && isset($_GET['adults']) && isset($_GET['originLocationCode'])) {
    $destinationLocationCode = $_GET['destinationLocationCode'];
    $departureDate = $_GET['departureDate'];
    $adults = $_GET['adults'];
    $originLocationCode = $_GET['originLocationCode'];

    // Thay đổi các giá trị này thành các giá trị thực tế của bạn
    $apiKey = 'vKnqLeZVR6k1AOHQzwrPP4cAxWVRTJLR';
    $apiSecret = 'ukdzzbePHfevXXmc';
    $accessTokenEndpoint = 'https://test.api.amadeus.com/v1/security/oauth2/token';
    $jsonResourceEndpoint = 'https://test.api.amadeus.com/v2/shopping/flight-offers';

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

        // Gửi yêu cầu đến nguồn dữ liệu JSON với access token và dữ liệu từ form
        $curl = curl_init();

        $queryParams = http_build_query(array(
            'originLocationCode' => $originLocationCode,
            'destinationLocationCode' => $destinationLocationCode,
            'departureDate' => $departureDate,
            'adults' => $adults
        ));

        $jsonResourceEndpoint .= '?' . $queryParams;

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
           
                foreach ($jsonData['data'] as $flight) {
                    $itinerary = $flight['itineraries'][0];
                    $segments = $itinerary['segments'];
            
                    $departureAirport = $segments[0]['departure']['iataCode'];
                    $arrivalAirport = end($segments)['arrival']['iataCode'];
                    $departureTime = $segments[0]['departure']['at'];
                    $arrivalTime = end($segments)['arrival']['at'];
                    $duration = $itinerary['duration'];
                    $price = $flight['price']['total'];
            ?>
            
                <div>
                    <p>Từ: <?php echo $departureAirport; ?> đến: <?php echo $arrivalAirport; ?></p>
                    <p>Giờ khởi hành: <?php echo $departureTime; ?></p>
                    <p>Giờ đến: <?php echo $arrivalTime; ?></p>
                    <p>Thời lượng: <?php echo $duration; ?></p>
                    <p>Giá: <?php echo $price; ?></p>
                    <hr>
                </div>
            
            <?php
                }
            
        }
    }
} else {
    // Nếu không có dữ liệu từ form, hiển thị thông báo lỗi
    echo "Missing required data from the form!";
}
?>
