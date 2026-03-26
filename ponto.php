<?php
/**
 * Relógio de Ponto - App Principal (SaaS Look)
 * Criado por: João Pedro Alves Rocha
 */
session_start();
require_once __DIR__ . "/Funcionario.php";
require_once __DIR__ . "/Registro.php";

$success = $_SESSION["success"] ?? null;
$error   = $_SESSION["error"]   ?? null;
unset($_SESSION["success"], $_SESSION["error"]);

try {
    $func       = new Funcionario();
    $reg        = new Registro();
    $funcionarios = $func->all();

    $pontosAbertos = [];
    foreach ($funcionarios as $f) {
        $pontosAbertos[$f["id"]] = $reg->findAberto($f["id"]);
    }
} catch (PDOException $e) {
    $error        = "Erro ao inicializar base de dados: " . $e->getMessage();
    $funcionarios  = [];
    $pontosAbertos = [];
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bater Ponto · PontoApp</title>
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

  <div class="time-hero">
    <div class="time-display" id="time-display">00:00:00</div>
    <div class="time-date" id="time-date"></div>
  </div>

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 style="font-size:1.25rem;font-weight:600;display:flex;align-items:center;gap:8px">
      <svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;color:var(--primary)"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
      Registro de Frequência
    </h2>
    <span style="background:var(--bg-card-light);padding:4px 12px;border-radius:20px;font-size:0.85rem;color:var(--text-secondary);border:1px solid var(--border)">
      <?= count($funcionarios) ?> ativo(s)
    </span>
  </div>

  <?php if (empty($funcionarios)): ?>
  <div class="empty-state">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
    <p>O sistema não possui funcionários cadastrados.<br><br>
       <a href="funcionarios.php" class="btn btn-primary">Começar a cadastrar equipe</a>
    </p>
  </div>
  <?php else: ?>
  <div class="ponto-grid">
    <?php foreach ($funcionarios as $f):
        $aberto    = $pontosAbertos[$f["id"]] ?? false;
        $working   = (bool) $aberto;
        $entradaFmt = $aberto ? date("H:i", strtotime($aberto["entrada"])) : null;
    ?>
    <div class="ponto-card <?= $working ? 'working' : '' ?>">
      <div class="ponto-card-top">
        <div class="ponto-identity">
          <div class="avatar"><?= mb_strtoupper(mb_substr($f["nome"], 0, 1)) ?></div>
          <div>
            <div class="emp-name"><?= htmlspecialchars($f["nome"]) ?></div>
            <div class="emp-role"><?= htmlspecialchars($f["cargo"] ?: "Sem cargo") ?></div>
          </div>
        </div>
        <div class="status-badge <?= $working ? 'status-working' : 'status-off' ?>">
          <span class="status-dot"></span>
          <?= $working ? 'Iniciado' : 'Offline' ?>
        </div>
      </div>

      <?php if ($working && $entradaFmt): ?>
      <div class="entrada-info">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:18px"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm.5 5v5.25l4.5 2.67-.75 1.23L11 13V7h1.5z"/></svg>
        <span>Entrada registrada às <strong><?= $entradaFmt ?></strong></span>
      </div>
      <?php endif; ?>

      <div style="margin-top:auto">
        <?php if (!$working): ?>
        <form action="ponto_entrada.php" method="POST">
          <input type="hidden" name="funcionario_id" value="<?= $f["id"] ?>">
          <button type="submit" class="btn btn-primary btn-ponto" style="width:100%">
            Fazer Check-in
          </button>
        </form>
        <?php else: ?>
        <form action="ponto_saida.php" method="POST">
          <input type="hidden" name="funcionario_id" value="<?= $f["id"] ?>">
          <input type="hidden" name="registro_id"    value="<?= $aberto["id"] ?>">
          <button type="submit" class="btn btn-saida btn-ponto" style="width:100%">
            Fazer Check-out
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · PontoApp · UI Modernizada · Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>

<script>
  function atualizarRelogio() {
    const agora = new Date();
    const h = String(agora.getHours()).padStart(2, '0');
    const m = String(agora.getMinutes()).padStart(2, '0');
    document.getElementById('time-display').textContent = `${h}:${m}`;

    const dias = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
    const meses = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
    document.getElementById('time-date').textContent = `${dias[agora.getDay()]}, ${agora.getDate()} de ${meses[agora.getMonth()]}`;
  }
  atualizarRelogio();
  setInterval(atualizarRelogio, 10000); // 10s visto que tiramos os segundos no visual SaaS
</script>

</body>
</html>
