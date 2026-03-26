<?php
/**
 * Componente de navegação compartilhado.
 * Incluído no topo de todas as views.
 *
 * Detecta a página atual para marcar o link ativo.
 */
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>
<nav class="top-nav">
  <div class="nav-inner">
    <a href="ponto.php" class="nav-brand">
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
      </svg>
      PontoApp
    </a>

    <ul class="nav-links">
      <li>
        <a href="ponto.php"
           class="nav-link <?= in_array($paginaAtual, ['ponto.php','index.php']) ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
          </svg>
          Ponto
        </a>
      </li>
      <li>
        <a href="funcionarios.php"
           class="nav-link <?= in_array($paginaAtual, ['funcionarios.php','funcionario_edit.php']) ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
          </svg>
          Funcionários
        </a>
      </li>
      <li>
        <a href="relatorios.php"
           class="nav-link <?= $paginaAtual === 'relatorios.php' ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
          </svg>
          Relatórios
        </a>
      </li>
    </ul>
  </div>
</nav>
