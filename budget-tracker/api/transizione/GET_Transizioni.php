<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $UTENTE_ID = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $CATEGORIA_Nome = isset($_POST["CATEGORIA_Nome"]) ? $_POST["CATEGORIA_Nome"] : null;
    $flagTipoGet = isset($_POST["flagTipoGet"]) ? $_POST["flagTipoGet"] : null;

    if (!empty($UTENTE_ID) && !empty($CATEGORIA_Nome)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // OTTENGO L'ID DELLA CATEGORIA
            $queryCategoria = "SELECT CATEGORIA_ID FROM categoria WHERE CATEGORIA_Nome = ?";
            $stmtCategoria = mysqli_prepare($conn, $queryCategoria);
            mysqli_stmt_bind_param($stmtCategoria, 's', $CATEGORIA_Nome);
            mysqli_stmt_execute($stmtCategoria);
            mysqli_stmt_store_result($stmtCategoria);

            if (mysqli_stmt_num_rows($stmtCategoria) > 0) {
                mysqli_stmt_bind_result($stmtCategoria, $CATEGORIA_ID);
                mysqli_stmt_fetch($stmtCategoria);
            } else {
                echo json_encode(array("message" => "Categoria non trovata", "code" => 404));
                mysqli_stmt_close($stmtCategoria);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($stmtCategoria);

            // flagTipoGet = 0 -> tutte le transazioni fatte 
            // flagTipoGet = 1 -> tutte le transazioni fatte su una specifica categoria

            if ($flagTipoGet == 1) {
                $query = "
                    SELECT 
                        t.TRANSIZIONE_ID, 
                        t.TRANSIZIONE_Nome, 
                        t.TRANSIZIONE_Data, 
                        t.TRANSIZIONE_DataGenerazione, 
                        t.TRANSIZIONE_QTA, 
                        t.TRANSIZIONE_Tipo, 
                        t.UTENTE_FK_ID, 
                        t.CATEGORIA_FK_ID
                    FROM 
                        transizione t
                    JOIN 
                        categoria c 
                    ON 
                        t.CATEGORIA_FK_ID = c.CATEGORIA_ID
                    WHERE 
                        t.UTENTE_FK_ID = ? AND c.CATEGORIA_Nome = ?";

                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'is', $UTENTE_ID, $CATEGORIA_Nome);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $transizioni = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $transizioni[] = $row;
                    }
                    echo json_encode($transizioni);
                } else {
                    echo json_encode(array("message" => "Nessuna transizione trovata per la categoria indicata."));
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit;
            } else {
                $query = "
                    SELECT 
                        TRANSIZIONE_ID, TRANSIZIONE_Nome, 
                        TRANSIZIONE_Data, TRANSIZIONE_DataGenerazione,
                        TRANSIZIONE_QTA, TRANSIZIONE_Tipo 
                    FROM 
                        transizione 
                    WHERE 
                        UTENTE_FK_ID = ?";

                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'i', $UTENTE_ID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $transizioni = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $transizioni[] = $row;
                    }
                    echo json_encode($transizioni);
                } else {
                    echo json_encode(array("message" => "Nessuna transizione trovata."));
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Valori mancanti", "code" => 400, "inseriti" => $UTENTE_ID . " " . $CATEGORIA_Nome . " " . $flagTipoGet]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito."]);
}

?>
