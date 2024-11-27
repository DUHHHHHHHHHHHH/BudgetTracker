<?php

// get transazione, restituisce i dati di una transazione tramite utente ID, nome transizione, data transizione, nome categoria.

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $utente_id = isset($_POST["utente_id"]) ? $_POST["utente_id"] : null;
    $nome_transazione = isset($_POST["nome_transazione"]) ? $_POST["nome_transazione"] : null;
    $data_transazione = isset($_POST["data_transazione"]) ? $_POST["data_transazione"] : null;
    $nome_categoria = isset($_POST["nome_categoria"]) ? $_POST["nome_categoria"] : null;

    if (!empty($utente_id) && !empty($nome_transazione) && !empty($data_transazione) && !empty($nome_categoria)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $query = "SELECT * FROM transazione WHERE UTENTE_FK_ID = ? AND TRANSAZIONE_Nome = ? AND TRANSAZIONE_Data = ? AND CATEGORIA_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'isss', $utente_id, $nome_transazione, $data_transazione, $nome_categoria);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $TRANSIZIONE_ID, $TRANSIZIONE_Nome, $TRANSIZIONE_Data, $TRANSIZIONE_DataGenerazione, $TRANSIZIONE_QTA, $TRANSIZIONE_Tipo, $UTENTE_FK_ID, $CATEGORIA_FK_ID);
                mysqli_stmt_fetch($stmt);

                $transazione = array(
                    "TRANSIZIONE_ID" => $TRANSIZIONE_ID,
                    "TRANSIZIONE_Nome" => $TRANSIZIONE_Nome,
                    "TRANSIZIONE_Data" => $TRANSIZIONE_Data,
                    "TRANSIZIONE_DataGenerazione" => $TRANSIZIONE_DataGenerazione,
                    "TRANSIZIONE_QTA" => $TRANSIZIONE_QTA,
                    "TRANSIZIONE_Tipo" => $TRANSIZIONE_Tipo,
                    "UTENTE_FK_ID" => $UTENTE_FK_ID,
                    "CATEGORIA_FK_ID" => $CATEGORIA_FK_ID
                );

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                echo json_encode($transazione);
            } else {
                throw new Exception("Transazione non trovata.");
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
