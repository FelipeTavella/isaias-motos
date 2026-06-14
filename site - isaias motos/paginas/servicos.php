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

        <div id="servicos-section" class="section">
            <div class="card">
    
              <div id="TitleCard">
                <h3>Serviços</h3>
                <button class="btn-blue" href="#" onclick="showSection('CadastroServicos')">Nova Serviço</button>
              </div>
    
              <div class="BarraPesquisa">
                <input type="text" placeholder="Buscar serviços cadastrados..." id="search-input">
                <button class="search-btn">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </button>
              </div>
    
              <div id="TabelaDados">
                <table>
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Nome do Serviço</th>
                      <th>Descrição</th>
                      <th>Categoria</th>
                      <th>Tempo Padrão</th>
                      <th>Preço Padrão</th>
                      <th>Dificuldade</th>
                      <th>Tipo</th>
                      <th>Status</th>
                      <th>Observações</th>
                      <th></th>
                      <th></th>
                    </tr>
                    
                  </thead>
                  <tbody id="servicos-table">
                    <?php
                    $sql = "SELECT * FROM servicos ORDER BY codservico DESC";
                    $stmt = $conexao->query($sql);
                    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($servicos as $s):
                    ?>
                        <tr>
                            <td><?= $s['codservico'] ?></td>
                            <td><?= $s['nome'] ?></td>
                            <td><?= $s['descricao'] ?></td>
                            <td><?= $s['categoria'] ?></td>
                            <td><?= $s['duracao_min'] ?> min</td>
                            <td>R$ <?= number_format($s['preco'], 2, ',', '.') ?></td>
                            <td><?= $s['dificuldade'] ?></td>
                            <td><?= $s['tipo'] ?></td>
                            <td><?= $s['status'] ?></td>
                            <td><?= $s['observacoes'] ?></td>

                            <td>
                                <a href="#"
                                    onclick="openEditServico(
                                  '<?= $s['codservico'] ?>',
                                  '<?= addslashes($s['nome']) ?>',
                                  '<?= addslashes($s['descricao']) ?>',
                                  '<?= addslashes($s['categoria']) ?>',
                                  '<?= $s['duracao_min'] ?>',
                                  '<?= $s['preco'] ?>',
                                  '<?= addslashes($s['dificuldade']) ?>',
                                  '<?= addslashes($s['tipo']) ?>',
                                  '<?= addslashes($s['status']) ?>',
                                  '<?= addslashes($s['observacoes']) ?>'
                              )">
                                    <i class="fa-solid fa-pen" style="color:blue"></i>
                                </a>
                            </td>

                            <td>
                                <a href="php/excluirServico.php?id=<?= $s['codservico'] ?>"
                                    onclick="return confirm('Excluir serviço?')">
                                    <i class="fa-solid fa-trash" style="color:red"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
              </div>
            </div>
          </div>

      <!-- ------------------------------------------------ CADASTROS ------------------------------------------------ -->


      <!-- ----- Serviços ----- -->


      <div id="CadastroServicos-section" class="section hidden">
        <div class="card form-card">

          <div class="form-header">
            <h2>Cadastrar Serviços</h2>
            <p>Preencha as informações abaixo</p>
          </div>

          <form id="servico-form">

            <div class="form-grid">
              <div class="form-group">
                <label>Nome do Serviço</label>
                <input type="text" id="servico-nome" required>
              </div>

              <div class="form-group">
                <label>Descrição</label>
                <input type="text" id="servico-descricao">
              </div>

              <div class="form-group">
                <label>Categoria</label>
                <input type="text" id="servico-categoria">
              </div>

              <div class="form-group">
                <label>Tempo Padrão</label>
                <input type="text" id="servico-tempo-padrao">
              </div>

              <div class="form-group">
                <label>Preço Padrão</label>
                <input type="text" id="servico-preco-padrao">
              </div>
              <div class="form-group">
                <label>Dificuldade</label>
                <input type="text" id="servico-dificuldade">
              </div>
              <div class="form-group">
                <label>Tipo</label>
                <input type="text" id="servico-tipo">
              </div>
              <div class="form-group">
                <label>Status</label>
                <input type="text" id="servico-status">
              </div>
              <div class="form-group">
                <label>Observação</label>
                <input type="text" id="servico-observacao">
              </div>

            </div>

            <div class="form-actions">
              <button type="button" class="btn-red" onclick="showSection('servicos')">
                Cancelar
              </button>

              <button type="reset" class="btn-blue">
                Limpar
              </button>

              <button type="submit" class="btn-green">
                Salvar Cliente
              </button>
            </div>

          </form>

        </div>
      </div>


      <!-- EDITAR -->
      <div id="EditarServicos-section" class="section hidden">

          <div class="card">
            <h3>Editar Serviço</h3>

            <form action="php/editarServico.php" method="POST">

              <input type="hidden" name="id" id="edit-id">

              <input name="nome" id="edit-nome">
              <input name="descricao" id="edit-descricao">
              <input name="categoria" id="edit-categoria">
              <input name="duracao_min" id="edit-duracao">
              <input name="preco" id="edit-preco">
              <input name="dificuldade" id="edit-dificuldade">
              <input name="tipo" id="edit-tipo">
              <input name="status" id="edit-status">
              <input name="observacoes" id="edit-observacoes">

                    <div class="form-actions">
                    <button type="button" class="btn-red" onclick="showSection('servicos')">
                      Cancelar
                    </button>

                    <button type="submit" class="btn-green">
                      Atualizar Cliente
                    </button>
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

    function openEditServico(id,nome,descricao,categoria,duracao,preco,dificuldade,tipo,status,obs){

    showSection('EditarServicos')

    document.getElementById('edit-id').value = id
    document.getElementById('edit-nome').value = nome
    document.getElementById('edit-descricao').value = descricao
    document.getElementById('edit-categoria').value = categoria
    document.getElementById('edit-duracao').value = duracao
    document.getElementById('edit-preco').value = preco
    document.getElementById('edit-dificuldade').value = dificuldade
    document.getElementById('edit-tipo').value = tipo
    document.getElementById('edit-status').value = status
    document.getElementById('edit-observacoes').value = obs
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