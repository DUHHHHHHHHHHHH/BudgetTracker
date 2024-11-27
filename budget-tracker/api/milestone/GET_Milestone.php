<?php

// get milestone, restituisce i dati di una milestone tramite criteri specifici

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $utente_id = isset($_POST["utente_id"]) ? $_POST["utente_id"] : null;
    $nome_categoria = isset($_POST["nome_categoria"]) ? $_POST["nome_categoria"] : null;
    $nome_milestone = isset($_POST["nome_milestone"]) ? $_POST["nome_milestone"] : null;

    if (!empty($utente_id) && !empty($nome_categoria) && !empty($nome_milestone)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $query = "SELECT * FROM milestone WHERE UTENTE_FK_ID = ? AND CATEGORIA_Nome = ? AND MILESTONE_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'iss', $utente_id, $nome_categoria, $nome_milestone);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $MILESTONE_ID, $MILESTONE_DataInizio, $MILESTONE_DataFine, $MILESTONE_Nome, $MILESTONE_Descrizione, $UTENTE_FK_ID, $CATEGORIA_FK_ID);
                mysqli_stmt_fetch($stmt);

                $milestone = array(
                    "MILESTONE_ID" => $MILESTONE_ID,
                    "MILESTONE_DataInizio" => $MILESTONE_DataInizio,
                    "MILESTONE_DataFine" => $MILESTONE_DataFine,
                    "MILESTONE_Nome" => $MILESTONE_Nome,
                    "MILESTONE_Descrizione" => $MILESTONE_Descrizione,
                    "UTENTE_FK_ID" => $UTENTE_FK_ID,
                    "CATEGORIA_FK_ID" => $CATEGORIA_FK_ID
                );

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                echo json_encode($milestone);
            } else {
                throw new Exception("Milestone non trovata.");
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
