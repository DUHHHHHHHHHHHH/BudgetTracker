<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utente_id = isset($_POST["UTENTE_ID"]) ? $_POST["UTENTE_ID"] : null;
    $categoria_nome = isset($_POST["CATEGORIA_Nome"]) ? $_POST["CATEGORIA_Nome"] : null;
    $flagTipoGet = isset($_POST["flagTipoGet"]) ? $_POST["flagTipoGet"] : null;

    if (!empty($utente_id) && !empty($categoria_nome)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
            }

            // OTTENGO L'ID DELLA CATEGORIA
            $queryCategoria = "SELECT CATEGORIA_ID FROM categoria WHERE CATEGORIA_Nome = ? AND UTENTE_FK_ID = ?";
            $stmtCategoria = mysqli_prepare($conn, $queryCategoria);
            mysqli_stmt_bind_param($stmtCategoria, 'si', $categoria_nome, $utente_id);
            mysqli_stmt_execute($stmtCategoria);
            mysqli_stmt_store_result($stmtCategoria);

            if (mysqli_stmt_num_rows($stmtCategoria) > 0) {
                mysqli_stmt_bind_result($stmtCategoria, $CATEGORIA_ID);
                mysqli_stmt_fetch($stmtCategoria);
            } else {
                echo json_encode(array("message" => "Categoria non trovata", "code" => 404, "post" => $stmtCategoria));
                mysqli_stmt_close($stmtCategoria);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($stmtCategoria);

            // flagTipoGet = 0 -> tutte le milestones fatte 
            // flagTipoGet = 1 -> tutte le milestones fatte su una specifica categoria

            if ($flagTipoGet == 1) {
                $query = "
                    SELECT 
                        m.MILESTONE_ID,
                        m.MILESTONE_Nome,
                        m.MILESTONE_Descrizione,
                        m.MILESTONE_DataInizio,
                        m.MILESTONE_DataFine,
                        m.MILESTONE_Completata,
                        m.UTENTE_FK_ID,
                        m.CATEGORIA_FK_ID
                    FROM 
                        milestone m
                    JOIN 
                        categoria c 
                    ON 
                        m.CATEGORIA_FK_ID = c.CATEGORIA_ID
                    WHERE 
                        m.UTENTE_FK_ID = ? AND c.CATEGORIA_Nome = ?";

                
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'is', $utente_id, $categoria_nome);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $milestones = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $milestones[] = $row;
                    }
                    echo json_encode($milestones);


                } else {
                    echo json_encode(array("message" => "Nessuna milestone trovata per la categoria indicata.", "code" => 404));
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit;
            } else {
                $query = "
                    SELECT 
                        MILESTONE_ID,
                        MILESTONE_Nome,
                        MILESTONE_Descrizione,
                        MILESTONE_DataInizio,
                        MILESTONE_DataFine,
                        MILESTONE_Completata
                    FROM 
                        milestone 
                    WHERE 
                        UTENTE_FK_ID = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'i', $utente_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $milestones = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $milestones[] = $row;
                    }
                    echo json_encode($milestones);
                } else {
                    echo json_encode(array("message" => "Nessuna milestone trovata.", "code" => 404));
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit;
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $milestones = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $milestones[] = $row;
            }

            echo json_encode($milestones);

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage(), "code" => 500]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID utente richiesto.", "code" => 400]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Metodo non consentito.", "code" => 405]);
}
?>
