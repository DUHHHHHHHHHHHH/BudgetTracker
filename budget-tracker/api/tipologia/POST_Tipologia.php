<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

// L'ADMIN crea la TIPOLOGIA

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $descrizione = isset($_POST['descrizione']) ? $_POST['descrizione'] : null;


    if (!empty($nome) && !empty($descrizione)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // Controlla se esiste già una tipologia con lo stesso nome
            $check_query = "SELECT COUNT(*) FROM TIPOLOGIA WHERE TIPOLOGIA_Nome = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $nome);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);

            if ($count > 0) {
            // Esiste già una tipologia con lo stesso nome
            echo json_encode(array("message" => "Esiste già una tipologia con lo stesso nome."));
            exit;
            }

            // creazione della tipologia
            $insert_query = "INSERT INTO TIPOLOGIA (TIPOLOGIA_Nome, TIPOLOGIA_Descrizione) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "ss", $nome, $descrizione);


            if (!mysqli_stmt_execute($insert_stmt)) {
                throw new Exception("Errore durante l'inserimento dei dati: " . mysqli_error($conn));
            }

            mysqli_stmt_close($insert_stmt);
            mysqli_close($conn);

            echo json_encode(array("message" => "Prenotazione inserita con successo."));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => "Errore server - " . $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Dati di prenotazione non validi."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}
?>