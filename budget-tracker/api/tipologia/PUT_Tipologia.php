<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    $nome_corrente = isset($data["nome_corrente"]) ? $data["nome_corrente"] : null;
    $nome = isset($data['nome']) ? $data['nome'] : null;
    $descrizione = isset($data['descrizione']) ? $data['descrizione'] : null;

    if (!empty($nome_corrente) && !empty($nome)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // Controllo se esiste già una tipologia con lo stesso nome
            $check_query = "SELECT * FROM tipologia WHERE TIPOLOGIA_Nome = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, 's', $nome);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            $check_result = mysqli_stmt_num_rows($check_stmt);

            if ($check_result > 0) {
                http_response_code(409);
                echo json_encode(array("message" => "Categoria con lo stesso nome già esistente."));
                mysqli_stmt_close($check_stmt);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($check_stmt);

            // Aggiornamento dei dati se tutto va bene prima
            $query = "UPDATE tipologia SET TIPOLOGIA_Nome = ?, TIPOLOGIA_Descrizione = ? WHERE TIPOLOGIA_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sss', $nome, $descrizione, $nome_corrente);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                http_response_code(200);
                echo json_encode(array("message" => "Tipologia aggiornata con successo."));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Errore durante l'aggiornamento della tipologia."));
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Nome corrente e nuovo nome sono richiesti."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}

?>
