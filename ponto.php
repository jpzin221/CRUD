<?php
/**
 * Tela principal – Registro de Ponto
 * A tela mais importante do sistema: simples, rápida, zero fricção.
 *
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

    // Para cada funcionário, busca seu registro em aberto
    $pontosAbertos = [];
    foreach ($funcionarios as $f) {
        $pontosAbertos[$f["id"]] = $reg->findAberto($f["id"]);
    }
} catch (PDOException $e) {
    $error        = "Não foi possível conectar ao banco. Verifique connect.php.";
    $funcionarios  = [];
    $pontosAbertos = [];
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Ponto · PontoApp</title>
  <meta name="description" content="Registro de entrada e saída de funcionários.">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once __DIR__ . "/nav.php"; ?>

<div class="page-wrapper">

  <!-- Flash -->
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

  <!-- Hora atual (atualizada via JS) -->
  <div class="time-hero">
    <div class="time-display" id="time-display">00:00:00</div>
    <div class="time-date" id="time-date"></div>
  </div>

  <!-- Seção de funcionários -->
  <div class="section-heading">
    <h2>
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
      </svg>
      Registro de Ponto
    </h2>
    <span class="badge-count"><?= count($funcionarios) ?> funcionário<?= count($funcionarios) !== 1 ? 's' : '' ?></span>
  </div>

  <?php if (empty($funcionarios)): ?>
  <div class="empty-state">
    <svg viewBox="0 0 24 24" fill="currentColor">
      <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
    </svg>
    <p>Nenhum funcionário cadastrado.<br>
       <a href="funcionarios.php" class="link-accent">Cadastre o primeiro funcionário →</a>
    </p>
  </div>
  <?php else: ?>
  <div class="ponto-grid">
    <?php foreach ($funcionarios as $f):
        $aberto    = $pontosAbertos[$f["id"]] ?? false;
        $working   = (bool) $aberto;
        $entradaFmt = $aberto
            ? date("H:i", strtotime($aberto["entrada"]))
            : null;
    ?>
    <div class="ponto-card <?= $working ? 'working' : '' ?>">

      <div class="ponto-card-top">
        <div class="func-avatar">
          <?= mb_strtoupper(mb_substr($f["nome"], 0, 1)) ?>
        </div>
        <div class="func-info">
          <span class="func-nome"><?= htmlspecialchars($f["nome"]) ?></span>
          <span class="func-cargo"><?= htmlspecialchars($f["cargo"] ?: "—") ?></span>
        </div>
        <div class="status-badge <?= $working ? 'status-working' : 'status-off' ?>">
          <span class="status-dot"></span>
          <?= $working ? 'Trabalhando' : 'Fora do expediente' ?>
        </div>
      </div>

      <?php if ($working && $entradaFmt): ?>
      <div class="entrada-info">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;opacity:.6">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm.5 5v5.25l4.5 2.67-.75 1.23L11 13V7h1.5z"/>
        </svg>
        Entrada às <strong><?= $entradaFmt ?></strong>
      </div>
      <?php endif; ?>

      <div class="ponto-action">
        <?php if (!$working): ?>
        <form action="ponto_entrada.php" method="POST">
          <input type="hidden" name="funcionario_id" value="<?= $f["id"] ?>">
          <button type="submit" class="btn btn-ponto btn-entrada" id="btn-entrada-<?= $f["id"] ?>">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/>
            </svg>
            Registrar Entrada
          </button>
        </form>
        <?php else: ?>
        <form action="ponto_saida.php" method="POST">
          <input type="hidden" name="funcionario_id" value="<?= $f["id"] ?>">
          <input type="hidden" name="registro_id"    value="<?= $aberto["id"] ?>">
          <button type="submit" class="btn btn-ponto btn-saida" id="btn-saida-<?= $f["id"] ?>">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
            </svg>
            Registrar Saída
          </button>
        </form>
        <?php endif; ?>
      </div>

    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · Sistema de Controle de Ponto ·
    Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>

<script>
  // Relógio em tempo real
  function atualizarRelogio() {
    const agora = new Date();
    const h = String(agora.getHours()).padStart(2, '0');
    const m = String(agora.getMinutes()).padStart(2, '0');
    const s = String(agora.getSeconds()).padStart(2, '0');
    document.getElementById('time-display').textContent = `${h}:${m}:${s}`;

    const dias = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
    const meses = ['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'];
    const d = agora.getDate();
    const mes = meses[agora.getMonth()];
    const ano = agora.getFullYear();
    const diaSemana = dias[agora.getDay()];
    document.getElementById('time-date').textContent = `${diaSemana}, ${d} de ${mes} de ${ano}`;
  }
  atualizarRelogio();
  setInterval(atualizarRelogio, 1000);
</script>

</body>
</html>
