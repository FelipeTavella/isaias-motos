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

        <div id="motos-section" class="section">
            <div class="card">
    
              <div id="TitleCard">
                <h3>Motos</h3>
                <button class="btn-blue" href="#" onclick="showSection('CadastroMoto')">Nova Moto</button>
              </div>
    
              <div class="BarraPesquisa">
                <input type="text" placeholder="Buscar motos cadastrados..." id="search-input">
                <button class="search-btn">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </button>
              </div>
    
              <div id="TabelaDados">
                <table>
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Placa</th>
                      <th>Modelo</th>
                      <th>Marca</th>
                      <th>Ano</th>
                      <th>Proprietário</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>

                  <tbody id="veiculos-table">

                   <?php
                      $sql = "SELECT m.*, c.nome_completo, c.telefone, c.email
                              FROM motos m
                              JOIN clientes c ON m.coduser = c.coduser
                              ORDER BY m.codmoto DESC";

                      $motos = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                      foreach($motos as $m):
                      ?>

                        <tr>
                          <td><?= str_pad($m['codmoto'], 3, '0', STR_PAD_LEFT) ?></td>
                          <td><?= $m['placa'] ?></td>
                          <td><?= $m['modelo'] ?></td>
                          <td><?= $m['marca'] ?></td>
                          <td><?= $m['ano'] ?></td>
                          <td><?= $m['nome_completo'] ?></td>
                          <td><?= $m['telefone'] ?></td>

                          <td>
                            <a href="#" onclick="openEditMoto(
                              '<?= $m['codmoto'] ?>',
                              '<?= $m['placa'] ?>',
                              '<?= $m['modelo'] ?>',
                              '<?= $m['marca'] ?>',
                              '<?= $m['ano'] ?>',
                              '<?= $m['coduser'] ?>'
                            )">
                              <i class="fa-solid fa-pencil" style="color:#2563eb"></i>
                            </a>
                          </td>

                          <td>
                            <a href="php/excluirMoto.php?id=<?= $m['codmoto'] ?>">
                              <i class="fa-solid fa-trash" style="color:#dc2626"></i>
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

      <div id="CadastroMoto-section" class="section hidden">
        <div class="card form-card">

          <div class="form-header">
            <h2>Cadastrar Motos</h2>
            <p>Preencha as informações abaixo</p>
          </div>

          <form method="POST" action="php/cadastrarMoto.php">

             <div class="form-grid">

              <div class="form-group">
                <label>Placa</label>
                <input type="text" name="placa" required>
              </div>

              <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo">
              </div>

              <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca">
              </div>

              <div class="form-group">
                <label>Ano</label>
                <input type="number" name="ano">
              </div>

              <div class="form-group">
                <label>Cliente (Proprietário)</label>
                <select name="coduser">
                  <?php
                  $clientes = $conexao->query("SELECT * FROM clientes")->fetchAll(PDO::FETCH_ASSOC);
                  foreach($clientes as $c):
                  ?>
                    <option value="<?= $c['coduser'] ?>">
                      <?= $c['nome_completo'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

            </div>

            <div class="form-actions">
              <button type="button" class="btn-red" onclick="showSection('motos')">Cancelar</button>
              <button type="submit" class="btn-green">Salvar</button>
            </div>

          </form>

        </div>
      </div>
      

      <!-- ================= EDITAR ================= -->
<div id="EditarMoto-section" class="section hidden">
  <div class="card form-card">

    <div class="form-header">
      <h2>Editar Moto</h2>
    </div>

    <form method="POST" action="php/editarMoto.php">

      <input type="hidden" name="id" id="edit-id">

      <div class="form-grid">

        <div class="form-group">
          <label>Placa</label>
          <input type="text" name="placa" id="edit-placa">
        </div>

        <div class="form-group">
          <label>Modelo</label>
          <input type="text" name="modelo" id="edit-modelo">
        </div>

        <div class="form-group">
          <label>Marca</label>
          <input type="text" name="marca" id="edit-marca">
        </div>

        <div class="form-group">
          <label>Ano</label>
          <input type="number" name="ano" id="edit-ano">
        </div>

      </div>

      <div class="form-actions">
        <button type="button" class="btn-red" onclick="showSection('motos')">Cancelar</button>
        <button type="submit" class="btn-green">Atualizar</button>
      </div>

    </form>

  </div>
</div>

</main>
</div>






    </main>
  </div>

  <script>
    function showSection(section) {
      document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'))
      document.getElementById(section + '-section').classList.remove('hidden')
      closeMenu()
    }

    function showSection(section){
      document.querySelectorAll('.section').forEach(s=>s.classList.add('hidden'))
      document.getElementById(section+'-section').classList.remove('hidden')
    }

    function openEditMoto(id, placa, modelo, marca, ano){
      showSection('EditarMoto')
      document.getElementById('edit-id').value = id
      document.getElementById('edit-placa').value = placa
      document.getElementById('edit-modelo').value = modelo
      document.getElementById('edit-marca').value = marca
      document.getElementById('edit-ano').value = ano
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