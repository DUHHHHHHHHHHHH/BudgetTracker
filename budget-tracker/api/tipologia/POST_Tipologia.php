<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

// L'ADMIN crea la TIPOLOGIA

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = isset($_POST['TIPOLOGIA_Nome']) ? $_POST['TIPOLOGIA_Nome'] : null;
    $descrizione = isset($_POST['TIPOLOGIA_Descrizione']) ? $_POST['TIPOLOGIA_Descrizione'] : null;
    $admin_id = isset($_POST['ADMIN_ID']) ? $_POST['ADMIN_ID'] : null;

    if (!empty($nome) && !empty($admin_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                echo json_encode(array("message" => "Connessione al database fallita",  mysqli_connect_error(), "code" => 500));
            }

                // CONTROLLO SE ESISTE GIA UNA TIPOLOGIA CON LO STESSO NOME
            $check_query = "SELECT COUNT(*) FROM TIPOLOGIA WHERE TIPOLOGIA_Nome = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $nome);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);

            if ($count > 0) {
                // Esiste già una tipologia con lo stesso nome
                echo json_encode(array("message" => "Esiste già una tipologia con lo stesso nome.", "code" => 400));
                mysqli_stmt_close($check_stmt);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($check_stmt);
            
        // SE non ho problemi prima, inserisco la nuova TIPOLOGIA
            $insert_query = "INSERT INTO TIPOLOGIA (TIPOLOGIA_Nome, TIPOLOGIA_Descrizione, ADMIN_FK_ID) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "ssi", $nome, $descrizione, $admin_id);

            if (!mysqli_stmt_execute($insert_stmt)) {
                http_response_code(500);
                echo json_encode(array("message" => "Errore durante l'inserimento dei dati:", mysqli_error($conn), "code" => 400));
            }

            mysqli_stmt_close($insert_stmt);
            mysqli_close($conn);

            echo json_encode(array("message" => "Prenotazione inserita con successo.", "code" => 200));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => "Errore server - " . $e->getMessage(), "code" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Dati di prenotazione non validi.", "code" => 400));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito.", "code" => 405));
}
?>