<?php
session_start();
require_once __DIR__ . "/Funcionario.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: funcionarios.php"); exit;
}

$nome    = trim($_POST["nome"]    ?? "");
$email   = trim($_POST["email"]   ?? "");
$cargo   = trim($_POST["cargo"]   ?? "");
$salario = (float) str_replace(",", ".", $_POST["salario"] ?? "0");

if ($nome === "") {
    $_SESSION["error"] = "O nome do funcionário é obrigatório.";
    header("Location: funcionarios.php"); exit;
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Informe um e-mail válido.";
    header("Location: funcionarios.php"); exit;
}

try {
    $func = new Funcionario();
    $func->create($nome, $email, $cargo, $salario);
    $_SESSION["success"] = "Funcionário <strong>" . htmlspecialchars($nome) . "</strong> cadastrado!";
} catch (PDOException $e) {
    $_SESSION["error"] = $e->getCode() === "23000"
        ? "Já existe um funcionário com esse e-mail."
        : "Erro ao cadastrar funcionário.";
}

header("Location: funcionarios.php");
exit;
