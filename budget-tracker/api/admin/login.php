<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config.php";

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST["ADMIN_Username"]) ? $_POST["ADMIN_Username"] : null;
    $password = isset($_POST["ADMIN_Password"]) ? $_POST["ADMIN_Password"] : null;

    if (!empty($name) && !empty($password)) {
        try {
            $conn = mysqli_connect($db->host, $db->user, $db->password, $db->db_name);

            if (!$conn) {
                throw new Exception("Connection to the database failed: " . mysqli_connect_error(), 500);
            }

            $query = "SELECT ADMIN_ID, ADMIN_Username FROM ADMIN WHERE ADMIN_Username = ? AND ADMIN_Password = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ss', $name, $password);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $ADMIN_ID, $ADMIN_Username);
                mysqli_stmt_fetch($stmt);
                
                $user = array(

                    "message" => "Credenziali Valide.",
                    "code" => 200,
                    "ADMIN_ID" => $ADMIN_ID,
                    "ADMIN_Username" => $ADMIN_Username
                    
                );

                mysqli_stmt_close($stmt);
                    mysqli_close($conn);

                    echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Email non trovata.", "code" => 404));
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
    echo json_encode(array("message" => "Method not allowed.", "code" => 405));
}
?>