<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $utente_id = isset($data["utente_id"]) ? $data["utente_id"] : null;
    $report_nome = isset($data["report_nome"]) ? $data["report_nome"] : null;
    $report_descrizione = isset($data["report_descrizione"]) ? $data["report_descrizione"] : null;
    $data_report = isset($data["report_fileexport"]) ? $data["report_fileexport"] : null;

    if (!empty($utente_id) && !empty($report_nome) && !empty($data_report)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // INSERIMENTO DEL REPORT
                $query = "INSERT INTO report (REPORT_Nome, REPORT_Descrizione, REPORT_FileExport, UTENTE_FK_ID) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ssbi', $report_nome, $report_descrizione, $data_report, $utente_id);
                mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(array("message" => "Report creato con successo."));
            } else {
                throw new Exception("Errore durante la creazione del report.");
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Tutti i campi sono obbligatori."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}

?>
