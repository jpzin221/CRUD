<?php
/**
 * Relatórios – Horas trabalhadas e estimativa de pagamento
 * Criado por: João Pedro Alves Rocha
 */
session_start();
require_once __DIR__ . "/Funcionario.php";
require_once __DIR__ . "/Registro.php";

// Filtro: mês/ano selecionado (padrão = mês atual)
$mes = (int) ($_GET["mes"] ?? date("n"));
$ano = (int) ($_GET["ano"] ?? date("Y"));

// Garante valores válidos
$mes = max(1, min(12, $mes));
$ano = max(2020, min((int)date("Y"), $ano));

$inicio = sprintf("%04d-%02d-01", $ano, $mes);
$fim    = date("Y-m-t", strtotime($inicio)); // último dia do mês

$mesesNomes = [
    1=>"Janeiro",2=>"Fevereiro",3=>"Março",4=>"Abril",
    5=>"Maio",6=>"Junho",7=>"Julho",8=>"Agosto",
    9=>"Setembro",10=>"Outubro",11=>"Novembro",12=>"Dezembro"
];

$error    = null;
$totais   = [];
$func     = null;

try {
    $funcModel = new Funcionario();
    $regModel  = new Registro();
    $totais    = $regModel->totaisPorPeriodo($inicio, $fim);
    $funcionarios = $funcModel->all();
} catch (PDOException $e) {
    $error = "Não foi possível conectar ao banco. Verifique connect.php.";
    $funcionarios = [];
}

// Totais gerais do período
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

  <!-- Filtro de período -->
  <div class="card" style="margin-bottom:1.5rem">
    <p class="card-title">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
      Período
    </p>
    <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
      <div class="form-group">
        <label for="mes">Mês</label>
        <select id="mes" name="mes" class="select-field">
          <?php foreach ($mesesNomes as $n => $nome): ?>
          <option value="<?= $n ?>" <?= $n === $mes ? "selected" : "" ?>><?= $nome ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="ano">Ano</label>
        <select id="ano" name="ano" class="select-field">
          <?php for ($y = (int)date("Y"); $y >= 2020; $y--): ?>
          <option value="<?= $y ?>" <?= $y === $ano ? "selected" : "" ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-bottom:0">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/></svg>
        Filtrar
      </button>
    </form>
  </div>

  <!-- Cards de resumo -->
  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-icon stat-icon-blue">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
      </div>
      <div class="stat-body">
        <span class="stat-value"><?= count($funcionarios) ?></span>
        <span class="stat-label">Funcionários</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon stat-icon-purple">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm.5 5v5.25l4.5 2.67-.75 1.23L11 13V7h1.5z"/></svg>
      </div>
      <div class="stat-body">
        <span class="stat-value"><?= number_format($totalHorasGeral, 1, ",", ".") ?>h</span>
        <span class="stat-label">Total de horas – <?= $mesesNomes[$mes] ?></span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon stat-icon-green">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
      </div>
      <div class="stat-body">
        <span class="stat-value">R$ <?= number_format($totalSalarioGeral, 2, ",", ".") ?></span>
        <span class="stat-label">Estimativa total</span>
      </div>
    </div>
  </div>

  <!-- Título do relatório -->
  <div class="section-heading">
    <h2>
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
      <?= $mesesNomes[$mes] ?> de <?= $ano ?>
    </h2>
  </div>

  <?php if (empty($totais)): ?>
  <div class="empty-state">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
    <p>Nenhum registro no período selecionado.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Funcionário</th>
          <th>Cargo</th>
          <th>Dias</th>
          <th>Horas</th>
          <th>Valor/hora</th>
          <th>Estimativa</th>
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
          <td class="td-name">
            <div style="display:flex;align-items:center;gap:.625rem">
              <div class="avatar-sm"><?= mb_strtoupper(mb_substr($t["nome"],0,1)) ?></div>
              <?= htmlspecialchars($t["nome"]) ?>
            </div>
          </td>
          <td class="td-email"><?= htmlspecialchars($t["cargo"] ?: "—") ?></td>
          <td>
            <?php if ($t["dias_trabalhados"] > 0): ?>
            <span class="badge-count"><?= $t["dias_trabalhados"] ?>d</span>
            <?php else: ?>
            <span style="color:var(--text-muted)">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($horas > 0): ?>
            <span class="hours-display"><?= $h ?>h<?= $min > 0 ? $min . "min" : "" ?></span>
            <?php else: ?>
            <span style="color:var(--text-muted)">—</span>
            <?php endif; ?>
          </td>
          <td class="td-doc">
            <?= $valorHora > 0 ? "R$ " . number_format($valorHora, 2, ",", ".") : "—" ?>
          </td>
          <td>
            <?php if ($t["estimativa_pagamento"] > 0): ?>
            <span class="salary-badge">R$ <?= number_format($t["estimativa_pagamento"], 2, ",", ".") ?></span>
            <?php else: ?>
            <span style="color:var(--text-muted)">Sem registros</span>
            <?php endif; ?>
          </td>
        </tr>

        <?php
        // Histórico detalhado deste funcionário
        if ($horas > 0):
            $hist = $regModel->findByFuncionario($t["funcionario_id"], $inicio, $fim);
        endif;
        ?>
        <?php if (!empty($hist) && $horas > 0): ?>
        <tr class="hist-row">
          <td colspan="6">
            <details>
              <summary class="hist-summary">Ver histórico detalhado (<?= count($hist) ?> registros)</summary>
              <div class="hist-table-wrap">
                <table class="hist-table">
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th>Entrada</th>
                      <th>Saída</th>
                      <th>Duração</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($hist as $reg):
                        $dur = (float)($reg["duracao_decimal"] ?? 0);
                        $durH = floor($dur);
                        $durM = round(($dur - $durH) * 60);
                    ?>
                    <tr>
                      <td><?= date("d/m/Y", strtotime($reg["entrada"])) ?></td>
                      <td><?= date("H:i", strtotime($reg["entrada"])) ?></td>
                      <td><?= $reg["saida"] ? date("H:i", strtotime($reg["saida"])) : '<span style="color:var(--accent)">Em aberto</span>' ?></td>
                      <td><?= $reg["duracao_decimal"] ? "{$durH}h{$durM}min" : "—" ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </details>
          </td>
        </tr>
        <?php endif; ?>

        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <p class="report-note">
    💡 Estimativa baseada em <strong>220h mensais</strong>.
    Valor/hora = Salário ÷ 220. Estimativa = Valor/hora × Horas trabalhadas.
  </p>
  <?php endif; ?>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> · Sistema de Controle de Ponto · Criado por <strong>João Pedro Alves Rocha</strong>
  </footer>
</div>
</body>
</html>
