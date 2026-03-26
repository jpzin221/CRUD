<?php

/**
 * Responsável por excluir um aluno pelo ID.
 *
 * Melhorias em relação ao original:
 *  - Usa a classe User (evita SQL duplicado)
 *  - Verifica se o aluno existe antes de excluir
 *  - Exibe mensagem de sucesso/erro via sessão (flash message)
 *  - Trata exceções do banco de dados
 */

session_start();

require_once __DIR__ . "/User.php";

// Captura e valida o ID recebido via GET (ex: delete.php?id=3)
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    $_SESSION["error"] = "ID de aluno inválido para exclusão.";
    header("Location: index.php");
    exit;
}

try {
    $user = new User();

    // Verifica se o aluno existe antes de tentar excluir
    $aluno = $user->findById($id);

    if (!$aluno) {
        $_SESSION["error"] = "Aluno não encontrado.";
        header("Location: index.php");
        exit;
    }

    $user->delete($id);

    $_SESSION["success"] = "Aluno <strong>" . htmlspecialchars($aluno["name"]) . "</strong> excluído com sucesso.";
} catch (PDOException $e) {
    $_SESSION["error"] = "Erro ao excluir o aluno. Tente novamente.";
}

header("Location: index.php");
exit;
