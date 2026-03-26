<?php
/**
 * Relatórios – Visão SaaS
 * Criado por: João Pedro Alves Rocha
 */
session_start();
require_once __DIR__ . "/Funcionario.php";
require_once __DIR__ . "/Registro.php";

$mes = (int) ($_GET["mes"] ?? date("n"));
$ano = (int) ($_GET["ano"] ?? date("Y"));

$mes = max(1, min(12, $mes));
$ano = max(2020, min((int)date("Y"), $ano));

$inicio = sprintf("%04d-%02d-01", $ano, $mes);
$fim    = date("Y-m-t", strtotime($inicio));

$mesesNomes = [
    1=>"Janeiro",2=>"Fevereiro",3=>"Março",4=>"Abril",
    5=>"Maio",6=>"Junho",7=>"Julho",8=>"Agosto",
    9=>"Setembro",10=>"Outubro",11=>"Novembro",12=>"Dezembro"
];

$error    = null;
$totais   = [];

try {
    $funcModel = new Funcionario();
    $regModel  = new Registro();
    $totais    = $regModel->totaisPorPeriodo($inicio, $fim);
    $funcionarios = $funcModel->all();
} catch (PDOException $e) {
    $error = "Erro interno: " . $e->getMessage();
    $funcionarios = [];
}

$totalHorasGeral  = array_sum(array_column($totais, "total_horas"));
$totalSalarioGeral = array_sum(array_column($totais, "estimativa_pagamento"));
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relatórios · PontoApp</title>
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

  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px;flex-wrap:wrap;gap:16px">
    <div>
      <p class="page-title">Financeiro & Horas</p>
      <p class="page-subtitle" style="margin-bottom:0">Estimativas baseadas no banco de horas de <?= $mesesNomes[$mes] ?>/<?= $ano ?></p>
    </div>
    
    <form method="GET" style="display:flex;gap:12px;align-items:center;background:var(--bg-card);padding:12px 20px;border-radius:var(--radius-md);border:1px solid var(--border)">
      <select id="mes" name="mes" class="select-field" style="width:140px;padding:8px 12px">
        <?php foreach ($mesesNomes as $n => $nome): ?>
        <option value="<?= $n ?>" <?= $n === $mes ? "selected" : "" ?>><?= $nome ?></option>
        <?php endforeach; ?>
      </select>
      <select id="ano" name="ano" class="select-field" style="width:100px;padding:8px 12px">
        <?php for ($y = (int)date("Y"); $y >= 2020; $y--): ?>
        <option value="<?= $y ?>" <?= $y === $ano ? "selected" : "" ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
      <button type="submit" class="btn btn-primary" style="padding:8px 16px">Filtrar</button>
    </form>
  </div>

  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-icon stat-icon-blue">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
      </div>
      <div class="stat-body">
        <span class="stat-value"><?= count($funcionarios) ?></span>
        <span class="stat-label">Equipe Ativa</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon stat-icon-purple">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm.5 5v5.25l4.5 2.67-.75 1.23L11 13V7h1.5z"/></svg>
      </div>
      <div class="stat-body">
        <span class="stat-value"><?= number_format($totalHorasGeral, 1, ",", ".") ?>h</span>
        <span class="stat-label">Horas Trabalhadas</span>
      </div>
    </div>
    <div class="stat-card" style="border-color:rgba(34,197,94,0.3);background:linear-gradient(to right, var(--bg-card), rgba(34,197,94,0.03))">
      <div class="stat-icon stat-icon-green">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
      </div>
      <div class="stat-body">
        <span class="stat-value" style="color:var(--success)">R$ <?= number_format($totalSalarioGeral, 2, ",", ".") ?></span>
        <span class="stat-label">Custo Estimado</span>
      </div>
    </div>
  </div>

  <?php if (empty($totais)): ?>
  <div class="empty-state">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
    <p>Nenhum registro de ponto encontrado para <?= $mesesNomes[$mes] ?>.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Colaborador</th>
          <th>Dias</th>
          <th>Horas Líquidas</th>
          <th>Valor / Hora</th>
          <th>Total Estimado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($totais as $t):
            $valorHora = $t["salario_mensal"] > 0
                ? $t["salario_mensal"] / 220
                : 0;
            $horas     = (float) $t["total_horas"];
            $h         = floor($horas);
            $min       = round(($horas - $h) * 60);
        ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:12px">
              <div class="avatar" style="width:36px;height:36px;font-size:.9rem"><?= mb_strtoupper(mb_substr($t["nome"],0,1)) ?></div>
              <div>
                <div style="font-weight:600;color:var(--text-main)"><?= htmlspecialchars($t["nome"]) ?></div>
                <div style="font-size:0.8rem;color:var(--text-muted)"><?= htmlspecialchars($t["cargo"] ?: "—") ?></div>
              </div>
            </div>
          </td>
          <td style="font-family:monospace;font-size:1rem;color:var(--text-secondary)">
            <?= $t["dias_trabalhados"] ?: "—" ?>
          </td>
          <td>
            <?php if ($horas > 0): ?>
            <span style="font-family:monospace;font-size:1.1rem;font-weight:600;color:var(--primary)"><?= $h ?>h<?= $min > 0 ? sprintf("%02d", $min) . "m" : "" ?></span>
            <?php else: ?>
            <span style="color:var(--text-muted)">—</span>
            <?php endif; ?>
          </td>
          <td style="font-family:monospace;font-size:0.95rem;color:var(--text-secondary)">
            <?= $valorHora > 0 ? "R$ " . number_format($valorHora, 2, ",", ".") : "—" ?>
          </td>
          <td>
            <?php if ($t["estimativa_pagamento"] > 0): ?>
            <span class="salary-badge">R$ <?= number_format($t["estimativa_pagamento"], 2, ",", ".") ?></span>
            <?php else: ?>
            <span style="color:var(--text-muted)">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · PontoApp · UI Modernizada · Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>
</body>
</html>
