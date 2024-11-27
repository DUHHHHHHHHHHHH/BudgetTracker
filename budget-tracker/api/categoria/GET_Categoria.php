<?php

// get categoria, fornisce una specifica categoria di un utente tramite il NOME e l'ID utente

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $nome_categoria = isset($_POST["nome_categoria"]) ? $_POST["nome_categoria"] : null;
    $utente_username = isset($_POST["utente_id"]) ? $_POST["utente_id"] : null;

    if (!empty($nome_categoria) && !empty($utente_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            // SELEZIONE TUTTO dalla tabella CATEGORIA dove ho ID UTENTE e NOME categoria.

            $query = "SELECT * FROM categoria WHERE UTENTE_FK_ID = ? AND CATEGORIA_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'si', $nome_categoria, $utente_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $CATEGORIA_ID, $CATEGORIA_Nome, $CATEGORIA_Descrizione, $CATEGORIA_Budget, $TIPOLOGIA_FK_ID, $UTENTE_FK_ID);
                mysqli_stmt_fetch($stmt);

                $categoria = array(
                    "CATEGORIA_ID" => $CATEGORIA_ID,
                    "CATEGORIA_Nome" => $CATEGORIA_Nome,
                    "CATEGORIA_Descrizione" => $CATEGORIA_Descrizione,
                    "CATEGORIA_Budget" => $CATEGORIA_Budget,
                    "TIPOLOGIA_FK_ID" => $TIPOLOGIA_FK_ID,
                    "UTENTE_FK_ID" => $UTENTE_FK_ID
                );

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                echo json_encode($categoria);
            } else {
                echo json_encode(array("message" => "Categoria non trovata per l'utente specificato."));
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
