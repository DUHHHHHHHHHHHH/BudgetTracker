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
    $report_nome = isset($_POST["REPORT_Nome"]) ? $_POST["REPORT_Nome"] : null;
    $report_descrizione = isset($_POST["REPORT_Descrizione"]) ? $_POST["REPORT_Descrizione"] : null;
    $report_datagenerazione = isset($_POST["REPORT_DataGenerazione"]) ? $_POST["REPORT_DataGenerazione"] : null;

    /* CONTROLLO SUL FILE */

    if (isset($_FILES['REPORT_FileExport'])) {
        $file = $_FILES['REPORT_FileExport'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        // Verifica se ci sono errori nel caricamento
        if ($fileError === 0) {
            // Ottieni l'estensione del file
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            // Estensioni permesse
            $allowed = array('jpg', 'jpeg', 'png', 'pdf', 'csv', 'ppt', 'pptx', 'xls', 'xlsx', 'doc', 'docx', 'potx');

            if (in_array($fileExt, $allowed)) {
                // Genera un nome univoco per il file
                $fileNameNew = uniqid('', true) . "." . $fileExt;
                $uploadDir = __DIR__ . '/../../DB/DB_Reports/';

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileDestination = $uploadDir . $fileNameNew;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $data_report = $fileNameNew;
                } else {
                    echo json_encode(array("message" => "Error moving uploaded file: " . error_get_last()['message'], "code" => 500));
                    exit;
                }
            } else {
                echo json_encode(array("message" => "Tipo di file non supportato.", "code" => 400));
            }
        } else {
            echo json_encode(array("message" => "Errore durante il caricamento del file.", "code" => 400));
        }
    } else {
        $data_report = null;
    }

    if (!empty($utente_id) && !empty($report_nome) && !empty($data_report)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // CONTROLLO SE CI SONO ALTRI REPORT CON LO STESSO NOME IN BASE ALL'UTENTE
            $query = "SELECT REPORT_ID FROM report WHERE UTENTE_FK_ID = ? AND REPORT_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'is', $utente_id, $report_nome);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $report_id);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            if (!empty($report_id)) {
                echo json_encode(array("message" => "Esiste già un report con lo stesso nome per questo utente.", "code" => 400));
                exit;
            }

            // INSERIMENTO DEL REPORT
            $file_path = 'DB/DB_Reports/' . $data_report; // Salviamo il percorso relativo del file
            $query = "INSERT INTO report (REPORT_Nome, REPORT_Descrizione, REPORT_DataGenerazione, REPORT_FileExport, UTENTE_FK_ID) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {
                http_response_code(500);
                echo json_encode(array("message" => "Error preparing statement: " . mysqli_error($conn), "code" => 500));
                exit;
            }

            mysqli_stmt_bind_param($stmt, 'ssssi', $report_nome, $report_descrizione, $report_datagenerazione, $file_path, $utente_id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(array("message" => "Report creato con successo.", "code" => 200, "path_file" => $file_path, "REPORT_ID" => mysqli_insert_id($conn)));
            } else {
                echo json_encode(array("message" => "Errore durante la creazione del report", "code" => 400));
            }

            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage(), "code" => 500));
        } finally {
            mysqli_close($conn);
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Tutti i campi sono obbligatori.", "code" => 400));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito.", "code" => 405));
}
?>