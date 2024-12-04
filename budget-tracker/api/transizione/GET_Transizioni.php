<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $utente_id = isset($_GET["utente_id"]) ? $_GET["utente_id"] : null;
    $categoria_id = isset($_GET["categoria_id"]) ? $_GET["categoria_id"] : null;

    if (!empty($utente_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            if (!empty($categoria_id)) {
                $query = "SELECT * FROM transazione WHERE UTENTE_FK_ID = ? AND CATEGORIA_FK_ID = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ii', $utente_id, $categoria_id);
            } else {
                $query = "SELECT * FROM transazione WHERE UTENTE_FK_ID = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'i', $utente_id);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $transazioni = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $transazioni[] = $row;
            }

            echo json_encode($transazioni);

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID utente richiesto."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito."]);
}
?>
