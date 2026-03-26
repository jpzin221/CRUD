<?php

/**
 * Classe responsável por criar e fornecer
 * uma conexão com o banco de dados (Singleton).
 *
 * Utilizamos o padrão Singleton para garantir que apenas
 * uma instância de conexão seja criada durante toda a requisição,
 * evitando conexões desnecessárias ao banco de dados.
 */
class Connect
{
    /**
     * Dados de configuração da conexão.
     * Altere conforme seu ambiente local.
     */
    private const HOST   = "localhost";
    private const DBNAME = "aula01";
    private const USER   = "root";
    private const PASS   = "";

    /**
     * Retorna a única instância PDO de conexão com o banco.
     *
     * @return PDO
     * @throws PDOException em caso de falha na conexão
     */
    public static function getInstance(): PDO
    {
        static $instance = null;

        if ($instance === null) {
            $dsn = "mysql:host=" . self::HOST
                 . ";dbname=" . self::DBNAME
                 . ";charset=utf8mb4";

            $instance = new PDO($dsn, self::USER, self::PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // melhoria: prepares reais
            ]);
        }

        return $instance;
    }
}
