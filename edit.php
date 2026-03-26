<?php

/**
 * Página de edição de aluno.
 *
 * Melhorias realizadas:
 *  - Estrutura HTML5 completa (DOCTYPE, head, meta charset, title)
 *  - Usa User::findById() para buscar os dados do aluno
 *  - Redireciona com erro se ID inválido ou aluno não encontrado
 *  - Layout consistente com index.php
 *  - htmlspecialchars() em todos os outputs (proteção XSS)
 *  - Flash messages de erro via sessão
 */

session_start();

require_once __DIR__ . "/User.php";

// Recupera e limpa flash messages de erro (ex.: vindo do update.php)
$error = $_SESSION["error"] ?? null;
unset($_SESSION["error"]);

// Captura e valida o ID da URL (?id=N)
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    $_SESSION["error"] = "ID inválido para edição.";
    header("Location: index.php");
    exit;
}

// Busca o aluno no banco
try {
    $user  = new User();
    $aluno = $user->findById($id);
} catch (PDOException $e) {
    $_SESSION["error"] = "Erro ao carregar os dados do aluno.";
    header("Location: index.php");
    exit;
}

// Aluno não encontrado
if (!$aluno) {
    $_SESSION["error"] = "Aluno não encontrado.";
    header("Location: index.php");
    exit;
}

?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Aluno · CRUD</title>
  <meta name="description" content="Editar os dados do aluno no sistema CRUD.">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page-wrapper">

  <!-- Header -->
  <header class="site-header">
    <div class="brand-icon">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 3C7.03 3 3 5.69 3 9v10c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V9c0-3.31-4.03-6-9-6zm0 2c3.87 0 7 2.24 7 5s-3.13 5-7 5-7-2.24-7-5 3.13-5 7-5zm0 8c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM5 17v-1.13C6.45 16.6 9.1 17 12 17s5.55-.4 7-1.13V17H5z"/>
      </svg>
    </div>
    <div class="brand-text">
      <h1>Gerenciador de Alunos</h1>
      <p>Sistema CRUD em PHP · Desenvolvimento Web</p>
    </div>
  </header>

  <!-- Flash de erro -->
  <?php if ($error): ?>
  <div class="flash flash-error" role="alert">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
  <?php endif; ?>

  <!-- Título da página -->
  <p class="page-title">Editar aluno</p>
  <p class="page-subtitle">Atualize os dados de <strong><?= htmlspecialchars($aluno['name']) ?></strong> (ID #<?= $aluno['id'] ?>)</p>

  <!-- Formulário de edição -->
  <div class="card">
    <p class="card-title">
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
      </svg>
      Dados do aluno
    </p>

    <form action="update.php" method="POST" novalidate>
      <!-- ID oculto — necessário para identificar qual aluno atualizar -->
      <input type="hidden" name="id" value="<?= $aluno['id'] ?>">

      <div class="form-grid">
        <div class="form-group">
          <label for="name">Nome completo</label>
          <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($aluno['name']) ?>"
            required
            autocomplete="name"
          >
        </div>

        <div class="form-group">
          <label for="email">E-mail</label>
          <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($aluno['email']) ?>"
            required
            autocomplete="email"
          >
        </div>

        <div class="form-group">
          <label for="document">Documento (CPF / RG)</label>
          <input
            type="text"
            id="document"
            name="document"
            value="<?= htmlspecialchars($aluno['document']) ?>"
            required
          >
        </div>
      </div>

      <div style="display:flex; gap: 0.75rem; flex-wrap: wrap;">
        <button type="submit" class="btn btn-primary" id="btn-salvar">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
          </svg>
          Salvar alterações
        </button>

        <a href="index.php" class="btn btn-ghost">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
          </svg>
          Voltar
        </a>
      </div>
    </form>
  </div>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · CRUD de Alunos · Disciplina de Desenvolvimento Web
  </footer>

</div>

</body>
</html>
