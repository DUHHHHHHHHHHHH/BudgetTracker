<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

// l'utente può creare una nuova categoria, deve essere allegata perforza ad una tipologia e il nome deve essere UNIVOCO.

$database = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : null;                                          // username creatore della categoria
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;                                                      // nome categoria
    $descrizione = isset($_POST['descrizione']) ? $_POST['descrizione'] : null;                                 // descrizione della categoria
    $budget = isset($_POST['budget']) ? $_POST['budget'] : null;                                                // budget è impostato a 0 all'inizio
    $nomeTipologiaAllegata = isset($_POST['nomeTipologiaAllegata']) ? $_POST['nomeTipologiaAllegata'] : null;   // tipologia allegata alla categoria

    if (!empty($username) && !empty($nome) && !empty($descrizione) && !empty($budget) && !empty($nomeTipologiaAllegata)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                echo json_encode(["message" => "Connessione al database fallita: " . mysqli_connect_error()]);
            }

        // ESISTE LA TIPOLOGIA PUBBLICA ALLEGATA?
        $check_tipologia_query = "SELECT TIPOLOGIA_ID FROM tipologia WHERE TIPOLOGIA_Nome = ?";
        $check_tipologia_stmt = mysqli_prepare($conn, $check_tipologia_query);
        mysqli_stmt_bind_param($check_tipologia_stmt, 's', $nomeTipologiaAllegata);
        mysqli_stmt_execute($check_tipologia_stmt);
        mysqli_stmt_store_result($check_tipologia_stmt);

        if (mysqli_stmt_num_rows($check_tipologia_stmt) == 0) {
            echo json_encode(["message" => "Tipologia non esistente"]);
        }

        // OTTENIMENTO DELL'ID DELLA TIPOLOGIA
        mysqli_stmt_bind_result($check_tipologia_stmt, $tipologia_id);
        mysqli_stmt_fetch($check_tipologia_stmt);

        // CONTROLLO SE ESISTE GIA' UNA CATEGORIA CON LO STESSO NOME PER QUESTA TIPOLOGIA, I NOMI DEVONO ESSERE UNICI.
        $check_categoria_query = "SELECT COUNT(*) FROM categoria WHERE CATEGORIA_Nome = ? AND TIPOLOGIA_FK_ID = ?";
        $check_categoria_stmt = mysqli_prepare($conn, $check_categoria_query);
        mysqli_stmt_bind_param($check_categoria_stmt, 'si', $nome, $tipologia_id);
        mysqli_stmt_execute($check_categoria_stmt);
        mysqli_stmt_bind_result($check_categoria_stmt, $categoria_count);
        mysqli_stmt_fetch($check_categoria_stmt);
        mysqli_stmt_close($check_categoria_stmt);

        if ($categoria_count > 0) {
            echo json_encode(["message" => "Esiste già una categoria con questo nome per la tipologia selezionata"]);
        }

        // INSERIMENTO DELLA CATEGORIA
        $insert_query = "INSERT INTO categoria (CATEGORIA_Nome, CATEGORIA_Descrizione, CATEGORIA_Budget, UTENTE_FK_ID, TIPOLOGIA_FK_ID) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, 'ssdii', $nome, $descrizione, $budget, $username, $tipologia_id);
        mysqli_stmt_execute($insert_stmt);

        if (mysqli_stmt_affected_rows($insert_stmt) > 0) {
            echo json_encode(array("message" => "Categoria creata con successo"));
        } else {
            echo json_encode(["message" => "Errore durante la creazione della categoria"]);
        }

        mysqli_stmt_close($insert_stmt);
        mysqli_close($conn);

        } catch (Exception $e) {
            http_response_code(500);
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
