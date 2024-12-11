<?php

// delete categoria, elimina una categoria di un utente

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $utente_id = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $categoria_id = isset($_POST["CATEGORIA_ID"]) ? $_POST["CATEGORIA_ID"] : null;

    if (!empty($utente_id) && !empty($categoria_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // Controllo se la categoria esiste
            $check_query = "SELECT COUNT(*) FROM categoria WHERE UTENTE_FK_ID = ? AND CATEGORIA_ID = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            if (!$check_stmt) {
                throw new Exception("Errore nella preparazione della query 1: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($check_stmt, 'ii', $utente_id, $categoria_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);

            if ($count == 0) {
                echo json_encode(array("message" => "La categoria non esiste.", "code" => 404));
                mysqli_close($conn);
                exit();
            }

            // elimino tutte le transizione e milestone associate alla categoria
            $delete_transizione_query = "DELETE FROM transizione WHERE CATEGORIA_FK_ID = ? AND UTENTE_FK_ID = ?";
            $delete_transizione_stmt = mysqli_prepare($conn, $delete_transizione_query);
            if (!$delete_transizione_stmt) {
                throw new Exception("Errore nella preparazione della query 2: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($delete_transizione_stmt, 'ii', $categoria_id, $utente_id);
            mysqli_stmt_execute($delete_transizione_stmt);
            mysqli_stmt_close($delete_transizione_stmt);

            // elimino tutte le milestone associate alla categoria
            $delete_milestone_query = "DELETE FROM milestone WHERE CATEGORIA_FK_ID = ? AND UTENTE_FK_ID = ?";
            $delete_milestone_stmt = mysqli_prepare($conn, $delete_milestone_query);
            if (!$delete_milestone_stmt) {
                throw new Exception("Errore nella preparazione della query 3: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($delete_milestone_stmt, 'ii', $categoria_id, $utente_id);
            mysqli_stmt_execute($delete_milestone_stmt);
            mysqli_stmt_close($delete_milestone_stmt);

            // Elimina la categoria
            $delete_query = "DELETE FROM categoria WHERE UTENTE_FK_ID = ? AND CATEGORIA_ID = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_query);
            if (!$delete_stmt) {
                throw new Exception("Errore nella preparazione della query 4: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($delete_stmt, 'ii', $utente_id, $categoria_id);

            if (mysqli_stmt_execute($delete_stmt)) {
                echo json_encode(array("message" => "Categoria eliminata con successo.", "code" => 200));
            } else {
                throw new Exception("Errore durante l'eliminazione della categoria.");
            }

            mysqli_stmt_close($delete_stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
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
