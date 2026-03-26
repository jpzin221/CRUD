<?php

/**
 * Responsável por processar a atualização dos dados de um aluno.
 *
 * Melhorias em relação ao original:
 *  - Usa a classe User (evita SQL duplicado)
 *  - Valida o formato do e-mail com filter_var()
 *  - Exibe mensagem de sucesso/erro via sessão (flash message)
 *  - Trata exceções do banco de dados
 */

session_start();

require_once __DIR__ . "/User.php";

// Aceita apenas requisições POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Captura e valida o ID (deve ser inteiro positivo)
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

// Captura e sanitiza os demais campos
$name     = trim($_POST["name"]     ?? "");
$email    = trim($_POST["email"]    ?? "");
$document = trim($_POST["document"] ?? "");

// Validação: ID inválido
if (!$id || $id <= 0) {
    $_SESSION["error"] = "ID de aluno inválido.";
    header("Location: index.php");
    exit;
}

// Validação: campos obrigatórios
if ($name === "" || $email === "" || $document === "") {
    $_SESSION["error"] = "Preencha todos os campos antes de salvar.";
    header("Location: edit.php?id=" . $id);
    exit;
}

// Validação: formato de e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Informe um endereço de e-mail válido.";
    header("Location: edit.php?id=" . $id);
    exit;
}

// Tenta atualizar os dados do aluno
try {
    $user = new User();
    $user->update($id, $name, $email, $document);

    $_SESSION["success"] = "Dados de <strong>" . htmlspecialchars($name) . "</strong> atualizados com sucesso!";
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        $_SESSION["error"] = "Já existe outro aluno com esse e-mail.";
    } else {
        $_SESSION["error"] = "Erro ao atualizar. Tente novamente.";
    }
}

header("Location: index.php");
exit;
