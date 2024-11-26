<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

        if (!$conn) {
            throw new Exception("connessione col database fallita " . mysqli_connect_error());
        }

        $query = "SELECT UTENTE_ID, UTENTE_Mail, UTENTE_Username FROM utente";
        $result = mysqli_query($conn, $query);

        $users = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        mysqli_close($conn);

        echo json_encode($users);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("error" => $e->getMessage()));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito"));
}