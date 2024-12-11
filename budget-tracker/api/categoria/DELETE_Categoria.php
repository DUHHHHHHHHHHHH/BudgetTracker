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
    $nome_categoria = isset($_POST["CATEGORIA_ID"]) ? $_POST["CATEGORIA_ID"] : null;

    if (!empty($utente_id) && !empty($nome_categoria)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            // Controllo se la categoria esiste
            $check_query = "SELECT COUNT(*) FROM categoria WHERE UTENTE_FK_ID = ? AND CATEGORIA_ID = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, 'is', $utente_id, $nome_categoria);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);

            if ($count == 0) {
                echo json_encode(array("message" => "La categoria non esiste.", "code" => 404));
                exit();
            }

            // Verifica se la categoria è associata a transazioni o milestones
            $check_associations_query = "SELECT COUNT(*) FROM transazioni WHERE CATEGORIA_ID = ? AND UTENTE_FK_ID = ?";
            $check_associations_stmt = mysqli_prepare($conn, $check_associations_query);
            mysqli_stmt_bind_param($check_associations_stmt, 'si', $nome_categoria, $utente_id);
            mysqli_stmt_execute($check_associations_stmt);
            mysqli_stmt_bind_result($check_associations_stmt, $assoc_count);
            mysqli_stmt_fetch($check_associations_stmt);
            mysqli_stmt_close($check_associations_stmt);

            if ($assoc_count > 0) {
                echo json_encode(array("message" => "Impossibile eliminare la categoria. È associata a una o più transazioni.", "code" => 409));
                exit();
            }

            // Verifica anche per le milestones
            $check_milestones_query = "SELECT COUNT(*) FROM milestones WHERE CATEGORIA_ID = ? AND UTENTE_FK_ID = ?";
            $check_milestones_stmt = mysqli_prepare($conn, $check_milestones_query);
            mysqli_stmt_bind_param($check_milestones_stmt, 'si', $nome_categoria, $utente_id);
            mysqli_stmt_execute($check_milestones_stmt);
            mysqli_stmt_bind_result($check_milestones_stmt, $milestone_count);
            mysqli_stmt_fetch($check_milestones_stmt);
            mysqli_stmt_close($check_milestones_stmt);

            if ($milestone_count > 0) {
                echo json_encode(array("message" => "Impossibile eliminare la categoria. È associata a una o più milestones.", "code" => 409));
                exit();
            }

            // Elimina la categoria
            $query = "DELETE FROM categoria WHERE UTENTE_FK_ID = ? AND CATEGORIA_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'is', $utente_id, $nome_categoria);

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(array("message" => "Categoria eliminata con successo.", "code" => 200));
            } else {
                echo json_encode(array("message" => "Errore durante l'eliminazione della categoria." , "code" => 500));
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit();
            }

            mysqli_stmt_close($stmt);
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
