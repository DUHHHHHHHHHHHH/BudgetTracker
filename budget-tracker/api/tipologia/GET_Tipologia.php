<?php
// get tipologia, fornisce una specifica tipologia pubblica tramite il NOME.

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $nome_tipologia = isset($_POST["nome_tipologia"]) ? $_POST["nome_tipologia"] : null;

    if (!empty($nome_tipologia)) {
        try {
        $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

        if (!$conn) { throw new Exception("Connection to the database failed: " . mysqli_connect_error()); }

        // SELEZIONO TUTTE LE TIPOLOGIE DEL DATABASE
        $query = "SELECT * FROM tipologia WHERE TIPOLOGIA_Nome = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $nome_tipologia);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt) === 0) echo json_encode(array("message" => "tipologie non presenti nel database"));

        if(mysqli_stmt_num_rows($stmt) > 0){
            mysqli_stmt_bind_result($stmt, $TIPOLOGIA_ID, $TIPOLOGIA_Nome, $TIPOLOGIA_Descrizione, $ADMIN_FK_ID);
            mysqli_stmt_fetch($stmt);

            $tipologia = array(
                "TIPOLOGIA_ID" => $TIPOLOGIA_ID,
                "TIPOLOGIA_Nome" => $TIPOLOGIA_Nome,
                "TIPOLOGIA_Descrizione" => $TIPOLOGIA_Descrizione,
                "ADMIN_FK_ID" => $ADMIN_FK_ID
            );

            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            echo json_encode($tipologia);
        }

        }catch(Exception $e) {
            http_response_code(401);
            echo json_encode(array("message" => $e->getMessage()));
        }

    }else{
        http_response_code(400);
        echo json_encode(array("message" => "Tutti i campi sono richiesti."));
    }

}
?>