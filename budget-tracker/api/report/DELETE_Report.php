<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utente_id = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $report_id = isset($_POST["REPORT_ID"]) ? $_POST["REPORT_ID"] : null;

    if (!empty($utente_id) && !empty($report_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                echo json_encode(array("message" => "Connessione al database fallita:", mysqli_connect_error(), "code" => 500));
            }

            $query = "DELETE FROM report WHERE UTENTE_FK_ID = ? AND REPORT_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $utente_id, $report_id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(array("message" => "Report eliminato con successo.", "code" => 200));
            } else {
                echo json_encode(array("message" => "Report non trovato o non eliminato.", "code" => 400));
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID utente e ID report sono richiesti.", "code" => 400));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito.", "code" => 500));
}

?>
