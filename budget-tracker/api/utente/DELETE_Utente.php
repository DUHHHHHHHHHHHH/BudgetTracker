<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $email = isset($_POST["UTENTE_Mail"]) ? $_POST["UTENTE_Mail"] : null;
    $password = isset($_POST["UTENTE_Password"]) ? $_POST["UTENTE_Password"] : null;

    // Validate input
    if (empty($email) || empty($password) || empty($userId)) {
        http_response_code(400);
        echo json_encode(["message" => "compilare TUTTI i campi nel modo correto", "campi inseriti:" => $userId . " " . $email]);
        exit;
    }

    try {
        $conn = new mysqli($db->host, $db->user, $db->password, $db->db_name);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // CONTROLLO SE ESISTE L'UTENTE CON QUELL'ID

        $query = "SELECT * FROM utente WHERE UTENTE_ID = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$row) {
            echo json_encode(array("message" => "Utente non trovato.", "code" => 404));
            mysqli_close($conn);
            exit;
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
            echo json_encode(["message" => "utente eliminato con successo!", "code" => 200, "id eliminato" => $userId]);

            $deleteStmt->close();
        } else {
            http_response_code(401);
            echo json_encode(["message" => "credenziali errate o utente non trovato", "code" => 401]);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage(), "code" => 500]);
    }
} else {
    echo json_encode(["message" => "metodo non consentito", "code" => 405]);
}

?>