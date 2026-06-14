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

        <div id="ordens-section" class="section">
            <div class="card">
    
              <div id="TitleCard">
                <h3>Ordens</h3>
                <button class="btn-blue" href="#" onclick="showSection('CadastroOrdens')">Nova Ordem</button>
              </div>
    
              <div class="BarraPesquisa">
                <input type="text" placeholder="Buscar ordens cadastrados..." id="search-input">
                <button class="search-btn">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </button>
              </div>
    
              <div id="TabelaDados">
                <table>
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Data Abertura</th>
                      <th>Cliente</th>
                      <th>Moto</th>
                      <th>Placa</th>
                      <th>Serviços</th>
                      <th>Peças</th>
                      <th>Status</th>
                      <th>Valor Total</th>
                      <th>Conclusão</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="ordens-table">

                  <?php

                  function statusLabel($s) {
                    return match($s) {
                      1 => "Aberto",
                      2 => "Em andamento",
                      3 => "Aguardando aprovação",
                      4 => "Concluído",
                      5 => "Cancelado",
                      default => "Desconhecido"
                    };
                  }

                  $sql = "
                  SELECT 
                    o.*,
                    c.nome_completo AS cliente,
                    m.modelo AS moto,
                    m.placa,

                    (
                      SELECT GROUP_CONCAT(s.nome SEPARATOR ', ')
                      FROM ordem_servicos os
                      JOIN servicos s ON s.codservico = os.codservico
                      WHERE os.codordem = o.codordem
                    ) AS servicos,

                    (
                      SELECT GROUP_CONCAT(p.nome SEPARATOR ', ')
                      FROM ordem_pecas op
                      JOIN pecas p ON p.codpeca = op.codpeca
                      WHERE op.codordem = o.codordem
                    ) AS pecas

                  FROM ordens_servico o
                  JOIN clientes c ON c.coduser = o.coduser
                  JOIN motos m ON m.codmoto = o.codmoto
                  ORDER BY o.codordem DESC
                  ";

                  $stmt = $conexao->query($sql);
                  $ordens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  foreach($ordens as $o):
                  ?>

                  <tr>
                    <td><?= $o['codordem'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($o['data_abertura'])) ?></td>
                    <td><?= htmlspecialchars($o['cliente']) ?></td>
                    <td><?= htmlspecialchars($o['moto']) ?></td>
                    <td><?= htmlspecialchars($o['placa']) ?></td>
                    <td><?= $o['servicos'] ?: '-' ?></td>
                    <td><?= $o['pecas'] ?: '-' ?></td>
                    <td><?= statusLabel($o['status']) ?></td>
                    <td>R$ <?= number_format($o['valor_total'], 2, ',', '.') ?></td>
                    <td><?= $o['data_fechamento'] ? date('d/m/Y', strtotime($o['data_fechamento'])) : '-' ?></td>

                    <td>
                      <a href="#" onclick="openEditarOrdem(
                        <?= $o['codordem'] ?>,
                        '<?= $o['data_abertura'] ?>',
                        '<?= addslashes($o['cliente']) ?>',
                        '<?= addslashes($o['moto']) ?>',
                        '<?= $o['placa'] ?>',
                        '<?= $o['status'] ?>',
                        '<?= $o['valor_total'] ?>',
                        '<?= $o['data_fechamento'] ?>'
                      )">
                        <i class="fa fa-pen" style="color:blue"></i>
                      </a>

                      <a href="php/excluirOrdem.php?id=<?= $o['codordem'] ?>" onclick="return confirm('Excluir ordem?')">
                        <i class="fa fa-trash" style="color:red"></i>
                      </a>
                    </td>
                  </tr>

                  <?php endforeach; ?>



                  </tbody>
                </table>
              </div>
            </div>
          </div>

      <!-- ------------------------------------------------ CADASTRO ------------------------------------------------ -->

      <div id="CadastroOrdens-section" class="section hidden">
        <div class="card form-card">

          <div class="form-header">
            <h2>Cadastrar Ordens</h2>
            <p>Preencha as informações abaixo</p>
          </div>

          <form method="POST" action="php/cadastroOrdens.php">

            <div class="form-grid">
              <div class="form-group">
                <label>Data de Abertura</label>
                <input type="text" name="data_abertura" id="ordem-data-abertura" required>
              </div>
              <div class="form-group">
                <label>Cliente</label>
                <input type="text" id="ordem-cliente" name="cliente">
              </div>
              <div class="form-group">
                <label>Moto</label>
                <input type="text" id="ordem-moto" name="moto">
              </div>
              <div class="form-group">
                <label>Placa da Moto</label>
                <input type="text" id="ordem-placa" name="placa">
              </div>
              <div class="form-group">
                <label>Serviço Realizado</label>
                <input type="text" id="ordem-servico">
              </div>
              <div class="form-group">
                <label>Peças Utilizadas</label>
                <input type="text" id="ordem-pecas">
              </div>
              <div class="form-group">
                <label>Status</label>
                <input type="text" id="ordem-status" ame="status">
              </div>
              <div class="form-group">
                <label>Prioridade</label>
                <input type="text" id="ordem-prioridade">
              </div>
              <div class="form-group">
                <label>Valor Total</label>
                <input type="text" id="ordem-valor-total" name="valor_total">
              </div>
              <div class="form-group">
                <label>Data de Conclusão</label>
                <input type="text" id="ordem-data-conclusao" name="data_conclusao">
              </div>

              <div class="form-actions">
              <button type="button" class="btn-red" onclick="showSection('ordens')">
                Cancelar
              </button>

              <button type="reset" class="btn-blue">
                Limpar
              </button>

              <button type="submit" class="btn-green">
                Salvar Cliente
              </button>
              </div>

            </div>  

          </form>
        </div>
      </div>


            <!-- ------------------------------------------------ EDITAR ------------------------------------------------ -->

      <div id="EditarOrdens-section" class="section hidden">
        <div class="card form-card">

          <div class="form-header">
            <h2>Editar Ordens</h2>
            <p>Preencha as informações abaixo</p>
          </div>

          <form method="POST" action="php/editarOrdens.php">

            <div class="form-grid">
              <div class="form-group">
                <label>Data de Abertura</label>
                <input type="text" id="edit-ordem-data-abertura" name="data_abertura"  required>
              </div>

              <input type="hidden" id="edit-ordem-id" name="id">

              <div class="form-group">
                <label>Cliente</label>
                <input type="text" id="edit-ordem-cliente" name="cliente">
              </div>
              <div class="form-group">
                <label>Moto</label>
                <input type="text" id="edit-ordem-moto" name="moto">
              </div>
              <div class="form-group">
                <label>Placa da Moto</label>
                <input type="text" id="edit-ordem-placa" name="placa">
              </div>
              <div class="form-group">
                <label>Serviço Realizado</label>
                <input type="text" id="edit-ordem-servico"> <!-- -------------------------------------- -->
              </div>
              <div class="form-group">
                <label>Peças Utilizadas</label>
                <input type="text" id="edit-ordem-pecas"> <!-- ----------------------------------------- -->
              </div>
              <div class="form-group">
                <label>Status</label>
                <input type="text" id="edit-ordem-status" name="status">
              </div>
              <div class="form-group">
                <label>Prioridade</label>
                <input type="text" id="edit-ordem-prioridade"> <!-- ----------------------------------------- -->
              </div>
              <div class="form-group">
                <label>Valor Total</label>
                <input type="text" id="edit-ordem-valor-total" name="valor_total">
              </div>
              <div class="form-group">
                <label>Data de Conclusão</label>
                <input type="text" id="edit-ordem-data-conclusao" name="data_conclusao">
              </div>

              <div class="form-actions">
              <button type="button" class="btn-red" onclick="showSection('ordens')">
                Cancelar
              </button>

              <button type="reset" class="btn-blue">
                Limpar
              </button>

              <button type="submit" class="btn-green">
                Salvar Ordem
              </button>
              </div>

            </div>  

          </form>
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

    function openEditarOrdem(id, data, cliente, moto, placa, status, valor, data_fechamento) {
        showSection('EditarOrdens');

        document.getElementById('edit-ordem-id').value = id;
        document.getElementById('edit-ordem-data-abertura').value = data;
        document.getElementById('edit-ordem-cliente').value = cliente;
        document.getElementById('edit-ordem-moto').value = moto;
        document.getElementById('edit-ordem-placa').value = placa;
        document.getElementById('edit-ordem-status').value = status;
        document.getElementById('edit-ordem-valor-total').value = valor;
        document.getElementById('edit-ordem-data-conclusao').value = data_fechamento;
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