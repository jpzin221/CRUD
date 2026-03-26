<?php

require_once __DIR__ . "/connect.php";

/**
 * Classe responsável por manipular os dados da tabela `users`.
 *
 * Centraliza todas as operações de banco de dados relacionadas
 * a alunos: listagem, busca, cadastro, atualização e exclusão.
 *
 * Isso evita repetição de SQL espalhada pelos arquivos e
 * facilita a manutenção futura.
 */
class User
{
    /** @var PDO Conexão com o banco de dados */
    private PDO $pdo;

    /**
     * Ao criar um objeto User, a conexão é obtida automaticamente.
     */
    public function __construct()
    {
        $this->pdo = Connect::getInstance();
    }

    /**
     * Retorna todos os alunos cadastrados, ordenados pelo ID (crescente).
     *
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Busca um aluno pelo ID.
     *
     * @param int $id
     * @return array|false  Dados do aluno ou false se não encontrado
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = :id LIMIT 1"
        );
        $stmt->execute([":id" => $id]);

        return $stmt->fetch();
    }

    /**
     * Cadastra um novo aluno no banco de dados.
     *
     * @param string $name     Nome completo
     * @param string $email    Endereço de e-mail (único)
     * @param string $document Documento de identificação (CPF, RG, etc.)
     * @return bool            True se inserido com sucesso
     */
    public function create(string $name, string $email, string $document): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, document)
            VALUES (:name, :email, :document)
        ");

        return $stmt->execute([
            ":name"     => $name,
            ":email"    => $email,
            ":document" => $document,
        ]);
    }

    /**
     * Atualiza os dados de um aluno existente.
     *
     * @param int    $id
     * @param string $name
     * @param string $email
     * @param string $document
     * @return bool
     */
    public function update(int $id, string $name, string $email, string $document): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET name     = :name,
                email    = :email,
                document = :document
            WHERE id = :id
        ");

        return $stmt->execute([
            ":id"       => $id,
            ":name"     => $name,
            ":email"    => $email,
            ":document" => $document,
        ]);
    }

    /**
     * Remove um aluno do banco de dados pelo ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");

        return $stmt->execute([":id" => $id]);
    }
}
