<?php
session_start();
require_once __DIR__ . "/Funcionario.php";
require_once __DIR__ . "/Registro.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ponto.php"); exit;
}

$fid = filter_input(INPUT_POST, "funcionario_id", FILTER_VALIDATE_INT);

if (!$fid) {
    $_SESSION["error"] = "Funcionário inválido.";
    header("Location: ponto.php"); exit;
}

try {
    $func = new Funcionario();
    $reg  = new Registro();

    // Regra crítica: não pode ter ponto aberto
    if ($func->hasOpenRecord($fid)) {
        $_SESSION["error"] = "Este funcionário já tem um ponto aberto. Registre a saída primeiro.";
        header("Location: ponto.php"); exit;
    }

    $alvo = $func->findById($fid);
    $reg->criarEntrada($fid);

    $_SESSION["success"] = "Entrada registrada para <strong>" . htmlspecialchars($alvo["nome"]) . "</strong> às " . date("H:i") . ".";
} catch (PDOException $e) {
    $_SESSION["error"] = "Erro ao registrar entrada. Tente novamente.";
}

header("Location: ponto.php");
exit;
