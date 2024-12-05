<?php

// register, post per creare l'utente con MAIL, USERNAME, PASSWORD.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = isset($_POST['UTENTE_Mail']) ? $_POST['UTENTE_Mail'] : null;
    $username = isset($_POST['UTENTE_Username']) ? $_POST['UTENTE_Username'] : null;
    $password = isset($_POST['UTENTE_Password']) ? $_POST['UTENTE_Password'] : null;

    if (!empty($mail) && !empty($username) && !empty($password)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            $check_query = "SELECT UTENTE_Mail FROM utente WHERE UTENTE_Mail = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, 's', $mail);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);

            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                echo json_encode(array("message" => "La mail inserita è già presente nel database.", "code" => 401));
                exit;
            }

            // CONTROLLO SE L'UTENTE ESISTE GIA' 

            $check_query2 = "SELECT UTENTE_Username FROM utente WHERE UTENTE_Username = ?";
            $check_stmt2 = mysqli_prepare($conn, $check_query2);
            mysqli_stmt_bind_param($check_stmt2, 's', $username);
            mysqli_stmt_execute($check_stmt2);
            mysqli_stmt_store_result($check_stmt2);

            if (mysqli_stmt_num_rows($check_stmt2) > 0) {
                echo json_encode(array("message" => "L'username inserito è già presente nel database.", "code" => 401));
                exit;
            }
            

            $insert_query = "INSERT INTO utente (UTENTE_Mail, UTENTE_Username, UTENTE_Password) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, 'sss', $mail, $username, $password);

            if (!mysqli_stmt_execute($insert_stmt)) {
                echo json_encode(array("message" => "Errore durante l'inserimento dei dati.", "code" => 404));
            }

            mysqli_stmt_close($insert_stmt);
            mysqli_close($conn);

            echo json_encode(array("message" => "Dati inseriti con successo.", "code" => 200));
        } catch (Exception $e) {
            echo json_encode(array("message" => $e->getMessage(), "code" => 500));
        }
    } else {
        echo json_encode(array("message" => "Tutti i campi sono richiesti.", "code" => 400));
    }
} else {
    // Metodo non consentito
    echo json_encode(array("message" => "Metodo non consentito.", "code" => 405));
}
?>