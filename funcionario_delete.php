<?php
session_start();
require_once __DIR__ . "/Funcionario.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "ID inválido.";
    header("Location: funcionarios.php"); exit;
}

try {
    $func  = new Funcionario();
    $alvo  = $func->findById($id);

    if (!$alvo) {
        $_SESSION["error"] = "Funcionário não encontrado.";
        header("Location: funcionarios.php"); exit;
    }

    $func->delete($id);
    $_SESSION["success"] = "Funcionário <strong>" . htmlspecialchars($alvo["nome"]) . "</strong> removido.";
} catch (PDOException $e) {
    $_SESSION["error"] = "Erro ao remover funcionário.";
}

header("Location: funcionarios.php");
exit;
