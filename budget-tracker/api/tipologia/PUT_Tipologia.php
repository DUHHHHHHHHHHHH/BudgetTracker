<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // elementi primitivi passati da front end
    $nome_corrente = isset($_POST["TIPOLOGIA_Nome"]) ? $_POST["TIPOLOGIA_Nome"] : null;

    // elementi modificabili
    $newNome = isset($_POST['TIPOLOGIA_NewNome']) ? $_POST['TIPOLOGIA_NewNome'] : null;
    $newDescrizione = isset($_POST['TIPOLOGIA_NewDescrizione']) ? $_POST['TIPOLOGIA_NewDescrizione'] : null;

    if (!empty($nome_corrente)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // controllo se esiste la tipologia che utilizzo per cambiare quella nuova
            $check_query2 = "SELECT * FROM tipologia WHERE TIPOLOGIA_Nome = ?";
            $check_stmt2 = mysqli_prepare($conn, $check_query2);
            mysqli_stmt_bind_param($check_stmt2, 's', $nome_corrente);
            mysqli_stmt_execute($check_stmt2);
            mysqli_stmt_store_result($check_stmt2);
            $check_result2 = mysqli_stmt_num_rows($check_stmt2);

            if ($check_result2 === 0) {
                http_response_code(409);
                echo json_encode(array("message" => "Categoria cercata non esistente", "code" => 409));
                mysqli_stmt_close($check_stmt2);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($check_stmt2);

            // Controllo se esiste già una tipologia con lo stesso newNome
            $check_query = "SELECT * FROM tipologia WHERE TIPOLOGIA_Nome = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, 's', $newNome);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            $check_result = mysqli_stmt_num_rows($check_stmt);

                if ($check_result > 0) {
                    http_response_code(409);
                    echo json_encode(array("message" => "Categoria con lo stesso newNome già esistente.", "code" => 409));
                    mysqli_stmt_close($check_stmt);
                    mysqli_close($conn);
                    exit;
                }

                mysqli_stmt_close($check_stmt);

            // Aggiornamento dei dati se tutto va bene prima
            // Costruzione dinamica della query di aggiornamento

                    $updateFields = array();
                    $types = '';
                    $params = array();

                    if (!empty($newNome)) {
                        $updateFields[] = "TIPOLOGIA_Nome = ?";
                        $types .= 's';
                        $params[] = $newNome;
                    }
                    if (!empty($newDescrizione)) {
                        $updateFields[] = "TIPOLOGIA_Descrizione = ?";
                        $types .= 's';
                        $params[] = $newDescrizione;
                    }

            // controllo se ci sta almeno un campo da modificare

                    if (empty($updateFields)) {
                        http_response_code(400);
                        echo json_encode(array("message" => "Nessun campo da aggiornare fornito.", "code" => 400));
                        mysqli_close($conn);
                        exit;
                    }
            
            // Aggiornamento dei dati da modificare

                    $query = "UPDATE tipologia SET " . implode(", ", $updateFields) . " WHERE TIPOLOGIA_Nome = ?";
                    $types .= 's';
                    $params[] = $nome_corrente;

                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                    $result = mysqli_stmt_execute($stmt);

                    if ($result) {
                        http_response_code(200);
                        echo json_encode(array("message" => "Tipologia aggiornata con successo.", "code" => 200, "campi aggiornati" => $updateFields));
                    } else {
                        http_response_code(500);
                        echo json_encode(array("message" => "Errore durante l'aggiornamento della tipologia.", "code" => 500));
                    }

                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage()));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Nome corrente e nuovo newNome sono richiesti."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito."));
}

?>
