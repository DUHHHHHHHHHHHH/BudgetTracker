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
    $CATEGORIA_Nome = isset($_POST["CATEGORIA_Nome"]) ? $_POST["CATEGORIA_Nome"] : null;
    $TRANSIZIONE_Nome = isset($_POST["TRANSIZIONE_Nome"]) ? $_POST["TRANSIZIONE_Nome"] : null;
    $TRANSIZIONE_Data = isset($_POST["TRANSIZIONE_Data"]) ? $_POST["TRANSIZIONE_Data"] : null;
    $TRANSIZIONE_Tipo = isset($_POST["TRANSIZIONE_Tipo"]) ? $_POST["TRANSIZIONE_Tipo"] : null;
    $TRANSIZIONE_QTA = isset($_POST["TRANSIZIONE_QTA"]) ? $_POST["TRANSIZIONE_QTA"] : null;

    if (!empty($utente_id) && !empty($CATEGORIA_Nome) && !empty($TRANSIZIONE_Nome) && !empty($TRANSIZIONE_Data) && !empty($TRANSIZIONE_Tipo) && !empty($TRANSIZIONE_QTA)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // CONVERSIONE DELLA DATA in qualcosa di decente
            // $dataSecondi = date("d-m-Y", strtotime($TRANSIZIONE_Data));
            // $TRANSIZIONE_Data = date("d-m-Y", $dataSecondi);
            

            // LA CATEGORIA è ASSOCIATA ALL'UTENTE?
                $query_verifica = "SELECT CATEGORIA_ID FROM categoria WHERE UTENTE_FK_ID = ? AND CATEGORIA_Nome = ?";
                $stmt_verifica = mysqli_prepare($conn, $query_verifica);
                mysqli_stmt_bind_param($stmt_verifica, 'is', $utente_id, $CATEGORIA_Nome);
                mysqli_stmt_execute($stmt_verifica);
                mysqli_stmt_bind_result($stmt_verifica, $categoria_id);
                mysqli_stmt_fetch($stmt_verifica);

            if (!empty($categoria_id)) {
                mysqli_stmt_close($stmt_verifica);

                // INSERIMENTO DELLA TRANSAZIONE
                $query = "INSERT INTO transizione (TRANSIZIONE_Nome, TRANSIZIONE_Data, TRANSIZIONE_QTA, TRANSIZIONE_Tipo, CATEGORIA_FK_ID, UTENTE_FK_ID) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                echo json_encode("tutti i parametri: " . $TRANSIZIONE_Nome . " " . $TRANSIZIONE_Data . " " . $TRANSIZIONE_QTA . " " . $TRANSIZIONE_Tipo . " " . $categoria_id . " " . $utente_id);
                mysqli_stmt_bind_param($stmt, 'ssdsii', $TRANSIZIONE_Nome, $TRANSIZIONE_Data, $TRANSIZIONE_QTA, $TRANSIZIONE_Tipo, $categoria_id, $utente_id);
                mysqli_stmt_execute($stmt);

                // Aggiorno il budget della categoria con un'unica query JOIN, se il tipo è ENTRATA = aggiungo al budget, altrimenti SPESA = sottraggo al budget
                $query_update = "UPDATE categoria c 
                               SET c.CATEGORIA_Budget = CASE 
                                   WHEN ? = 'ENTRATA' THEN c.CATEGORIA_Budget + ?       
                                   ELSE c.CATEGORIA_Budget - ?
                               END 
                               WHERE c.CATEGORIA_ID = ?";
                $stmt_update = mysqli_prepare($conn, $query_update);
                if ($stmt_update === false) {
                    throw new Exception("Errore nella preparazione della query di aggiornamento: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($stmt_update, 'sddi', $TRANSIZIONE_Tipo, $TRANSIZIONE_QTA, $TRANSIZIONE_QTA, $categoria_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
                mysqli_stmt_close($stmt);

                echo json_encode(array("message" => "Transazione creata con successo.", "code" => 201));
            } else {
                echo json_encode(array("message" => "Categoria non trovata o non appartiene all'utente.", "code" => 400, "categoria_id" => $categoria_id));
            }

            // mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Tutti i campi sono obbligatori."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}

?>
