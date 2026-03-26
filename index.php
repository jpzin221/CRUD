<?php

/**
 * Página principal – listagem e cadastro de alunos.
 *
 * Melhorias realizadas:
 *  - Estrutura HTML5 completa (DOCTYPE, head, meta charset, title)
 *  - Layout moderno com CSS próprio e Google Fonts
 *  - Flash messages de sucesso e erro via sessão
 *  - Usa a classe User para buscar os alunos
 *  - Proteção de saída com htmlspecialchars() (XSS)
 *  - Labels corrigidos (Documento em vez de Curso)
 */

session_start();

require_once __DIR__ . "/User.php";

// Recupera e limpa flash messages
$success = $_SESSION["success"] ?? null;
$error   = $_SESSION["error"]   ?? null;
unset($_SESSION["success"], $_SESSION["error"]);

// Busca todos os alunos
try {
    $user  = new User();
    $users = $user->all();
} catch (PDOException $e) {
    $error = "Não foi possível conectar ao banco de dados. Verifique as configurações em connect.php.";
    $users = [];
}

?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD de Alunos</title>
  <meta name="description" content="Sistema de gerenciamento de alunos – cadastro, edição e exclusão.">
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

  <!-- Flash Messages -->
  <?php if ($success): ?>
  <div class="flash flash-success" role="alert">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
    <span><?= $success ?></span>
  </div>
  <?php endif; ?>

  <?php if ($error): ?>
  <div class="flash flash-error" role="alert">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
  <?php endif; ?>

  <!-- Formulário de Cadastro -->
  <div class="card">
    <p class="card-title">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13 11h-2v3H8v2h3v3h2v-3h3v-2h-3zm1-9H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h8v5h5v11z"/></svg>
      Cadastrar novo aluno
    </p>

    <form action="store.php" method="POST" id="form-cadastro" novalidate>
      <div class="form-grid">
        <div class="form-group">
          <label for="name">Nome completo</label>
          <input
            type="text"
            id="name"
            name="name"
            placeholder="Ex.: João da Silva"
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
            placeholder="Ex.: joao@email.com"
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
            placeholder="Ex.: 123.456.789-00"
            required
          >
        </div>
      </div>

      <button type="submit" class="btn btn-primary" id="btn-cadastrar">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
        </svg>
        Cadastrar aluno
      </button>
    </form>
  </div>

  <!-- Lista de Alunos -->
  <div class="section-heading">
    <h2>
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
      Lista de alunos
    </h2>
    <span class="badge-count"><?= count($users) ?> aluno<?= count($users) !== 1 ? 's' : '' ?></span>
  </div>

  <?php if (empty($users)): ?>
  <div class="table-wrapper">
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-2.18c.07-.44.18-.87.18-1.33C18 2.1 15.9 0 13.33 0c-1.43 0-2.67.6-3.56 1.56L8 3.34 6.23 1.56C5.34.6 4.1 0 2.67 0 1.19 0-.09 1.19 0 2.67c0 .46.11.89.18 1.33H0v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6.67-4c.92 0 1.67.75 1.67 1.67s-.75 1.67-1.67 1.67-1.67-.75-1.67-1.67S12.41 2 13.33 2zM4 2.67C4 1.75 4.75 1 5.67 1S7.33 1.75 7.33 2.67 6.58 4.33 5.67 4.33 4 3.58 4 2.67zM20 20H4V8h16v12z"/></svg>
      <p>Nenhum aluno cadastrado ainda.<br>Use o formulário acima para começar.</p>
    </div>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th class="td-id">#</th>
          <th>Nome</th>
          <th>E-mail</th>
          <th>Documento</th>
          <th style="text-align:right">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td class="td-id"><?= $u['id'] ?></td>
          <td class="td-name"><?= htmlspecialchars($u['name']) ?></td>
          <td class="td-email"><?= htmlspecialchars($u['email']) ?></td>
          <td class="td-doc"><?= htmlspecialchars($u['document']) ?></td>
          <td>
            <div class="td-actions">
              <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-edit" title="Editar aluno">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                Editar
              </a>
              <a href="delete.php?id=<?= $u['id'] ?>"
                 class="btn btn-sm btn-delete"
                 title="Excluir aluno"
                 onclick="return confirm('Excluir o aluno <?= htmlspecialchars(addslashes($u['name'])) ?>? Esta ação não pode ser desfeita.')">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
                Excluir
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · CRUD de Alunos · Disciplina de Desenvolvimento Web
  </footer>

</div>

</body>
</html>
