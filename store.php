<?php

/**
 * Responsável por receber e processar o formulário de cadastro de aluno.
 *
 * Melhorias em relação ao original:
 *  - Usa a classe User (evita SQL duplicado)
 *  - Valida o formato do e-mail com filter_var()
 *  - Exibe mensagem de erro ou sucesso via sessão (flash message)
 *  - Trata exceções do banco de dados
 */

session_start();

require_once __DIR__ . "/User.php";

// Aceita apenas requisições POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Captura e sanitiza os dados do formulário
$name     = trim($_POST["name"]     ?? "");
$email    = trim($_POST["email"]    ?? "");
$document = trim($_POST["document"] ?? "");

// Validação: campos obrigatórios
if ($name === "" || $email === "" || $document === "") {
    $_SESSION["error"] = "Preencha todos os campos antes de cadastrar.";
    header("Location: index.php");
    exit;
}

// Validação: formato de e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Informe um endereço de e-mail válido.";
    header("Location: index.php");
    exit;
}

// Tenta cadastrar o aluno
try {
    $user = new User();
    $user->create($name, $email, $document);

    $_SESSION["success"] = "Aluno <strong>" . htmlspecialchars($name) . "</strong> cadastrado com sucesso!";
} catch (PDOException $e) {
    // Verifica se é erro de e-mail duplicado (código MySQL 23000)
    if ($e->getCode() === "23000") {
        $_SESSION["error"] = "Já existe um aluno cadastrado com esse e-mail.";
    } else {
        $_SESSION["error"] = "Erro ao cadastrar o aluno. Tente novamente.";
    }
}

header("Location: index.php");
exit;
