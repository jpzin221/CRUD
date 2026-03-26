<?php
require_once __DIR__ . "/connect.php";

/**
 * Modelo responsável pela tabela `registros` (Adaptado para SQLite).
 *
 * Em SQLite funções de data são tratadas levemente diferentes do MySQL.
 * `DATETIME('now', 'localtime')` é usado no lugar de `NOW()`.
 * Cálculo de tempo usa a diferença em `julianday` multiplicada por 24 para horas.
 */
class Registro
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connect::getInstance();
    }

    /** Cria entrada com o timestamp local do SQLite */
    public function criarEntrada(int $funcionario_id): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO registros (funcionario_id, entrada)
            VALUES (:fid, DATETIME('now', 'localtime'))
        ");
        return $stmt->execute([":fid" => $funcionario_id]);
    }

    /** Registra saída e calcula duração decimal via julianday() */
    public function registrarSaida(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE registros
            SET saida           = DATETIME('now', 'localtime'),
                duracao_decimal = ROUND(
                    (julianday(DATETIME('now', 'localtime')) - julianday(entrada)) * 24, 2
                )
            WHERE id = :id AND saida IS NULL
        ");
        return $stmt->execute([":id" => $id]);
    }

    /** Retorna registro aberto = saida IS NULL */
    public function findAberto(int $funcionario_id): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM registros
            WHERE funcionario_id = :fid
              AND saida IS NULL
            LIMIT 1
        ");
        $stmt->execute([":fid" => $funcionario_id]);
        return $stmt->fetch();
    }

    /** Histórico por período - SQLite trata TEXT (YYYY-MM-DD) natively nas comparações de string */
    public function findByFuncionario(int $funcionario_id, string $inicio, string $fim): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM registros
            WHERE funcionario_id = :fid
              AND DATE(entrada) BETWEEN DATE(:inicio) AND DATE(:fim)
            ORDER BY entrada DESC
        ");
        $stmt->execute([
            ":fid"    => $funcionario_id,
            ":inicio" => $inicio,
            ":fim"    => $fim." 23:59:59"
        ]);
        return $stmt->fetchAll();
    }

    /** Agrega totais usando coalesces compatíveis em SQLite */
    public function totaisPorPeriodo(string $inicio, string $fim): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                f.id                                        AS funcionario_id,
                f.nome,
                f.cargo,
                f.salario_mensal,
                COUNT(r.id)                                 AS dias_trabalhados,
                COALESCE(SUM(r.duracao_decimal), 0)         AS total_horas,
                ROUND(
                    (f.salario_mensal / 220) *
                    COALESCE(SUM(r.duracao_decimal), 0), 2
                )                                           AS estimativa_pagamento
            FROM funcionarios f
            LEFT JOIN registros r
                   ON r.funcionario_id = f.id
                  AND r.saida IS NOT NULL
                  AND DATE(r.entrada) BETWEEN DATE(:inicio) AND DATE(:fim)
            GROUP BY f.id
            ORDER BY f.nome ASC
        ");
        
        $stmt->execute([
            ":inicio" => $inicio, 
            ":fim" => $fim." 23:59:59"
        ]);
        return $stmt->fetchAll();
    }
}
