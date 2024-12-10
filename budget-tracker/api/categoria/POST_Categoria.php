<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

// l'utente può creare una nuova categoria, deve essere allegata perforza ad una tipologia e il nome deve essere UNIVOCO.

$database = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = isset($_POST['UTENTE_ID']) ? $_POST['UTENTE_ID'] : null;                                          // userID creatore della categoria
    $nome = isset($_POST['CATEGORIA_Nome']) ? $_POST['CATEGORIA_Nome'] : null;                                  // nome categoria
    $descrizione = isset($_POST['CATEGORIA_Descrizione']) ? $_POST['CATEGORIA_Descrizione'] : null;             // descrizione della categoria
    $budget = isset($_POST['CATEGORIA_Budget']) ? $_POST['CATEGORIA_Budget'] : 0;                               // budget è impostato a 0 se non modificato
    $nomeTipologiaAllegata = isset($_POST['nomeTipologiaAllegata']) ? $_POST['nomeTipologiaAllegata'] : null;   // tipologia allegata alla categoria

    if (!empty($userID) && !empty($nome) && !empty($descrizione) && !empty($nomeTipologiaAllegata)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                echo json_encode(["message" => "Connessione al database fallita: " . mysqli_connect_error(), "code" => 500]);
                // http_response_code(500);
                exit();
            }

        // ESISTE LA TIPOLOGIA PUBBLICA ALLEGATA?
        $check_tipologia_query = "SELECT TIPOLOGIA_ID FROM tipologia WHERE TIPOLOGIA_Nome = ?";
        $check_tipologia_stmt = mysqli_prepare($conn, $check_tipologia_query);
        mysqli_stmt_bind_param($check_tipologia_stmt, 's', $nomeTipologiaAllegata);
        mysqli_stmt_execute($check_tipologia_stmt);
        mysqli_stmt_store_result($check_tipologia_stmt);

        if (mysqli_stmt_num_rows($check_tipologia_stmt) == 0) {
            echo json_encode(["message" => "Tipologia non esistente", "code" => 400]);
            http_response_code(400);
            mysqli_stmt_close($check_tipologia_stmt);
            mysqli_close($conn);
            exit();
        }

        // OTTENIMENTO DELL'ID DELLA TIPOLOGIA per le query dopo
        mysqli_stmt_bind_result($check_tipologia_stmt, $tipologia_id);
        mysqli_stmt_fetch($check_tipologia_stmt);
        mysqli_stmt_close($check_tipologia_stmt);

        // CONTROLLO SE ESISTE GIA' UNA CATEGORIA CON LO STESSO NOME PER QUESTA TIPOLOGIA, I NOMI DEVONO ESSERE UNICI.
        $check_categoria_query = "SELECT COUNT(*) FROM categoria WHERE CATEGORIA_Nome = ? AND TIPOLOGIA_FK_ID = ? AND UTENTE_FK_ID = ?";
        $check_categoria_stmt = mysqli_prepare($conn, $check_categoria_query);
        mysqli_stmt_bind_param($check_categoria_stmt, 'sii', $nome, $tipologia_id, $userID);
        mysqli_stmt_execute($check_categoria_stmt);
        mysqli_stmt_bind_result($check_categoria_stmt, $categoria_count);
        mysqli_stmt_fetch($check_categoria_stmt);
        mysqli_stmt_close($check_categoria_stmt);

        if ($categoria_count > 0) {
            http_response_code(255);
            echo json_encode(["message" => "Esiste già una categoria con questo nome per la tipologia selezionata", "code" => 255]);
            mysqli_close($conn);
            exit();
            
        }

        // INSERIMENTO DELLA CATEGORIA
        $insert_query = "INSERT INTO categoria (CATEGORIA_Nome, CATEGORIA_Descrizione, CATEGORIA_Budget, UTENTE_FK_ID, TIPOLOGIA_FK_ID) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, 'ssdii', $nome, $descrizione, $budget, $userID, $tipologia_id);
        mysqli_stmt_execute($insert_stmt);

        if (mysqli_stmt_affected_rows($insert_stmt) > 0) {
            echo json_encode(array("message" => "Categoria creata con successo", "code" => 200));
            http_response_code(200);
        } else {
            echo json_encode(["message" => "Errore durante la creazione della categoria", "code" => 696]);
            http_response_code(696);
            mysqli_stmt_close($insert_stmt);
            mysqli_close($conn);
            exit();
        }

        mysqli_stmt_close($insert_stmt);
        mysqli_close($conn);

        } catch (Exception $e) {
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
