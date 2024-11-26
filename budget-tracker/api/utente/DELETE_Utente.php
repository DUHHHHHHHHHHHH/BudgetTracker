<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['mail'];
    $password = $data['password'];
    $userId = $data['UTENTE_ID'];

    // Validate input
    if (empty($email) || empty($password) || empty($userId)) {
        http_response_code(400);
        echo json_encode(["message" => "compilare TUTTI i campi"]);
        exit;
    }

    try {
        $conn = new mysqli($db->host, $db->user, $db->password, $db->db_name);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // CONTROLLO CREDENZIALI
        $stmt = $conn->prepare("SELECT UTENTE_ID FROM utente WHERE UTENTE_Mail = ? AND UTENTE_Password = ? AND UTENTE_ID = ?");
        $stmt->bind_param("ssi", $email, $password, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Delete the user
            $deleteStmt = $conn->prepare("DELETE FROM utente WHERE UTENTE_ID = ?");
            $deleteStmt->bind_param("i", $userId);
            $deleteStmt->execute();

            http_response_code(200);
            echo json_encode(["message" => "utente eliminato con successo"]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "credenziali errate"]);
        }

        $stmt->close();
        $deleteStmt->close();
        $conn->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "metodo non consentito"]);
}

?>