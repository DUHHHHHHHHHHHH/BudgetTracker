<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

        if (!$conn) {
            throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
        }

        $query = "SELECT TIPOLOGIA_ID, TIPOLOGIA_Nome, TIPOLOGIA_Descrizione FROM TIPOLOGIA t ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        mysqli_stmt_bind_result($stmt, $tipologia_id, $tipologia_nome, $tipologia_descrizione);


        $tipologie = [];
        while (mysqli_stmt_fetch($stmt)) {
            $tipologie[] = array(
                "TIPOLOGIA_ID" => $tipologia_id,
                "TIPOLOGIA_Nome" => $tipologia_nome,
                "TIPOLOGIA_Descrizione" => $tipologia_descrizione
            );
        }
        mysqli_close($conn);
        echo json_encode($tipologie);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => $e->getMessage(), "code" => 200));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito.", "code" => 405));
}
?>
