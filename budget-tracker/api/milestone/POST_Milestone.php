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
    $categoria_nome = isset($_POST["CATEGORIA_Nome"]) ? $_POST["CATEGORIA_Nome"] : null;
    $milestone_nome = isset($_POST["MILESTONE_Nome"]) ? $_POST["MILESTONE_Nome"] : null;
    $milestone_DataInizio = isset($_POST["MILESTONE_DataInizio"]) ? $_POST["MILESTONE_DataInizio"] : null;
    $milestone_DataFine = isset($_POST["MILESTONE_DataFine"]) ? $_POST["MILESTONE_DataFine"] : null;
    $milestone_Descrizione = isset($_POST["MILESTONE_Descrizione"]) ? $_POST["MILESTONE_Descrizione"] : null;

    if (!empty($utente_id) && 
        !empty($categoria_nome) && 
        !empty($milestone_nome) && 
        !empty($milestone_DataInizio) && 
        !empty($milestone_DataFine) && 
        !empty($milestone_Descrizione)) { // i controlli verranno fatti nel front end

        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // LA CATEGORIA è ASSOCIATA ALL'UTENTE?
                $query_verifica = "SELECT CATEGORIA_ID FROM categoria WHERE UTENTE_FK_ID = ? AND CATEGORIA_Nome = ?";
                $stmt_verifica = mysqli_prepare($conn, $query_verifica);
                mysqli_stmt_bind_param($stmt_verifica, 'is', $utente_id, $categoria_nome);
                mysqli_stmt_execute($stmt_verifica);
                mysqli_stmt_bind_result($stmt_verifica, $categoria_id);     //ottengo categoria ID
                mysqli_stmt_fetch($stmt_verifica);

            if (!empty($categoria_id)) {
                mysqli_stmt_close($stmt_verifica);

                // Inserisci la milestone
                $query = "INSERT INTO milestone (MILESTONE_Nome, MILESTONE_DataInizio, MILESTONE_DataFine, MILESTONE_Descrizione, CATEGORIA_FK_ID, UTENTE_FK_ID) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ssssii', $milestone_nome, $milestone_DataInizio, $milestone_DataFine, $milestone_Descrizione ,$categoria_id, $utente_id);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    echo json_encode(array("message" => "Milestone creata con successo.", "code" => 201, "post" => $_POST));
                } else {
                    echo json_encode(array("message" => "Errore durante la creazione della milestone.", "code" => 400));
                }

                mysqli_stmt_close($stmt);
            } else {
                echo json_encode(array("message" => "Categoria non trovata o non associata all'utente."));
            }

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
