<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utente_id = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;

    if (!empty($utente_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            $query = "SELECT * FROM report WHERE UTENTE_FK_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $utente_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $reports = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $reports[] = $row;
            }

            // ESTRAGGO LE RIGHE CHE MI INTERESSANO:

            $reports = array_map(function ($report) {
                return [
                    "REPORT_ID" => $report["REPORT_ID"],
                    "REPORT_Nome" => $report["REPORT_Nome"],
                    "REPORT_DataGenerazione" => $report["REPORT_DataGenerazione"],
                    "REPORT_FileExport" => $report["REPORT_FileExport"]
                ];
            }, $reports);

            echo json_encode($reports);

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID utente richiesto.", "code" => 400]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito.", "code" => 405]);
}
?>
