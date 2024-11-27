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

        $query = "SELECT t.TIPOLOGIA_ID, t.TIPOLOGIA_Nome, t.TIPOLOGIA_Descrizione, t.ADMIN_FK_ID, a.ADMIN_Nome 
                  FROM tipologia t 
                  JOIN admin a ON t.ADMIN_FK_ID = a.ADMIN_ID";
        $result = mysqli_query($conn, $query);

        $tipologie = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $tipologie[] = array(
                "TIPOLOGIA_ID" => $row["TIPOLOGIA_ID"],
                "TIPOLOGIA_Nome" => $row["TIPOLOGIA_Nome"],
                "TIPOLOGIA_Descrizione" => $row["TIPOLOGIA_Descrizione"],
                "ADMIN_FK_ID" => $row["ADMIN_FK_ID"],
                "ADMIN_Nome" => $row["ADMIN_Nome"]
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
