<?php

require_once __DIR__ . "/connect.php";

/**
 * Modelo responsável pela tabela `registros`.
 *
 * Gerencia o ciclo completo de um ponto:
 *  - Criar entrada (início do turno)
 *  - Registrar saída (fim + cálculo automático)
 *  - Buscar registro aberto de um funcionário
 *  - Histórico por período
 *  - Totais agregados por funcionário
 */
class Registro
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connect::getInstance();
    }

    /**
     * Cria um registro de entrada para o funcionário.
     * Deve ser chamado apenas se não há ponto aberto.
     */
    public function criarEntrada(int $funcionario_id): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO registros (funcionario_id, entrada)
            VALUES (:fid, NOW())
        ");
        return $stmt->execute([":fid" => $funcionario_id]);
    }

    /**
     * Registra a saída e calcula automaticamente a duração em horas decimais.
     * Ex: 7h30min → 7.50
     */
    public function registrarSaida(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE registros
            SET saida           = NOW(),
                duracao_decimal = ROUND(
                    TIMESTAMPDIFF(MINUTE, entrada, NOW()) / 60, 2
                )
            WHERE id = :id AND saida IS NULL
        ");
        return $stmt->execute([":id" => $id]);
    }

    /**
     * Retorna o registro em aberto de um funcionário.
     * Registro em aberto = saida IS NULL.
     *
     * @return array|false
     */
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

    /**
     * Retorna o histórico de registros de um funcionário
     * dentro de um intervalo de datas.
     */
    public function findByFuncionario(
        int    $funcionario_id,
        string $inicio,
        string $fim
    ): array {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM registros
            WHERE funcionario_id = :fid
              AND DATE(entrada) BETWEEN :inicio AND :fim
            ORDER BY entrada DESC
        ");
        $stmt->execute([
            ":fid"    => $funcionario_id,
            ":inicio" => $inicio,
            ":fim"    => $fim,
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Agrega totais por funcionário em um período.
     * Retorna: funcionario_id, nome, cargo, salario_mensal,
     *          dias_trabalhados, total_horas, estimativa_pagamento.
     *
     * Fórmula de estimativa:
     *   valor_hora = salario_mensal / 220
     *   estimativa = valor_hora × total_horas
     */
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
                  AND DATE(r.entrada) BETWEEN :inicio AND :fim
            GROUP BY f.id
            ORDER BY f.nome ASC
        ");
        $stmt->execute([":inicio" => $inicio, ":fim" => $fim]);
        return $stmt->fetchAll();
    }

    /**
     * Retorna todos os registros fechados de um funcionário
     * em um período (para o relatório detalhado).
     */
    public function historicoDetalhado(
        int    $funcionario_id,
        string $inicio,
        string $fim
    ): array {
        return $this->findByFuncionario($funcionario_id, $inicio, $fim);
    }
}
