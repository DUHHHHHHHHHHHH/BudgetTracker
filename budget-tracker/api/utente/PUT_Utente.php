<?php

// update dell'utente, permette l'aggiornamento del nome, dell'email e della password.
// quando si fa l'update del nome oppure dell'email, bisogna controllare che entrambi non siano già usati

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = isset($_POST["mail"]) ? $_POST["mail"] : null;
    $username = isset($_POST["nome"]) ? $_POST["nome"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;

    if (!empty($mail) && !empty($username) && !empty($password)){
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

            $update_query = "UPDATE utente SET UTENTE_Username = ?, UTENTE_Password = ?, UTENTE_Mail = ? WHERE UTENTE_Mail = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);

            mysqli_stmt_bind_param($update_stmt, 'ssss', $username, $password, $mail, $mail);
            mysqli_stmt_execute($update_stmt);

            if (mysqli_stmt_affected_rows($update_stmt) > 0) {
                echo json_encode(array("message" => "Utente modificato con successo."));
            } else {
                echo json_encode(array("message" => "Nessuna modifica fatta o l'utente non è stato trovato."));
            }

            mysqli_stmt_close($update_stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
        
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Mancano dati."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}



?>