<?php

require_once __DIR__ . "/connect.php";

/**
 * Modelo responsável pela tabela `funcionarios`.
 *
 * Operações disponíveis:
 *  - Listar todos
 *  - Buscar por ID
 *  - Verificar se tem ponto aberto
 *  - Criar / Atualizar / Excluir
 */
class Funcionario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connect::getInstance();
    }

    /** Retorna todos os funcionários ordenados pelo nome. */
    public function all(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM funcionarios ORDER BY nome ASC"
        );
        return $stmt->fetchAll();
    }

    /** Busca funcionário pelo ID. */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM funcionarios WHERE id = :id LIMIT 1"
        );
        $stmt->execute([":id" => $id]);
        return $stmt->fetch();
    }

    /**
     * Verifica se o funcionário tem um registro de ponto em aberto.
     * Um registro em aberto é aquele onde saida IS NULL.
     *
     * @return bool true = está trabalhando agora
     */
    public function hasOpenRecord(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM registros
             WHERE funcionario_id = :id AND saida IS NULL"
        );
        $stmt->execute([":id" => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Cadastra um novo funcionário. */
    public function create(
        string $nome,
        string $email,
        string $cargo,
        float  $salario
    ): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO funcionarios (nome, email, cargo, salario_mensal)
            VALUES (:nome, :email, :cargo, :salario)
        ");
        return $stmt->execute([
            ":nome"    => $nome,
            ":email"   => $email,
            ":cargo"   => $cargo,
            ":salario" => $salario,
        ]);
    }

    /** Atualiza dados de um funcionário. */
    public function update(
        int    $id,
        string $nome,
        string $email,
        string $cargo,
        float  $salario
    ): bool {
        $stmt = $this->pdo->prepare("
            UPDATE funcionarios
            SET nome           = :nome,
                email          = :email,
                cargo          = :cargo,
                salario_mensal = :salario
            WHERE id = :id
        ");
        return $stmt->execute([
            ":id"      => $id,
            ":nome"    => $nome,
            ":email"   => $email,
            ":cargo"   => $cargo,
            ":salario" => $salario,
        ]);
    }

    /** Remove um funcionário (registros são removidos por CASCADE). */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM funcionarios WHERE id = :id"
        );
        return $stmt->execute([":id" => $id]);
    }
}
