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

        $query = "SELECT * FROM tipologia";
        $result = mysqli_query($conn, $query);

        $tipologie = [];
        while (mysqli_stmt_fetch($stmt)){
            $tipologie = array(
                "TIPOLOGIA_ID" => $TIPOLOGIA_ID,
                "TIPOLOGIA_Nome" => $TIPOLOGIA_Nome,
                "TIPOLOGIA_Descrizione" => $TIPOLOGIA_Descrizione,
                "ADMIN_FK_ID" => $ADMIN_FK_ID
            );
        }

        mysqli_close($conn);
        echo json_encode($tipologie);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => $e->getMessage()));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}

?>
