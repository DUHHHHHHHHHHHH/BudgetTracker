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
    $transizione_id = isset($_POST["TRANSIZIONE_ID"]) ? $_POST["TRANSIZIONE_ID"] : null;
    $categoria_id = isset($_POST["CATEGORIA_ID"]) ? $_POST["CATEGORIA_ID"] : null;

    if (!empty($utente_id) && !empty($transizione_id) && !empty($categoria_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                echo json_encode(array("message" => "Connessione al database fallita: " . mysqli_connect_error(), "code" => 500));
            }

            mysqli_begin_transaction($conn);

            // Ottieni l'importo e il tipo della transizione prima di eliminarla
            $query_select = "SELECT TRANSIZIONE_QTA, TRANSIZIONE_Tipo FROM transizione WHERE UTENTE_FK_ID = ? AND TRANSIZIONE_ID = ? AND CATEGORIA_FK_ID = ?";
            $stmt_select = mysqli_prepare($conn, $query_select);
            mysqli_stmt_bind_param($stmt_select, 'iii', $utente_id, $transizione_id, $categoria_id);
            mysqli_stmt_execute($stmt_select);
            $result = mysqli_stmt_get_result($stmt_select);
            $transizione = mysqli_fetch_assoc($result);

                        if ($transizione) {
                            $importo = $transizione['TRANSIZIONE_QTA'];
                            $tipo = $transizione['TRANSIZIONE_Tipo'];

                            // Aggiorna la categoria
                            $query_update = "";
                            if ($tipo == 'ENTRATA') {
                                $query_update = "UPDATE categoria SET CATEGORIA_Budget = CATEGORIA_Budget - ? WHERE CATEGORIA_ID = ? AND UTENTE_FK_ID = ?";
                            } else {
                                $query_update = "UPDATE categoria SET CATEGORIA_Budget = CATEGORIA_Budget + ? WHERE CATEGORIA_ID = ? AND UTENTE_FK_ID = ?";
                            }

                            $stmt_update = mysqli_prepare($conn, $query_update);
                            mysqli_stmt_bind_param($stmt_update, 'dii', $importo, $categoria_id, $utente_id);
                            mysqli_stmt_execute($stmt_update);

                // Elimina la transizione
                $query_delete = "DELETE FROM transizione WHERE UTENTE_FK_ID = ? AND TRANSIZIONE_ID = ? AND CATEGORIA_FK_ID = ?";
                $stmt_delete = mysqli_prepare($conn, $query_delete);
                mysqli_stmt_bind_param($stmt_delete, 'iii', $utente_id, $transizione_id, $categoria_id);
                mysqli_stmt_execute($stmt_delete);

                if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                    mysqli_commit($conn);
                    echo json_encode(array("message" => "Transazione eliminata con successo.", "code" => 200));
                } else {
                    mysqli_rollback($conn);
                    echo json_encode(array("message" => "Transazione non trovata o non eliminata.", "code" => 400));
                }

                mysqli_stmt_close($stmt_delete);
                mysqli_stmt_close($stmt_update);
            } else {
                mysqli_rollback($conn);
                echo json_encode(array("message" => "Transazione non trovata.", "code" => 400));
            }

            mysqli_stmt_close($stmt_select);
            mysqli_close($conn);
        } catch (Exception $e) {
            if (isset($conn)) {
                mysqli_rollback($conn);
            }
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID utente e ID transazione sono richiesti.", "code" => 400));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito.", "code" => 405));
}

?>
