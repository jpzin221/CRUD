<?php
/**
 * Gestão de Funcionários - Versão SaaS Card List
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
    $error       = "Erro interno: " . $e->getMessage();
    $funcionarios = [];
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Equipe · PontoApp</title>
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
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 15h2v2h-2zm0-8h2v6h-2zm1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
  <?php endif; ?>

  <p class="page-title">Funcionários</p>
  <p class="page-subtitle">Gerencie o elenco e remuneração da equipe.</p>

  <!-- Cadastro novo funcionário (SaaS Card Form) -->
  <div class="card">
    <h2 class="card-title">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      Novo Funcionário
    </h2>
    <form action="funcionario_store.php" method="POST" novalidate>
      <div class="form-grid">
        <div class="form-group">
          <label for="nome">Nome completo *</label>
          <input type="text" id="nome" name="nome" class="input" placeholder="Ex.: Ana Lima" required>
        </div>
        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" class="input" placeholder="ana@empresa.com">
        </div>
        <div class="form-group">
          <label for="cargo">Cargo</label>
          <input type="text" id="cargo" name="cargo" class="input" placeholder="Ex.: Analista">
        </div>
        <div class="form-group">
          <label for="salario">Salário Base (Mensal)</label>
          <input type="number" id="salario" name="salario" class="input" step="0.01" min="0" placeholder="R$ 3.500,00">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Cadastrar
      </button>
    </form>
  </div>

  <!-- Lista de Funcionários convertida pra Employee Cards -->
  <br>
  
  <?php if (empty($funcionarios)): ?>
  <div class="empty-state">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
    <p>A equipe ainda está vazia.</p>
  </div>
  <?php else: ?>
  <div class="employee-list">
    <?php foreach ($funcionarios as $f): ?>
    <div class="employee-card">
      <div class="emp-info">
        <div class="avatar"><?= mb_strtoupper(mb_substr($f["nome"],0,1)) ?></div>
        <div>
          <div class="emp-name"><?= htmlspecialchars($f["nome"]) ?></div>
          <div class="emp-role"><?= htmlspecialchars($f["cargo"] ?: "Sem cargo") ?></div>
        </div>
      </div>
      
      <div class="emp-meta">
        <?= $f["salario_mensal"] > 0
            ? "R$ " . number_format((float)$f["salario_mensal"], 2, ",", ".")
            : '<span style="color:var(--text-muted);font-weight:400;font-family:sans-serif;font-size:0.85rem">Sem salário base</span>' ?>
      </div>
      
      <div class="emp-actions">
        <a href="funcionario_edit.php?id=<?= $f["id"] ?>" class="btn btn-sm btn-edit">Editar</a>
        <a href="funcionario_delete.php?id=<?= $f["id"] ?>"
           class="btn btn-sm btn-delete"
           onclick="return confirm('Remover <?= htmlspecialchars(addslashes($f["nome"])) ?>? O histórico de ponto será perdido.')">
          Remover
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · PontoApp · UX Modernizer · Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>
</body>
</html>
