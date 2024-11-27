<?php

// delete categoria, elimina una categoria di un utente

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    $utente_id = isset($data["utente_id"]) ? $data["utente_id"] : null;
    $nome_categoria = isset($data["nome_categoria"]) ? $data["nome_categoria"] : null;

    if (!empty($utente_id) && !empty($nome_categoria)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $query = "DELETE FROM categoria WHERE UTENTE_ID = ? AND CATEGORIA_Nome = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'is', $utente_id, $nome_categoria);

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(array("message" => "Categoria eliminata con successo."));
            } else {
                throw new Exception("Errore nell'eliminazione della categoria.");
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
