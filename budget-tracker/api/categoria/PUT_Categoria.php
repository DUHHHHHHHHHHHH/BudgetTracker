<?php

// put categoria, modifica i dati di una categoria di un utente

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $newNome = isset($_POST['CATEGORIA_newNome']) ? $_POST['CATEGORIA_newNome'] : null;
    $newDescrizione = isset($_POST['CATEGORIA_newDescrizione']) ? $_POST['CATEGORIA_newDescrizione'] : null;

    $utente_id = isset($_POST['UTENTE_ID']) ? $_POST['UTENTE_ID'] : null;               // passo da front end
    $categoria_id = isset($_POST['CATEGORIA_ID']) ? $_POST['CATEGORIA_ID'] : null;      // passo da front end

    if (!empty($utente_id)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error());
            }

        // OTTENGO DATI CATEGORIA
        $query = "SELECT * FROM categoria WHERE CATEGORIA_ID = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $categoria_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$row) {
            echo json_encode(array("message" => "Utente non trovato.", "code" => 404));
            mysqli_close($conn);
            exit;
        }

        // OTTENGO IL NOME DELLA CATEGORIA DAL RISULTATO DI PRIMA per usarlo dopo nell'update
        $CATEGORIA_Nome = $row['CATEGORIA_Nome'];

        // ESISTE GIA' UNA CATEGORIA COL NOME CHE VOGLIO METTERE NEL MODIFICARE QUESTA CATEGORIA?
            $check_query = "SELECT * FROM categoria WHERE CATEGORIA_Nome = ? AND UTENTE_FK_ID = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, 'si', $CATEGORIA_newNome, $utente_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            $check_result = mysqli_stmt_num_rows($check_stmt);
            
            if ($check_result > 0) {

                echo json_encode(array("message" => "Categoria con lo stesso nome già esistente.", "code" => 409, "C" => $CATEGORIA_Nome));;
                http_response_code(409);
                mysqli_stmt_close($check_stmt);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($check_stmt);

        // Se non ci sono errori, PROCEDO CON L'UPDATE DEI CAMPI.
                    // Costruisco la query dinamicamente in base ai parametri forniti
            $updateFields = array();
            $paramTypes = '';
            $paramValues = array();

            if (!empty($newNome)) {
                $updateFields[] = "CATEGORIA_Nome = ?";
                $paramTypes .= 's';
                $paramValues[] = $newNome;
            }

            if (!empty($newDescrizione)) {
                $updateFields[] = "CATEGORIA_Descrizione = ?";
                $paramTypes .= 's';
                $paramValues[] = $newDescrizione;
            }

            // Aggiungo i parametri WHERE
            $paramTypes .= 'si';
            $paramValues[] = $utente_id;
            $paramValues[] = $CATEGORIA_Nome;

            if (!empty($updateFields)) {
                $query3 = "UPDATE categoria SET " . implode(", ", $updateFields) . " WHERE UTENTE_FK_ID = ? AND CATEGORIA_Nome = ?";
                $stmt3 = mysqli_prepare($conn, $query3);

                // Creo array di riferimenti per bind_param
                $params = array($paramTypes);
                foreach ($paramValues as $key => $value) {
                    $params[] = &$paramValues[$key];
                }
                call_user_func_array(array($stmt3, 'bind_param'), $params);

                mysqli_stmt_execute($stmt3);

            if (mysqli_stmt_affected_rows($stmt3) > 0) {

                http_response_code(200);
                echo json_encode(
                    
                array(
                        "message" => "Categoria modificata con successo.", 
                        "code" => 200, 
                        "modifiche" => array(
                            "righe_modificate" => mysqli_stmt_affected_rows($stmt),
                            "campi_aggiornati" => $updateFields
                        )
                    )
                );

            } else {
                http_response_code(404);
                echo json_encode(array( "message" => "Update non effettuato.", "code" => 404, "campi" => $_POST));
            }

                mysqli_stmt_close($stmt3);
                mysqli_close($conn);
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Nessun campo da aggiornare fornito.", "code" => 400));
                mysqli_close($conn);
            }        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => $e->getMessage(), "code" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Tutti i campi sono richiesti.", "code" => 400, "campi" => $_POST));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Metodo non consentito.", "code" => 500));
}
?>
