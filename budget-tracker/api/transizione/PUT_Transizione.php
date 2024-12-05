<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verifica se i dati necessari sono presenti
    $TRANSIZIONE_ID = isset($_POST["TRANSIZIONE_ID"]) ? $_POST["TRANSIZIONE_ID"] : null;
    $UTENTE_ID = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $TRANSIZIONE_NewNome = isset($_POST["TRANSIZIONE_NewNome"]) ? $_POST["TRANSIZIONE_NewNome"] : null;

    if (!empty($TRANSIZIONE_ID) && !empty($UTENTE_ID)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // Preparare la query di aggiornamento
            $updateFields = array();
            $types = '';
            $params = array();

            if (!empty($TRANSIZIONE_NewNome)) {
                $updateFields[] = "TRANSIZIONE_Nome = ?";
                $types .= 's';
                $params[] = $TRANSIZIONE_NewNome;
            }

            if (count($updateFields) > 0) {
                $query = "UPDATE transizione SET " . implode(", ", $updateFields) . " WHERE TRANSIZIONE_ID = ? AND UTENTE_FK_ID = ?";
                
                $types .= 'si';
                $params[] = $TRANSIZIONE_ID;
                $params[] = $UTENTE_ID;

                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, $types, ...$params);

                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        echo json_encode(["message" => "Transazione aggiornata con successo.", "code" => 200]);
                    } else {
                        echo json_encode(["message" => "Nessuna modifica effettuata. Verifica i dati inviati.", "code" => 69]);
                    }
                } else {
                    echo json_encode(["message" => "Errore durante l'aggiornamento della transazione.", "code" => 500]);
                }

                mysqli_stmt_close($stmt);
            } else {
                echo json_encode(["message" => "Nessun campo da aggiornare fornito.", "code" => 400]);
            }
            
            mysqli_close($conn);        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage(), "code" => 500]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Tutti i campi richiesti (TRANSIZIONE_ID, UTENTE_ID) devono essere forniti.", "code" => 400]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito.", "code" => 405]);
}
?>
