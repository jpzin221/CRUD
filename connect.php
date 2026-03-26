<?php
/**
 * PontoApp - Conexão e Inicialização do Banco de Dados SQLite
 * Criado por: João Pedro Alves Rocha
 * 
 * Usamos SQLite embarcado no projeto. Não é necessário MySQL.
 * O arquivo database.sqlite será gerado automaticamente.
 */
class Connect
{
    public static function getInstance(): PDO
    {
        static $instance = null;

        if ($instance === null) {
            $dbFile = __DIR__ . "/database.sqlite";
            $isNewDb = !file_exists($dbFile);

            $dsn = "sqlite:" . $dbFile;

            $instance = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Se o arquivo SQLite acabou de ser criado, roda o schema inicial
            if ($isNewDb) {
                self::initSchema($instance);
            }
        }

        return $instance;
    }

    /**
     * Cria as tabelas necessárias automaticamente.
     */
    private static function initSchema(PDO $pdo): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS funcionarios (
                id             INTEGER PRIMARY KEY AUTOINCREMENT,
                nome           VARCHAR(120) NOT NULL,
                email          VARCHAR(160) UNIQUE,
                cargo          VARCHAR(80)  DEFAULT '',
                salario_mensal DECIMAL(10,2) DEFAULT 0.00,
                created_at     DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS registros (
                id               INTEGER PRIMARY KEY AUTOINCREMENT,
                funcionario_id   INTEGER NOT NULL,
                entrada          DATETIME NOT NULL,
                saida            DATETIME NULL,
                duracao_decimal  DECIMAL(6,2) NULL,
                FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE
            );
            
            INSERT INTO funcionarios (nome, email, cargo, salario_mensal) VALUES
                ('João Pedro Alves Rocha', 'joao@empresa.com', 'Desenvolvedor Web', 3000.00),
                ('Maria Silva',            'maria@empresa.com', 'Designer',          2500.00);
        ";
        
        $pdo->exec($sql);
    }
}
