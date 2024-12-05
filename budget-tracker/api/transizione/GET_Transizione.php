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
    $UTENTE_ID = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $TRANSIZIONE_Nome = isset($_POST["TRANSIZIONE_Nome"]) ? $_POST["TRANSIZIONE_Nome"] : null;
    $TRANSIZIONE_Data = isset($_POST["TRANSIZIONE_Data"]) ? $_POST["TRANSIZIONE_Data"] : null;
    $CATEGORIA_Nome = isset($_POST["CATEGORIA_Nome"]) ? $_POST["CATEGORIA_Nome"] : null;

    if (!empty($UTENTE_ID) && !empty($TRANSIZIONE_Nome) && !empty($TRANSIZIONE_Data) && !empty($CATEGORIA_Nome)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            // Query con JOIN tra transazione e categoria
            $query = "
                SELECT 
                    t.TRANSIZIONE_ID, 
                    t.TRANSIZIONE_Nome, 
                    t.TRANSIZIONE_Data, 
                    t.TRANSIZIONE_DataGenerazione, 
                    t.TRANSIZIONE_QTA, 
                    t.TRANSIZIONE_Tipo, 
                    t.UTENTE_FK_ID, 
                    t.CATEGORIA_FK_ID
                FROM 
                    transizione t
                JOIN 
                    categoria c 
                ON 
                    t.CATEGORIA_FK_ID = c.CATEGORIA_ID
                WHERE 
                    t.UTENTE_FK_ID = ? 
                    AND t.TRANSIZIONE_Nome = ? 
                    AND t.TRANSIZIONE_Data = ? 
                    AND c.CATEGORIA_Nome = ?
                    AND t.TRANSIZIONE_DataGenerazione IS NOT NULL
                LIMIT 1            ";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'isss', $UTENTE_ID, $TRANSIZIONE_Nome, $TRANSIZIONE_Data, $CATEGORIA_Nome);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result(
                    $stmt,
                    $TRANSIZIONE_ID, 
                    $TRANSIZIONE_Nome, 
                    $TRANSIZIONE_Data, 
                    $TRANSIZIONE_DataGenerazione, 
                    $TRANSIZIONE_QTA, 
                    $TRANSIZIONE_Tipo, 
                    $UTENTE_FK_ID, 
                    $CATEGORIA_FK_ID
                );

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
