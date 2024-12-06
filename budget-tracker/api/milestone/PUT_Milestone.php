<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $MILESTONE_ID = isset($_POST["MILESTONE_ID"]) ? $_POST["MILESTONE_ID"] : null;
    $MILESTONE_Nome = isset($_POST["MILESTONE_Nome"]) ? $_POST["MILESTONE_Nome"] : null;
    $MILESTONE_Descrizione = isset($_POST["MILESTONE_Descrizione"]) ? $_POST["MILESTONE_Descrizione"] : null;
    $MILESTONE_Completata = isset($_POST["MILESTONE_Completata"]) ? $_POST["MILESTONE_Completata"] : null;

    if (!empty($MILESTONE_ID)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // Preparare i campi da aggiornare
            $updateFields = [];
            $types = '';
            $params = [];

            if (!empty($MILESTONE_Nome)) {
                $updateFields[] = "MILESTONE_Nome = ?";
                $types .= 's';
                $params[] = $MILESTONE_Nome;
            }

            if (!empty($MILESTONE_Descrizione)) {
                $updateFields[] = "MILESTONE_Descrizione = ?";
                $types .= 's';
                $params[] = $MILESTONE_Descrizione;
            }

            if (!is_null($MILESTONE_Completata)) {
                $updateFields[] = "MILESTONE_Completata = ?";
                $types .= 'i';
                $params[] = (int)$MILESTONE_Completata;
            }

            if (count($updateFields) > 0) {
                $query = "UPDATE milestone SET " . implode(", ", $updateFields) . " WHERE MILESTONE_ID = ?";
                
                $types .= 'i';
                $params[] = $MILESTONE_ID;

                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, $types, ...$params);

                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        echo json_encode(["message" => "Milestone aggiornata con successo.", "code" => 200]);
                    } else {
                        echo json_encode(["message" => "Nessuna modifica effettuata. Verifica i dati inviati.", "code" => 69]);
                    }
                } else {
                    echo json_encode(["message" => "Errore durante l'aggiornamento della milestone.", "code" => 500]);
                }

                mysqli_stmt_close($stmt);
            } else {
                echo json_encode(["message" => "Nessun campo da aggiornare fornito.", "code" => 400]);
            }

            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage(), "code" => 500]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "MILESTONE_ID è obbligatorio.", "code" => 400]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito.", "code" => 405]);
}

?>
