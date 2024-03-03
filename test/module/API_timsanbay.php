<!DOCTYPE html>
<html>
<head>
    <title>Hiển thị dữ liệu từ JSON thành form</title>
    <script>
    //     function sendData() {
    //         // Lấy dữ liệu từ các trường input trong form
    //         var name = document.getElementById("name").value;
    //         var iataCode = document.getElementById("iata_code").value;
    //         var countryName = document.getElementById("country_name").value;
    //         var latitude = document.getElementById("latitude").value;
    //         var longitude = document.getElementById("longitude").value;
    //         var timeZoneOffset = document.getElementById("time_zone_offset").value;
    //         var referenceLocalDateTime = document.getElementById("reference_local_date_time").value;

    //         // Tạo một đối tượng JSON chứa dữ liệu
    //         var data = {
    //             name: name,
    //             iataCode: iataCode,
    //             countryName: countryName,
    //             latitude: latitude,
    //             longitude: longitude,
    //             timeZoneOffset: timeZoneOffset,
    //             referenceLocalDateTime: referenceLocalDateTime
    //         };

    //         // Gửi dữ liệu đi
    //         // Ví dụ: sử dụng AJAX
    //         var xhr = new XMLHttpRequest();
    //         xhr.open("POST", "admin.php", true);
    //         xhr.setRequestHeader("Content-Type", "application/json");
    //         xhr.onreadystatechange = function () {
    //             if (xhr.readyState === 4 && xhr.status === 200) {
    //                 // Xử lý phản hồi từ trang web khác (nếu cần)
    //                 console.log(xhr.responseText);
    //             }
    //         };
    //         xhr.send(JSON.stringify(data));
    //     }
    // </script>
</head>
<body>

<form>

<form method="get" action="">
        <label for="searchInput">Tìm kiếm tên quốc gia:</label>
        <input type="text" id="searchInput" name="searchInput">
        <button type="submit">Tìm kiếm</button>
    </form>
    <?php
    // Mã PHP để lấy dữ liệu JSON và điền vào form
    $apiKey = 'vKnqLeZVR6k1AOHQzwrPP4cAxWVRTJLR';
    $apiSecret = 'ukdzzbePHfevXXmc';
    $accessTokenEndpoint = 'https://test.api.amadeus.com/v1/security/oauth2/token';
    $jsonResourceEndpoint = 'https://test.api.amadeus.com/v1/airline/destinations?airlineCode=BA';

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
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
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
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        // Xử lý và hiển thị dữ liệu JSON trong form
        if ($err) {
            echo "CURL Error #:" . $err;
        } else {
            $jsonData = json_decode($response, true);
            if(isset($_GET['searchInput'])){
                $searchInput = $_GET['searchInput'];
        
                // Lặp qua mảng dữ liệu và hiển thị kết quả tương ứng
                foreach ($jsonData['data'] as $item) {
                    if (mb_stripos($item['address']['countryName'], $searchInput) !== false) {
                        echo '<label>Tên: </label><input type="text" value="' . $item['name'] . '"><br>';
                        echo '<label>Mã IATA: </label><input type="text" value="' . $item['iataCode'] . '"><br>';
                        echo '<label>Tên quốc gia: </label><input type="text" value="' . $item['address']['countryName'] . '"><br>';
                        echo '<label>Vĩ độ: </label><input type="text" value="' . $item['geoCode']['latitude'] . '"><br>';
                        echo '<label>Kinh độ: </label><input type="text" value="' . $item['geoCode']['longitude'] . '"><br>';
                        echo '<label>Giờ chênh lệch múi giờ: </label><input type="text" value="' . $item['timeZone']['offSet'] . '"><br>';
                        echo '<label>Thời gian địa phương tham chiếu: </label><input type="text" value="' . $item['timeZone']['referenceLocalDateTime'] . '"><br>';
                        echo '<hr>';
                    }
                }
            }
        }
    }
    ?>
    
</form>

</body>
</html>
