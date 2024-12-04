<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Ottieni i dati JSON dalla richiesta
    $data = json_decode(file_get_contents("php://input"), true);

    // Verifica se i dati necessari sono presenti
    $nome_transazione = isset($data["nome_transazione"]) ? $data["nome_transazione"] : null;
    $utente_id = isset($data["utente_id"]) ? $data["utente_id"] : null;
    $nuovo_nome = isset($data["nuovo_nome"]) ? $data["nuovo_nome"] : null;
    $nuovo_importo = isset($data["nuovo_importo"]) ? $data["nuovo_importo"] : null;
    $nuovo_tipo = isset($data["nuovo_tipo"]) ? $data["nuovo_tipo"] : null;

    if (!empty($nome_transazione) && !empty($utente_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // Preparare la query di aggiornamento
            $query = "UPDATE transazione 
                      SET TRANSAZIONE_Nome = ?, TRANSAZIONE_QTA = ?, TRANSAZIONE_Tipo = ?
                      WHERE TRANSAZIONE_Nome = ? AND UTENTE_FK_ID = ?";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sdssi', $nuovo_nome, $nuovo_importo, $nuovo_tipo, $nome_transazione, $utente_id);

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    echo json_encode(["message" => "Transazione aggiornata con successo."]);
                } else {
                    echo json_encode(["message" => "Nessuna modifica effettuata. Verifica i dati inviati."]);
                }
            } else {
                throw new Exception("Errore nell'esecuzione della query: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Tutti i campi richiesti (nome_transazione, utente_id) devono essere forniti."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito."]);
}
?>
