$DB_HOST = '127.0.0.1';
$DB_NAME = 'gerenciador';
$DB_USER = 'root';
$DB_PASS = ''; // ajuste conforme seu ambiente (XAMPP/Laragon)

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection error: ' . $e->getMessage()]);
    exit;
}