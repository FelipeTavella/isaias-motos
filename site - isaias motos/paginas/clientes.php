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

      <div id="clientes-section" class="section">
        <div class="card">

          <div id="TitleCard">
            <h3>Clientes</h3>
            <button class="btn-blue" href="#" onclick="showSection('CadastroCliente')">Novo Cliente</button>
          </div>

          <div class="BarraPesquisa">
            <input type="text" placeholder="Buscar clientes cadastrados..." id="search-input">
            <button class="search-btn">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
          </div>

          <div id="TabelaDados">
            <table>
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Nome Completo</th>
                  <th>Email</th>
                  <th>Telefone</th>
                  <th>CPF</th>
                  <th>Endereço</th>
                  <th>Bairro</th>
                  <th>Cidade</th>
                  <th>Estado</th>
                  <th>CEP</th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>


              <tbody id="clientes-table">

              <?php

              $sqlClientes = "SELECT * FROM clientes ORDER BY coduser DESC";
              $clientes = $conexao->query($sqlClientes)->fetchAll(PDO::FETCH_ASSOC);

              foreach($clientes as $cliente):
              ?>

              <tr>
                  <td><?= str_pad($cliente['coduser'], 3, '0', STR_PAD_LEFT) ?></td>
                  <td><?= $cliente['nome_completo'] ?></td>
                  <td><?= $cliente['email'] ?></td>
                  <td><?= $cliente['telefone'] ?></td>
                  <td><?= $cliente['cpf'] ?></td>
                  <td><?= $cliente['endereco'] ?></td>
                  <td><?= $cliente['bairro'] ?></td>
                  <td><?= $cliente['cidade'] ?></td>
                  <td><?= $cliente['estado'] ?></td>
                  <td><?= $cliente['cep'] ?></td>

                  <td>
                      <a href="#" onclick="openEditCliente(
                          '<?= $cliente['coduser'] ?>',
                          '<?= addslashes($cliente['nome_completo']) ?>',
                          '<?= $cliente['email'] ?>',
                          '<?= $cliente['telefone'] ?>',
                          '<?= $cliente['cpf'] ?>',
                          '<?= addslashes($cliente['endereco']) ?>',
                          '<?= $cliente['bairro'] ?>',
                          '<?= $cliente['cidade'] ?>',
                          '<?= $cliente['estado'] ?>',
                          '<?= $cliente['cep'] ?>'
                      )">
                          <i style="color: #2563eb;" class="fa-solid fa-pencil"></i>
                      </a>
                  </td>

                  <td>
                      <a href="php/excluirCliente.php?id=<?= $cliente['coduser'] ?>">
                          <i style="color: #dc2626;" class="fa-solid fa-trash"></i>
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

      <div id="CadastroCliente-section" class="section hidden">
        <div class="card form-card">

          <div class="form-header">
            <h2>Cadastrar Cliente</h2>
            <p>Preencha as informações abaixo</p>
          </div>

          <form method="POST" action="php/cadastrarCliente.php">

            <div class="form-grid">

              <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required>
              </div>

              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
              </div>

              <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone">
              </div>

              <div class="form-group">
                <label>CPF</label>
                <input type="text" name="cpf">
              </div>

              <div class="form-group">
                <label>Endereço</label>
                <input type="text" name="endereco">
              </div>

              <div class="form-group">
                <label>Bairro</label>
                <input type="text" name="bairro">
              </div>

              <div class="form-group">
                <label>Cidade</label>
                <input type="text" name="cidade">
              </div>

              <div class="form-group">
                <label>Estado</label>
                <input type="text" name="estado">
              </div>

              <div class="form-group">
                <label>CEP</label>
                <input type="text" name="cep">
              </div>

            </div>

            <div class="form-actions">
              <button type="button" class="btn-red" onclick="showSection('clientes')">
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


      <!-- ------------------------------------------------ EDITAR CLIENTE ------------------------------------------------ -->

<div id="EditarCliente-section" class="section hidden">
  <div class="card form-card">

    <div class="form-header">
      <h2>Editar Cliente</h2>
      <p>Atualize os dados do cliente</p>
    </div>

    <form method="POST" action="php/editarCliente.php">

      <input type="hidden" name="id" id="edit-id">

      <div class="form-grid">

        <div class="form-group">
          <label>Nome Completo</label>
          <input type="text" name="nome" id="edit-nome">
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" id="edit-email">
        </div>

        <div class="form-group">
          <label>Telefone</label>
          <input type="text" name="telefone" id="edit-telefone">
        </div>

        <div class="form-group">
          <label>CPF</label>
          <input type="text" name="cpf" id="edit-cpf">
        </div>

        <div class="form-group">
          <label>Endereço</label>
          <input type="text" name="endereco" id="edit-endereco">
        </div>

        <div class="form-group">
          <label>Bairro</label>
          <input type="text" name="bairro" id="edit-bairro">
        </div>

        <div class="form-group">
          <label>Cidade</label>
          <input type="text" name="cidade" id="edit-cidade">
        </div>

        <div class="form-group">
          <label>Estado</label>
          <input type="text" name="estado" id="edit-estado">
        </div>

        <div class="form-group">
          <label>CEP</label>
          <input type="text" name="cep" id="edit-cep">
        </div>

      </div>

      <div class="form-actions">
        <button type="button" class="btn-red" onclick="showSection('clientes')">
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

    function openEditCliente(id, nome, email, telefone, cpf, endereco, bairro, cidade, estado, cep) {
    showSection('EditarCliente')
    document.getElementById('edit-id').value = id
    document.getElementById('edit-nome').value = nome
    document.getElementById('edit-email').value = email
    document.getElementById('edit-telefone').value = telefone
    document.getElementById('edit-cpf').value = cpf
    document.getElementById('edit-endereco').value = endereco
    document.getElementById('edit-bairro').value = bairro
    document.getElementById('edit-cidade').value = cidade
    document.getElementById('edit-estado').value = estado
    document.getElementById('edit-cep').value = cep
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