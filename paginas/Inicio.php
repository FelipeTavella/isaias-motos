<!DOCTYPE html>
<html lang="pt-br">

<?php require_once("../Conexao/conexao.php"); ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERP - Oficina Mecânica</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <!-- ------------------------------------------------ Barra Lateral ------------------------------------------------ -->

  <div id="overlay" class="overlay" onclick="closeMenu()"></div>

  <div id="sidebar" class="sidebar hidden-mobile">
    <div style="height:80px;display:flex;align-items:center;justify-content:center;background:#020617">
      <img src="../Imagens/IsaiasmotosW.png" style="height:50px;margin-right:10px">
    </div>

    <nav>
      <ul>
        <li><a href="Inicio.php"><i class="fa-solid fa-bars"></i>Dashboard</a></li>
        <li><a href="clientes.php"><i class="fa-solid fa-users icon-black"></i>Clientes</a></li>
        <li><a href="motos.php"><i class="fa-solid fa-motorcycle"></i>Motos</a></li>
        <li><a href="pecas.php"><i class="fa-solid fa-gear"></i>Peças</a></li>
        <li><a href="servicos.php"><i class="fa-solid fa-wrench"></i>Serviços</a></li>
        <li><a href="ordens.php"><i class="fa-solid fa-clipboard"></i>Ordens</a></li>
        <li><a href="relatorios.php"><i class="fa-solid fa-file-lines"></i>Relatórios</a></li>
        <li><a href="meu-Usuario.php"><i class="fa-solid fa-user"></i>Meu Usuário</a></li>
        <li><a href="usuarios.php"><i class="fa-solid fa-users"></i>Usuários</a></li>
        <li><a href="configuracoes.php"><i class="fa-solid fa-gear"></i>Configurações</a></li>
      </ul>
    </nav>
  </div>

  <!-- --------------------------------------------------------------------------------------------------------------- -->


  <div class="main-content">

    <header class="main-header">
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
      <h2 id="page-title">Oficina Digital</h2>
      <span id="current-date"></span>
    </header>

    <main>

      <div id="dashboard-section" class="section">
        <div class="grid grid-5 dashboard-cards">

          <div class="card dashboard-card">
            <div class="icon blue"><i class="fa-solid fa-users"></i></div>
            <div class="info">
              <span>CLIENTES</span>
              <h2 id="total-clientes">
                    <?php
                    $sqlcontagemclientes = "SELECT COUNT(*) AS Clientes FROM clientes";
                    $contagemclientes = $conexao->query($sqlcontagemclientes)->fetch(PDO::FETCH_ASSOC); 
                    echo $contagemclientes['Clientes'];
                  ?>

              </h2>
            </div>
          </div>

          <div class="card dashboard-card">
            <div class="icon green"><i class="fa-solid fa-motorcycle"></i></div>
            <div class="info">
              <span>ORDENS ABERTAS</span>
              <h2 id="total-ordens-abertas">
                  <?php
                    $sqlOrdensAbertas = "SELECT COUNT(*) AS total_ordens FROM ordens_servico WHERE status IN (1,2,3)";

                    $contagemOrdens = $conexao->query($sqlOrdensAbertas)->fetch(PDO::FETCH_ASSOC);

                    echo $contagemOrdens['total_ordens'];
                  ?>
                </h2>
            </div>
          </div>

          <div class="card dashboard-card">
            <div class="icon purple"><i class="fa-solid fa-gear"></i></div>
            <div class="info">
              <span>ORDENS ESPERANDO APROVAÇÃO</span>
              <h2 id="total-servicos">
                  <?php
                    $sqlOrdensAbertas = "SELECT COUNT(*) AS total_ordens FROM ordens_servico WHERE status IN (3)";
                    $contagemOrdens = $conexao->query($sqlOrdensAbertas)->fetch(PDO::FETCH_ASSOC);

                    echo $contagemOrdens['total_ordens'];
                  ?>
              </h2>
            </div>
          </div>

          <div class="card dashboard-card">
            <div class="icon yellow"><i class="fa-solid fa-file-lines"></i></div>
            <div class="info">
              <span>ORDENS ATRASADAS</span>
                  <h2 id="total-ordens-atrasadas">
                    <?php
                      $sqlOrdensAtrasadas = "SELECT COUNT(*) AS total FROM ordens_servico WHERE status != 4 AND data_prevista < CURDATE()";
                      $contagem = $conexao->query($sqlOrdensAtrasadas)->fetch(PDO::FETCH_ASSOC);

                      echo $contagem['total'];
                    ?>
                  </h2>
            </div>
          </div>

          <div class="card dashboard-card">
            <div class="icon red"><i class="fa-solid fa-wrench"></i></div>
            <div class="info">
              <span>ORDENS CONCLUÍDAS NO MÊS</span>
              <h2 id="total-ordens-concluidas">
                <?php
                  $sqlOrdensConcluidas = "SELECT COUNT(*) AS total FROM ordens_servico WHERE status = 4
                    AND MONTH(data_fechamento) = MONTH(CURDATE())
                    AND YEAR(data_fechamento) = YEAR(CURDATE())";

                  $contagem = $conexao->query($sqlOrdensConcluidas)->fetch(PDO::FETCH_ASSOC);

                  echo $contagem['total'];
                ?>
              </h2>
            </div>
          </div>
        </div>

        <div id="Conteudo">

          <div id="ContCard" class="card">
            <h3> <i style="margin-right: 7px; color: rgb(199, 199, 0);" class="far fa-clock"></i>Ordens Recentes</h3>
            <br>
            <hr>
              
        <div id="TabelaDados">
          <table>
            <thead>
              <tr>
                <th>Código</th>
                <th>Abertura</th>
                <th>Prevista</th>
                <th>Fechamento</th>
                <th>Cliente</th>
                <th>Moto</th>
                <th>Serviço</th>
                <th>Status</th>
              </tr>
            </thead>

            <tbody>
            <?php
              $sqlOrdensRecentes = "SELECT
                                      os.codordem, os.data_abertura, os.data_prevista, os.data_fechamento, c.nome_completo AS cliente, m.modelo AS moto, s.nome AS servico, os.status
                                    FROM ordens_servico os
                                    INNER JOIN clientes c ON os.coduser = c.coduser
                                    INNER JOIN motos m ON os.codmoto = m.codmoto
                                    LEFT JOIN ordem_servicos ovs ON os.codordem = ovs.codordem
                                    LEFT JOIN servicos s ON ovs.codservico = s.codservico
                                    ORDER BY os.codordem DESC LIMIT 15";

              $ordensRecentes = $conexao->query($sqlOrdensRecentes)->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php foreach($ordensRecentes as $ordem):

                switch($ordem['status']) {
                    case 1: $status = 'Aberta'; break;
                    case 2: $status = 'Em andamento'; break;
                    case 3: $status = 'Aguardando aprovação'; break;
                    case 4: $status = 'Finalizado'; break;
                    case 5: $status = 'Cancelado'; break;
                    default: $status = 'Desconhecido';
                }

                $prevista = $ordem['data_prevista'] ?? '-';
                $fechamento = $ordem['data_fechamento'] ?? '-';

                if ($prevista != '-') {
                    $prevista = date('d/m/y', strtotime($prevista));
                }

                if ($fechamento != '-') {
                    $fechamento = date('d/m/y', strtotime($fechamento));
                }
            ?>

            <tr>
                <td><?= str_pad($ordem['codordem'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= date('d/m/y', strtotime($ordem['data_abertura'])) ?></td>
                <td><?= $prevista ?></td>
                <td><?= $fechamento ?></td>
                <td><?= htmlspecialchars($ordem['cliente']) ?></td>
                <td><?= htmlspecialchars($ordem['moto']) ?></td>
                <td><?= htmlspecialchars($ordem['servico']) ?></td>
                <td><?= $status ?></td>
            </tr>

            <?php endforeach; ?>

            </tbody>
          </table>
        </div>

          </div>

          <div id="ContCard" class="card">
              <h3>
                  <i style="margin-right: 7px; color: rgb(33, 199, 0);" class="fas fa-chart-line"></i>
                  Estatísticas do Mês
              </h3>

              <br>
              <hr>

              <?php
              // Ordens concluídas no mês
              $sqlOrdensConcluidas = "
                  SELECT COUNT(*) 
                  FROM ordens_servico
                  WHERE data_fechamento IS NOT NULL
                  AND MONTH(data_fechamento) = MONTH(CURDATE())
                  AND YEAR(data_fechamento) = YEAR(CURDATE())
              ";
              $ordensConcluidas = $conexao->query($sqlOrdensConcluidas)->fetchColumn();

              // Faturamento do mês
              $sqlFaturamento = "
                  SELECT IFNULL(SUM(valor_total), 0)
                  FROM ordens_servico
                  WHERE data_fechamento IS NOT NULL
                  AND MONTH(data_fechamento) = MONTH(CURDATE())
                  AND YEAR(data_fechamento) = YEAR(CURDATE())
              ";
              $faturamentoMes = $conexao->query($sqlFaturamento)->fetchColumn();

              // Ticket médio
              $sqlTicketMedio = "
                  SELECT IFNULL(AVG(valor_total), 0)
                  FROM ordens_servico
                  WHERE data_fechamento IS NOT NULL
                  AND MONTH(data_fechamento) = MONTH(CURDATE())
                  AND YEAR(data_fechamento) = YEAR(CURDATE())
              ";
              $ticketMedio = $conexao->query($sqlTicketMedio)->fetchColumn();


              // Valor em aberto
              $sqlValorAberto = "
                  SELECT IFNULL(SUM(valor_total), 0)
                  FROM ordens_servico
                  WHERE status IN (1,2,3)
              ";
              $valorAberto = $conexao->query($sqlValorAberto)->fetchColumn();
              ?>

              <div id="DashEstatistica">
                  <h4>Ordens concluídas</h4>
                  <h4 style="color: rgb(0, 95, 204);">
                      <?= $ordensConcluidas ?>
                  </h4>
              </div>

              <div id="DashEstatistica">
                  <h4>Faturamento</h4>
                  <h4 style="color: rgb(58, 204, 0);">
                      R$ <?= number_format($faturamentoMes, 2, ',', '.') ?>
                  </h4>
              </div>

              <div id="DashEstatistica">
                  <h4>Ticket médio</h4>
                  <h4 style="color: rgb(255, 140, 0);">
                      R$ <?= number_format($ticketMedio, 2, ',', '.') ?>
                  </h4>
              </div>

              <div id="DashEstatistica">
                  <h4>Valor em aberto</h4>
                  <h4 style="color: rgb(204, 0, 0);">
                      R$ <?= number_format($valorAberto, 2, ',', '.') ?>
                  </h4>
              </div>

          </div>


        </div>
      </div>

    </main>
  </div>

  <script>
    function showSection(section) {
      document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'))
      document.getElementById(section + '-section').classList.remove('hidden')
      closeMenu()
    }

    function toggleMenu() {
      const sidebar = document.getElementById('sidebar')
      const overlay = document.getElementById('overlay')

      sidebar.classList.toggle('active')
      overlay.classList.toggle('active')
    }

    function closeMenu() {
      document.getElementById('sidebar').classList.remove('active')
      document.getElementById('overlay').classList.remove('active')
    }

    window.addEventListener('resize', () => {
      if (window.innerWidth > 768) {
        document.getElementById('sidebar').classList.remove('active')
        document.getElementById('overlay').classList.remove('active')
      }
    })

    window.onload = () => {
      if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.remove('active')
      }
    }
  </script>

</body>

</html>