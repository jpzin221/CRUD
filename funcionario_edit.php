<?php
/**
 * Edição de Funcionário
 * Criado por: João Pedro Alves Rocha
 */
session_start();
require_once __DIR__ . "/Funcionario.php";

$error = $_SESSION["error"] ?? null;
unset($_SESSION["error"]);

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION["error"] = "ID inválido.";
    header("Location: funcionarios.php"); exit;
}

try {
    $func = new Funcionario();
    $f    = $func->findById($id);
} catch (PDOException $e) {
    $_SESSION["error"] = "Erro ao carregar dados.";
    header("Location: funcionarios.php"); exit;
}

if (!$f) {
    $_SESSION["error"] = "Funcionário não encontrado.";
    header("Location: funcionarios.php"); exit;
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Funcionário · PontoApp</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once __DIR__ . "/nav.php"; ?>

<div class="page-wrapper">

  <?php if ($error): ?>
  <div class="flash flash-error">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
  <?php endif; ?>

  <p class="page-title">Editar funcionário</p>
  <p class="page-subtitle">Atualizando dados de <strong><?= htmlspecialchars($f["nome"]) ?></strong></p>

  <div class="card">
    <p class="card-title">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
      Dados do funcionário
    </p>
    <form action="funcionario_update.php" method="POST" novalidate>
      <input type="hidden" name="id" value="<?= $f["id"] ?>">
      <div class="form-grid">
        <div class="form-group">
          <label for="nome">Nome completo *</label>
          <input type="text" id="nome" name="nome"
                 value="<?= htmlspecialchars($f["nome"]) ?>" required>
        </div>
        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($f["email"] ?? "") ?>">
        </div>
        <div class="form-group">
          <label for="cargo">Cargo</label>
          <input type="text" id="cargo" name="cargo"
                 value="<?= htmlspecialchars($f["cargo"] ?? "") ?>">
        </div>
        <div class="form-group">
          <label for="salario">Salário mensal (R$)</label>
          <input type="number" id="salario" name="salario" step="0.01" min="0"
                 value="<?= $f["salario_mensal"] ?>">
        </div>
      </div>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <button type="submit" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
          Salvar
        </button>
        <a href="funcionarios.php" class="btn btn-ghost">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
          Voltar
        </a>
      </div>
    </form>
  </div>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · Sistema de Controle de Ponto · Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>
</body>
</html>
