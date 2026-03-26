<?php
/**
 * Edição de Funcionário - Versão SaaS
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

<div class="page-wrapper" style="max-width:700px">

  <?php if ($error): ?>
  <div class="flash flash-error">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
  <?php endif; ?>

  <p class="page-title">Editar Perfil</p>
  <p class="page-subtitle">Atualizando dados de <strong><?= htmlspecialchars($f["nome"]) ?></strong></p>

  <div class="card">
    <form action="funcionario_update.php" method="POST" novalidate>
      <input type="hidden" name="id" value="<?= $f["id"] ?>">
      <div class="form-grid" style="grid-template-columns:1fr">
        <div class="form-group">
          <label for="nome">Nome completo *</label>
          <input type="text" id="nome" name="nome" class="input"
                 value="<?= htmlspecialchars($f["nome"]) ?>" required>
        </div>
        <div class="form-group">
          <label for="email">E-mail corporativo</label>
          <input type="email" id="email" name="email" class="input"
                 value="<?= htmlspecialchars($f["email"] ?? "") ?>">
        </div>
        <div class="form-group">
          <label for="cargo">Cargo</label>
          <input type="text" id="cargo" name="cargo" class="input"
                 value="<?= htmlspecialchars($f["cargo"] ?? "") ?>">
        </div>
        <div class="form-group">
          <label for="salario">Salário Base Mensal (R$)</label>
          <input type="number" id="salario" name="salario" class="input" step="0.01" min="0"
                 value="<?= $f["salario_mensal"] ?>">
        </div>
      </div>
      <div style="display:flex;gap:12px;margin-top:32px">
        <button type="submit" class="btn btn-primary" style="flex:1">
          Salvar Alterações
        </button>
        <a href="funcionarios.php" class="btn btn-ghost" style="flex:1">
          Cancelar
        </a>
      </div>
    </form>
  </div>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · PontoApp · UI Modernizada · Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>
</body>
</html>
