<?php


// get utente, fornisce un specifico utente tramite L'EMAIL

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = isset($_POST["mail"]) ? $_POST["mail"] : null;

    if (!empty($mail)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $query = "SELECT * FROM utente WHERE UTENTE_Mail = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 's', $mail);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $UTENTE_Mail, $UTENTE_Nome, $UTENTE_ID);
                mysqli_stmt_fetch($stmt);

                $user = array(
                    "UTENTE_Mail" => $UTENTE_Mail,
                    "UTENTE_Nome" => $UTENTE_Nome,
                    "UTENTE_ID" => $UTENTE_ID
                );

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                echo json_encode($user);
            } else {
                throw new Exception("Nome utente non valido.");
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