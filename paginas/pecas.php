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

        <div id="pecas-section" class="section">
            <div class="card">
    
              <div id="TitleCard">
                <h3>Peças</h3>
                <button class="btn-blue" href="#" onclick="showSection('CadastroPecas')">Nova Peça</button>
              </div>
    
              <div class="BarraPesquisa">
                <input type="text" placeholder="Buscar peças cadastradas..." id="search-input">
                <button class="search-btn">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </button>
              </div>
    
              <div id="TabelaDados">
                <table>
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Nome</th>
                      <th>Marca</th>
                      <th>Preço</th>
                      <th>Categoria</th>
                      <th>Estoque</th>
                      <th>Lozalização</th>
                      <th>Descrição</th>
                      <th></th>
                      <th></th>
                    </tr>
                    <tr>
                    <tr>
                  </thead>

                  <tbody id="pecas-table">
                    <?php 
                      $sqlPecas = "SELECT * FROM pecas ORDER BY codpeca DESC"; 
                      $pecas = $conexao ->query($sqlPecas) ->fetchAll(PDO::FETCH_ASSOC); 
                      foreach($pecas as $peca): 
                    ?> 
                    <tr> 
                      <td><?= str_pad($peca['codpeca'],3,'0',STR_PAD_LEFT) ?></td> 
                      <td><?= $peca['nome'] ?></td> 
                      <td><?= $peca['marca'] ?></td> 
                      <td> R$ <?= number_format($peca['preco_medio'],2,',','.') ?> </td> 
                      <td><?= $peca['categoria'] ?></td> 
                      <td><?= $peca['estoque'] ?></td> 
                      <td><?= $peca['localizacao'] ?></td> 
                      <td><?= $peca['descricao'] ?></td> 
                    <td> 
                        <a href="#" onclick="openEditPeca( '<?= $peca['codpeca'] ?>', '<?= addslashes($peca['nome']) ?>', '<?= addslashes($peca['marca']) ?>', '<?= $peca['preco_medio'] ?>', '<?= addslashes($peca['categoria']) ?>', '<?= $peca['estoque'] ?>', '<?= addslashes($peca['localizacao']) ?>', '<?= addslashes($peca['descricao']) ?>' )"> 
                        <i style="color:#2563eb" class="fa-solid fa-pencil"></i> 
                        </a> 
                    </td> 
                    <td> 
                        <a href="php/excluirPecas.php?id=<?= $peca['codpeca'] ?>" onclick="return confirm('Deseja excluir esta peça?')">
                        <i style="color:#dc2626" class="fa-solid fa-trash"></i> 
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

      <!-- ----- Peças ----- -->
      <div id="CadastroPecas-section" class="section hidden">
          <div class="card form-card">
              <div class="form-header">
                  <h2>Cadastrar Peça</h2>
                  <p>Preencha os dados abaixo</p>
              </div>
              <form method="POST" action="php/cadastrarPecas.php">
                  <div class="form-grid">
                      <div class="form-group"> <label>Nome</label> <input type="text" name="nome" required> </div>
                      <div class="form-group"> <label>Marca</label> <input type="text" name="marca"> </div>
                      <div class="form-group"> <label>Preço Médio</label> <input type="number" step="0.01" name="preco"> </div>
                      <div class="form-group"> <label>Categoria</label> <input type="text" name="categoria"> </div>
                      <div class="form-group"> <label>Estoque</label> <input type="number" name="estoque"> </div>
                      <div class="form-group"> <label>Localização</label> <input type="text" name="localizacao"> </div>
                      <div class="form-group"> <label>Descrição</label> <textarea name="descricao" rows="3"></textarea> </div>
                  </div>
                  <div class="form-actions"> <button type="button" class="btn-red" onclick="showSection('pecas')"> Cancelar </button> <button type="reset" class="btn-blue"> Limpar </button> <button type="submit" class="btn-green"> Salvar Peça </button> </div>
              </form>
          </div>
      </div>

      <!-- ------------------------------------------------ editar ------------------------------------------------ -->

      <!-- EDITAR -->
      <div id="EditarPeca-section" class="section hidden">
          <div class="card form-card">
              <div class="form-header">
                  <h2>Editar Peça</h2>
                  <p>Atualize os dados da peça</p>
              </div>
              <form method="POST" action="php/editarPecas.php"> <input type="hidden" name="id" id="edit-id">
                  <div class="form-grid">
                      <div class="form-group"> <label>Nome</label> <input type="text" name="nome" id="edit-nome"> </div>
                      <div class="form-group"> <label>Marca</label> <input type="text" name="marca" id="edit-marca"> </div>
                      <div class="form-group"> <label>Preço Médio</label> <input type="number" step="0.01" name="preco" id="edit-preco"> </div>
                      <div class="form-group"> <label>Categoria</label> <input type="text" name="categoria" id="edit-categoria"> </div>
                      <div class="form-group"> <label>Estoque</label> <input type="number" name="estoque" id="edit-estoque"> </div>
                      <div class="form-group"> <label>Localização</label> <input type="text" name="localizacao" id="edit-localizacao"> </div>
                      <div class="form-group"> <label>Descrição</label> <textarea name="descricao" id="edit-descricao" rows="3"></textarea> </div>
                  </div>
                  <div class="form-actions"> <button type="button" class="btn-red" onclick="showSection('pecas')"> Cancelar </button> <button type="submit" class="btn-green"> Atualizar Peça </button> </div>
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

    function openEditPeca(
        id,
        nome,
        marca,
        preco,
        categoria,
        estoque,
        localizacao,
        descricao
    ) {

        showSection('EditarPeca');

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nome').value = nome;
        document.getElementById('edit-marca').value = marca;
        document.getElementById('edit-preco').value = preco;
        document.getElementById('edit-categoria').value = categoria;
        document.getElementById('edit-estoque').value = estoque;
        document.getElementById('edit-localizacao').value = localizacao;
        document.getElementById('edit-descricao').value = descricao;
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