<?php
session_start();
require_once __DIR__ . "/Funcionario.php";
require_once __DIR__ . "/Registro.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ponto.php"); exit;
}

$fid = filter_input(INPUT_POST, "funcionario_id", FILTER_VALIDATE_INT);
$rid = filter_input(INPUT_POST, "registro_id",    FILTER_VALIDATE_INT);

if (!$fid || !$rid) {
    $_SESSION["error"] = "Dados inválidos para saída.";
    header("Location: ponto.php"); exit;
}

try {
    $func = new Funcionario();
    $reg  = new Registro();

    $alvo    = $func->findById($fid);
    $aberto  = $reg->findAberto($fid);

    if (!$aberto || $aberto["id"] != $rid) {
        $_SESSION["error"] = "Nenhum ponto aberto encontrado para este funcionário.";
        header("Location: ponto.php"); exit;
    }

    $reg->registrarSaida($rid);

    // Busca o registro atualizado para exibir a duração
    $stmt = Connect::getInstance()->prepare(
        "SELECT duracao_decimal FROM registros WHERE id = :id"
    );
    $stmt->execute([":id" => $rid]);
    $duracao = $stmt->fetchColumn();

    $h   = floor($duracao);
    $min = round(($duracao - $h) * 60);

    $_SESSION["success"] = "Saída registrada para <strong>" . htmlspecialchars($alvo["nome"]) . "</strong>. Duração: {$h}h{$min}min.";
} catch (PDOException $e) {
    $_SESSION["error"] = "Erro ao registrar saída. Tente novamente.";
}

header("Location: ponto.php");
exit;
