class Item {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function all($filters = []) {
        $sql = "SELECT * FROM itens";
        $clauses = [];
        $params = [];

        if (!empty($filters['q'])) {
            $clauses[] = "nome LIKE :q";
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['tipo'])) {
            $clauses[] = "tipo = :tipo";
            $params[':tipo'] = $filters['tipo'];
        }

        if ($clauses) $sql .= ' WHERE ' . implode(' AND ', $clauses);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM itens WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO itens (nome, tipo, quantidade) VALUES (:nome, :tipo, :quantidade)");
        $stmt->execute([
            ':nome' => $data['nome'],
            ':tipo' => $data['tipo'],
            ':quantidade' => $data['quantidade'] ?? 0,
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE itens SET nome = :nome, tipo = :tipo, quantidade = :quantidade WHERE id = :id");
        return $stmt->execute([
            ':nome' => $data['nome'],
            ':tipo' => $data['tipo'],
            ':quantidade' => $data['quantidade'] ?? 0,
            ':id' => $id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM itens WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}