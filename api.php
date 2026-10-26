<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "pythonuser", "Yahya18+", "gas_db");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gas = isset($_POST['gas_value']) ? intval($_POST['gas_value']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'NORMAL';

    $sql = "INSERT INTO gas_logs (gas_value, status) VALUES ('$gas', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        
        // --- LOGIKA NOTIFIKASI TELEGRAM ---
        if ($status === 'BAHAYA') {
            $telegram_token = "8982648410:AAFtIpr1mCDxRv5HNFSs-BiDyob4Q5nPthc";
            $chat_id = "8928465456";
            
            $pesan = "⚠️ *PERINGATAN BAHAYA KEBOCORAN GAS!* ⚠️\n\n";
            $pesan .= "Nilai Sensor: *" . $gas . "*\n";
            $pesan .= "Status: *" . $status . "*\n";
            $pesan .= "Waktu: " . date("Y-m-d H:i:s");

            $url = "https://api.telegram.org/bot" . $telegram_token . "/sendMessage";
            $data = [
                'chat_id' => $chat_id,
                'text' => $pesan,
                'parse_mode' => 'Markdown'
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                ]
            ];
            $context  = stream_context_create($options);
            @file_get_contents($url, false, $context);
        }
        // ---------------------------------

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    $result = $conn->query("SELECT * FROM gas_logs ORDER BY id DESC LIMIT $limit");
    $data = array();
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
}

$conn->close();
?>
