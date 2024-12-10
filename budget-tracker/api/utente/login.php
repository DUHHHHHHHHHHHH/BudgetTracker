<?php
// login, permette di accedere alla parte privata tramite MAIL e PASSWORD.

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // prendo i valori dalla richiesta POST
    $mail = isset($_POST["UTENTE_Mail"]) ? $_POST["UTENTE_Mail"] : null;
    $password = isset($_POST["UTENTE_Password"]) ? $_POST["UTENTE_Password"] : null;

    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (!empty($mail) && !empty($password)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error(), 500);
            }

            $query = "SELECT UTENTE_Username, UTENTE_Mail, UTENTE_ID FROM utente WHERE UTENTE_Mail = ? AND UTENTE_Password = ?" ;
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ss', $mail, $password);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            // controllo se l'email esiste nel database

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $UTENTE_Username, $UTENTE_Mail, $UTENTE_ID);
                mysqli_stmt_fetch($stmt);

                // controllo se la password è corretta 

                    $user = array(

                        "message" => "Credenziali Valide.",
                        "code" => 200,
                        "UTENTE_Mail" => $UTENTE_Mail,
                        "UTENTE_Username" => $UTENTE_Username,
                        "UTENTE_ID" => $UTENTE_ID

                    );

                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);

                    echo json_encode($user);
                
            } else {
                http_response_code(416);
                echo json_encode(array("message" => "Email non trovata.", "code" => 416));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => "Internal Server Error", "code" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Dati inseriti non validi", "code" => 400));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Accesso non consentito.", "code" => 405, "method" => $_SERVER["REQUEST_METHOD"]));
}
?>