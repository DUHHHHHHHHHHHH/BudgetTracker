<?php

// get utente, fornisce un specifico utente tramite L'EMAIL

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = isset($_POST["UTENTE_Mail"]) ? $_POST["UTENTE_Mail"] : null;

    if (!empty($mail)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $query = "SELECT UTENTE_ID, UTENTE_Username, UTENTE_Mail FROM utente WHERE UTENTE_Mail = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 's', $mail);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $UTENTE_ID, $UTENTE_Username, $UTENTE_Mail);
                mysqli_stmt_fetch($stmt);

                $user = array(

                    "message" => "utente trovato con successo!",
                    "code" => 200,
                    "UTENTE_Mail" => $UTENTE_Mail,
                    "UTENTE_Username" => $UTENTE_Username,
                    "UTENTE_ID" => $UTENTE_ID
                    
                );

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                echo json_encode($user);
            } else {
                http_response_code(404); // Usa il codice 404 per "non trovato"
                echo json_encode(array("message" => "Utente non trovato."));
            }
        } catch (Exception $e) {
            http_response_code(401);
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