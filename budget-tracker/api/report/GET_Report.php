<?php

// get report, restituisce i dati di un report tramite utente ID, nome report.

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $utente_id = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $nome_report = isset($_POST["REPORT_Nome"]) ? $_POST["REPORT_Nome"] : null;

    if (!empty($utente_id) && !empty($nome_report)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $query = "SELECT * FROM report WHERE UTENTE_FK_ID = ? AND REPORT_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'is', $utente_id, $nome_report);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $REPORT_ID, $REPORT_Nome, $REPORT_DataGenerazione, $REPORT_FileExport, $UTENTE_FK_ID);
                mysqli_stmt_fetch($stmt);

                $report = array(
                    "REPORT_ID" => $REPORT_ID,
                    "REPORT_Nome" => $REPORT_Nome,
                    "REPORT_DataGenerazione" => $REPORT_DataGenerazione,
                    "REPORT_FileExport" => $REPORT_FileExport,
                    "UTENTE_FK_ID" => $UTENTE_FK_ID
                );

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                echo json_encode($report);
            } else {
                throw new Exception("Report non trovato.");
            }
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Tutti i campi sono richiesti."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}
?>
