<?php

// put categoria, modifica i dati di una categoria di un utente

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $nomeVecchio = isset($_POST['nomeVecchio']) ? $_POST['nomeVecchio'] : null;                                                
    $descrizione = isset($_POST['descrizione']) ? $_POST['descrizione'] : null;
    $utente_id = isset($_POST['utente_id']) ? $_POST['utente_id'] : null;

    if (!empty($nome) && !empty($utente_id) && !empty($nomeVecchio)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

        // ESISTE GIA' UNA CATEGORIA COL NOME CHE VOGLIO METTERE NEL MODIFICARE QUESTA CATEGORIA?
            $check_query = "SELECT * FROM categoria WHERE CATEGORIA_Nome = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, 's', $nome);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            $check_result = mysqli_stmt_num_rows($check_stmt);

            mysqli_stmt_close($check_stmt);
            
            if ($check_result > 0) {
                echo json_encode(array("message" => "Categoria con lo stesso nome già esistente."));
                http_response_code(409);
                mysqli_stmt_close($check_stmt);
                mysqli_close($conn);
                exit;
            }

        // Se non ci sono errori, PROCEDO CON L'UPDATE DEI CAMPI.
            $query = "UPDATE categoria SET CATEGORIA_Nome = ?, CATEGORIA_Descrizione = ? WHERE UTENTE_FK_ID = ? AND CATEGORIA_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssiS', $nome, $descrizione, $utente_id, $nomeVecchio);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                http_response_code(200);
                echo json_encode(array("message" => "Categoria modificata con successo."));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Categoria non trovata."));
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Tutti i campi sono richiesti."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}
?>
