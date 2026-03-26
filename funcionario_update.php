<?php
session_start();
require_once __DIR__ . "/Funcionario.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: funcionarios.php"); exit;
}

$id      = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$nome    = trim($_POST["nome"]    ?? "");
$email   = trim($_POST["email"]   ?? "");
$cargo   = trim($_POST["cargo"]   ?? "");
$salario = (float) str_replace(",", ".", $_POST["salario"] ?? "0");

if (!$id || $nome === "") {
    $_SESSION["error"] = "Dados inválidos.";
    header("Location: funcionarios.php"); exit;
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Informe um e-mail válido.";
    header("Location: funcionario_edit.php?id=" . $id); exit;
}

try {
    $func = new Funcionario();
    $func->update($id, $nome, $email, $cargo, $salario);
    $_SESSION["success"] = "Dados de <strong>" . htmlspecialchars($nome) . "</strong> atualizados!";
} catch (PDOException $e) {
    $_SESSION["error"] = $e->getCode() === "23000"
        ? "Já existe outro funcionário com esse e-mail."
        : "Erro ao atualizar.";
}

header("Location: funcionarios.php");
exit;
