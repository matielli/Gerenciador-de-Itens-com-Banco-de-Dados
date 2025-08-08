header('Content-Type: application/json');
// Habilitar CORS para desenvolvimento local (ajuste em produção)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Item.php';

$itemModel = new Item($pdo);

$method = $_SERVER['REQUEST_METHOD'];

// recebe id via querystring: ?id=1
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

$input = json_decode(file_get_contents('php://input'), true) ?: [];

try {
    if ($method === 'GET') {
        if ($id) {
            $item = $itemModel->find($id);
            if ($item) echo json_encode(['success' => true, 'data' => $item]);
            else { http_response_code(404); echo json_encode(['success' => false, 'error' => 'Item not found']); }
        } else {
            $filters = [];
            if (isset($_GET['q'])) $filters['q'] = $_GET['q'];
            if (isset($_GET['tipo'])) $filters['tipo'] = $_GET['tipo'];
            $items = $itemModel->all($filters);
            echo json_encode(['success' => true, 'data' => $items]);
        }
    } elseif ($method === 'POST') {
        // validar dados mínimos
        if (empty($input['nome']) || empty($input['tipo'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Campos obrigatórios ausentes']);
            exit;
        }
        $newId = $itemModel->create($input);
        http_response_code(201);
        echo json_encode(['success' => true, 'id' => $newId]);
    } elseif ($method === 'PUT') {
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'error' => 'ID é obrigatório para PUT']); exit; }
        $exists = $itemModel->find($id);
        if (!$exists) { http_response_code(404); echo json_encode(['success' => false, 'error' => 'Item não encontrado']); exit; }
        $itemModel->update($id, $input);
        echo json_encode(['success' => true]);
    } elseif ($method === 'DELETE') {
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'error' => 'ID é obrigatório para DELETE']); exit; }
        $exists = $itemModel->find($id);
        if (!$exists) { http_response_code(404); echo json_encode(['success' => false, 'error' => 'Item não encontrado']); exit; }
        $itemModel->delete($id);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
