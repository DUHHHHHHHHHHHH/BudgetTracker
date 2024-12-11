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

    // utente passato dal front end.
    $utente_id = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $password = isset($_POST["UTENTE_Password"]) ? $_POST["UTENTE_Password"] : null;

    // elementi modificabili
    $newUsername = isset($_POST["UTENTE_NewUsername"]) ? $_POST["UTENTE_NewUsername"] : null;
    $newMail = isset($_POST["UTENTE_NewMail"]) ? $_POST["UTENTE_NewMail"] : null;
    $newPassword = isset($_POST["UTENTE_NewPassword"]) ? $_POST["UTENTE_NewPassword"] : null;

    if (!empty($utente_id)){
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

        // OTTENGO DATI UTENTE

        $query = "SELECT * FROM utente WHERE UTENTE_ID = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $utente_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$row) {
            echo json_encode(array("message" => "Utente non trovato.", "code" => 404));
            mysqli_close($conn);
            exit;
        }
                // CONTROLLO SE LA PASSWORD INSERITA E' GIUSTA.

                $check_query = "SELECT * FROM utente WHERE UTENTE_ID = ? AND UTENTE_Password = ?";
                $check_stmt2 = mysqli_prepare($conn, $check_query);
                mysqli_stmt_bind_param($check_stmt2, 'is', $utente_id, $password);
                mysqli_stmt_execute($check_stmt2);
                $result = mysqli_stmt_get_result($check_stmt2);

                if (mysqli_num_rows($result) == 0) {
                    echo json_encode(array("message" => "Password errata.", "code" => 401));
                    mysqli_stmt_close($check_stmt);
                    mysqli_close($conn);
                    exit;
                }

                mysqli_stmt_close($check_stmt2);
                
        // CONTROLLO SE USERNAME nuovo o MAIL nuovo sono già utilizzati.

        if(!empty($newUsername) && !empty($newMail)) {

            $check_query = "SELECT * FROM utente WHERE (UTENTE_Username = ? OR UTENTE_Mail = ?) AND UTENTE_Mail != ?";
            $check_stmt3 = mysqli_prepare($conn, $check_query);

            mysqli_stmt_bind_param($check_stmt3, 'sss', $UTENTE_NewUsername, $UTENTE_NewMail, $UTENTE_NewMail);
            mysqli_stmt_execute($check_stmt3);
            $result = mysqli_stmt_get_result($check_stmt3);

            if (mysqli_num_rows($result) > 0) {
                // Se esiste un altro utente con lo stesso username o email
                echo json_encode(array("message" => "USERNAME o MAIL già in uso da un altro utente, riprova."));
                http_response_code(409);
                mysqli_stmt_close($check_stmt3);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($check_stmt3);
        }

        // Se non ci sono errori, PROCEDO CON L'UPDATE

                    $update_fields = array();
                    $param_types = '';
                    $param_values = array();

                    if (!empty($newUsername)) {
                        $update_fields[] = "UTENTE_Username = ?";
                        $param_types .= 's';
                        $param_values[] = $newUsername;
                    }

                    if (!empty($newMail)) {
                        $update_fields[] = "UTENTE_Mail = ?";
                        $param_types .= 's';
                        $param_values[] = $newMail;
                    }

                    if (!empty($newPassword)) {
                        $update_fields[] = "UTENTE_Password = ?";
                        $param_types .= 's';
                        $param_values[] = $newPassword;
                    }

            if (!empty($update_fields)) {
                $update_query = "UPDATE utente SET " . implode(', ', $update_fields) . " WHERE UTENTE_ID = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);

                $param_types .= 'i';
                $param_values[] = $utente_id;

                mysqli_stmt_bind_param($update_stmt, $param_types, ...$param_values);
                mysqli_stmt_execute($update_stmt);

                if (mysqli_stmt_affected_rows($update_stmt) > 0) {
                                    echo json_encode(array(
                                        "message" => "Utente modificato con successo",
                                        "code" => 200,
                                        "modifiche" => array(
                                            "righe_modificate" => mysqli_stmt_affected_rows($update_stmt),
                                            "campi_aggiornati" => $update_fields
                                        )
                                    ));
                } else {
                    echo json_encode(array("message" => "Nessuna modifica fatta o l'utente non è stato trovato.", "code" => 404, "campi_inseriti" => $_POST));
                }

                mysqli_stmt_close($update_stmt);
            } else {
                echo json_encode(array("message" => "Nessun campo da aggiornare specificato."));
            }
            
                    mysqli_close($conn);        
                } catch (Exception $e) {
            echo json_encode(array("message" => $e->getMessage(), "code" => 500));
        }
        
    } else {
        echo json_encode(array("message" => "Mancano dati.", "code" => 400));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}



?>