<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $utente_id = isset($data["utente_id"]) ? $data["utente_id"] : null;
    $categoria_nome = isset($data["categoria_nome"]) ? $data["categoria_nome"] : null;
    $transazione_nome = isset($data["transazione_nome"]) ? $data["transazione_nome"] : null;
    $transizione_data = isset($data["transizione_data"]) ? $data["transizione_data"] : null;
    $transizione_tipo = isset($data["transizione_tipo"]) ? $data["transizione_tipo"] : null;
    $importo = isset($data["importo"]) ? $data["importo"] : null;

    if (!empty($utente_id) && !empty($categoria_nome) && !empty($transazione_nome) && !empty($transizione_data) && !empty($transizione_tipo) && !empty($importo)) {
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
                mysqli_stmt_bind_result($stmt_verifica, $categoria_id);
                mysqli_stmt_fetch($stmt_verifica);

            if ($categoria_id) {
                mysqli_stmt_close($stmt_verifica);

            // INSERIMENTO DELLA TRANSAZIONE
                $query = "INSERT INTO transazione (TRANSIZIONE_Nome, TRANSIZIONE_Data, TRANSIZIONE_QTA, TRANSIZIONE_Tipo, CATEGORIA_FK_ID, UTENTE_FK_ID) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ssdsii', $transazione_nome, $transizione_data, $importo, $transizione_tipo, $categoria_id, $utente_id);
                mysqli_stmt_execute($stmt);

                echo json_encode(array("message" => "Transazione creata con successo."));
            } else {
                echo json_encode(array("message" => "Categoria non trovata o non appartiene all'utente."));
            }

            mysqli_stmt_close($stmt);
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
