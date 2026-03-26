-- =====================================================
-- Sistema de Controle de Ponto
-- Criado por: João Pedro Alves Rocha
-- =====================================================

CREATE DATABASE IF NOT EXISTS aula01
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE aula01;

-- Tabela de funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
    id             INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome           VARCHAR(120) NOT NULL,
    email          VARCHAR(160) UNIQUE,
    cargo          VARCHAR(80)  DEFAULT '',
    salario_mensal DECIMAL(10,2) DEFAULT 0.00,
    created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de registros de ponto (entrada/saída)
-- Um registro em aberto = saida IS NULL
CREATE TABLE IF NOT EXISTS registros (
    id               INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    funcionario_id   INT         NOT NULL,
    entrada          DATETIME    NOT NULL,
    saida            DATETIME    NULL,
    duracao_decimal  DECIMAL(6,2) NULL, -- horas decimais (ex: 7.50 = 7h30min)
    FOREIGN KEY (funcionario_id)
        REFERENCES funcionarios(id)
        ON DELETE CASCADE
);

-- Dados de exemplo
INSERT INTO funcionarios (nome, email, cargo, salario_mensal) VALUES
    ('João Pedro Alves Rocha', 'joao@empresa.com', 'Desenvolvedor Web', 3000.00),
    ('Maria Silva',            'maria@empresa.com', 'Designer',          2500.00);
