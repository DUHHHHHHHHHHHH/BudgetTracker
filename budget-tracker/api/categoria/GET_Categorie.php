<?php

// get categorie, restituisce tutte le categorie di un utente

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $utente_id = isset($_GET["utente_id"]) ? $_GET["utente_id"] : null;

    if (!empty($utente_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $query = "SELECT * FROM categoria WHERE UTENTE_FK_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $utente_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $categorie = [];
            while (mysqli_stmt_fetch($stmt)) {
                $categorie[] = array(
                    "CATEGORIA_ID" => $CATEGORIA_ID,
                    "CATEGORIA_Nome" => $CATEGORIA_Nome,
                    "CATEGORIA_Descrizione" => $CATEGORIA_Descrizione,
                    "CATEGORIA_Budget" => $CATEGORIA_Budget,
                    "TIPOLOGIA_FK_ID" => $TIPOLOGIA_FK_ID,
                    "UTENTE_FK_ID" => $UTENTE_FK_ID
                );
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            echo json_encode($categorie);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID utente richiesto."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}
?>
