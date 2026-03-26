<?php
/**
 * Gestão de Funcionários
 * Criado por: João Pedro Alves Rocha
 */
session_start();
require_once __DIR__ . "/Funcionario.php";

$success = $_SESSION["success"] ?? null;
$error   = $_SESSION["error"]   ?? null;
unset($_SESSION["success"], $_SESSION["error"]);

try {
    $func        = new Funcionario();
    $funcionarios = $func->all();
} catch (PDOException $e) {
    $error       = "Não foi possível conectar ao banco. Verifique connect.php.";
    $funcionarios = [];
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Funcionários · PontoApp</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once __DIR__ . "/nav.php"; ?>

<div class="page-wrapper">

  <?php if ($success): ?>
  <div class="flash flash-success">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
    <span><?= $success ?></span>
  </div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="flash flash-error">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
  <?php endif; ?>

  <!-- Formulário de Cadastro -->
  <div class="card">
    <p class="card-title">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      Cadastrar funcionário
    </p>
    <form action="funcionario_store.php" method="POST" novalidate>
      <div class="form-grid">
        <div class="form-group">
          <label for="nome">Nome completo *</label>
          <input type="text" id="nome" name="nome" placeholder="Ex.: Ana Lima" required>
        </div>
        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="Ex.: ana@empresa.com">
        </div>
        <div class="form-group">
          <label for="cargo">Cargo</label>
          <input type="text" id="cargo" name="cargo" placeholder="Ex.: Vendedor">
        </div>
        <div class="form-group">
          <label for="salario">Salário mensal (R$)</label>
          <input type="number" id="salario" name="salario" step="0.01" min="0" placeholder="Ex.: 2200.00">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Cadastrar
      </button>
    </form>
  </div>

  <!-- Lista -->
  <div class="section-heading">
    <h2>
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
      </svg>
      Equipe
    </h2>
    <span class="badge-count"><?= count($funcionarios) ?></span>
  </div>

  <?php if (empty($funcionarios)): ?>
  <div class="empty-state">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
    <p>Nenhum funcionário cadastrado ainda.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nome</th>
          <th>Cargo</th>
          <th>Salário</th>
          <th style="text-align:right">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($funcionarios as $f): ?>
        <tr>
          <td class="td-id"><?= $f["id"] ?></td>
          <td class="td-name">
            <div style="display:flex;align-items:center;gap:.625rem">
              <div class="avatar-sm"><?= mb_strtoupper(mb_substr($f["nome"],0,1)) ?></div>
              <?= htmlspecialchars($f["nome"]) ?>
            </div>
          </td>
          <td class="td-email"><?= htmlspecialchars($f["cargo"] ?: "—") ?></td>
          <td class="td-doc">
            <?= $f["salario_mensal"] > 0
                ? "R$ " . number_format($f["salario_mensal"], 2, ",", ".")
                : "—" ?>
          </td>
          <td>
            <div class="td-actions">
              <a href="funcionario_edit.php?id=<?= $f["id"] ?>" class="btn btn-sm btn-edit">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                Editar
              </a>
              <a href="funcionario_delete.php?id=<?= $f["id"] ?>"
                 class="btn btn-sm btn-delete"
                 onclick="return confirm('Remover <?= htmlspecialchars(addslashes($f["nome"])) ?>? Os registros de ponto serão excluídos junto.')">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                Remover
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
    &copy; <?= date('Y') ?> · Sistema de Controle de Ponto · Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>
</body>
</html>
