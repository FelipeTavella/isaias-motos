<!DOCTYPE html>
<html lang="pt-br">

<?php require_once("../Conexao/conexao.php"); ?>

<?php
// =========================================================
// HIERARQUIA DE PERMISSÕES
// =========================================================
// Para testes da sessão do usuário logado
// Na sua aplicação real, substitua por: $_SESSION['nivel_usuario']
// =========================================================

session_start();

// Para testar, defina manualmente o nível:
// $_SESSION['nivel_usuario'] = 'ADMIN';
$nivel_logado = $_SESSION['nivel_usuario'] ?? 'ADMIN';

// Define os poderes de cada nível
$pode_adicionar = in_array($nivel_logado, ['DEV', 'ADMIN']);
$pode_editar    = in_array($nivel_logado, ['DEV', 'ADMIN', 'GERENTE']);
$pode_excluir   = in_array($nivel_logado, ['DEV', 'ADMIN']);
$pode_ver_senha = in_array($nivel_logado, ['DEV']);
$pode_alterar_nivel = in_array($nivel_logado, ['DEV', 'ADMIN']);

// Definição dos níveis disponíveis no ENUM
$niveis_disponiveis = ['ADMIN', 'GERENTE', 'MECANICO', 'ATENDENTE'];
if ($nivel_logado === 'DEV') {
    $niveis_disponiveis = ['DEV', 'ADMIN', 'GERENTE', 'MECANICO', 'ATENDENTE'];
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERP - Oficina Mecânica</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css">

  <style>
    /* ── Badges de nível ── */
    .badge-nivel {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      white-space: nowrap;
    }
    .badge-DEV      { background: #7c3aed22; color: #7c3aed; border: 1px solid #7c3aed55; }
    .badge-ADMIN    { background: #dc262622; color: #dc2626; border: 1px solid #dc262655; }
    .badge-GERENTE  { background: #d9770622; color: #d97706; border: 1px solid #d9770655; }
    .badge-MECANICO { background: #2563eb22; color: #2563eb; border: 1px solid #2563eb55; }
    .badge-ATENDENTE{ background: #16a34a22; color: #16a34a; border: 1px solid #16a34a55; }

    /* ── Status de último login ── */
    .login-hoje    { color: #16a34a; font-weight: 600; }
    .login-recente { color: #d97706; }
    .login-antigo  { color: #9ca3af; }

    /* ── Senha expirando ── */
    .senha-ok      { color: #16a34a; }
    .senha-alerta  { color: #d97706; font-weight: 600; }
    .senha-expirou { color: #dc2626; font-weight: 700; }

    /* ── Info de permissão da sessão ── */
    .nivel-sessao-info {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 16px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-size: 0.82rem;
      color: #64748b;
      margin-bottom: 16px;
    }
    .nivel-sessao-info strong { color: #1e293b; }

    /* ── Permissões bloqueadas ── */
    .btn-bloqueado {
      opacity: 0.3;
      cursor: not-allowed;
      pointer-events: none;
    }

    /* ── Aviso de permissão ── */
    .permissao-tag {
      font-size: 0.7rem;
      color: #94a3b8;
      font-style: italic;
    }

    /* ── Modal de confirmação exclusão ── */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      z-index: 9999;
      align-items: center;
      justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
      background: #fff;
      border-radius: 12px;
      padding: 32px 28px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.2);
      text-align: center;
    }
    .modal-box i { font-size: 2.5rem; color: #dc2626; margin-bottom: 12px; }
    .modal-box h3 { font-size: 1.1rem; color: #1e293b; margin-bottom: 8px; }
    .modal-box p  { font-size: 0.88rem; color: #64748b; margin-bottom: 24px; }
    .modal-actions { display: flex; gap: 10px; justify-content: center; }

    /* ── Coluna senha expira (só DEV vê) ── */
    .col-senha-dev { display: <?= $pode_ver_senha ? 'table-cell' : 'none' ?>; }

    /* ── Conta criada formatada ── */
    .data-criacao { font-size: 0.8rem; color: #94a3b8; }
  </style>
</head>

<body>

  <!-- ------------------------------------------------ Overlay ------------------------------------------------ -->
  <div id="overlay" class="overlay" onclick="closeMenu()"></div>

  <!-- ------------------------------------------------ Sidebar ------------------------------------------------ -->
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
        <li><a href="meu-usuario.php"><i class="fa-solid fa-user"></i>Meu Usuário</a></li>
        <li><a href="usuarios.php" class="active"><i class="fa-solid fa-users"></i>Usuários</a></li>
        <li><a href="configuracoes.php"><i class="fa-solid fa-gear"></i>Configurações</a></li>
      </ul>
    </nav>
  </div>

  <!-- ------------------------------------------------ Conteúdo ------------------------------------------------ -->
  <div class="main-content">

    <header class="main-header">
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
      <h2 id="page-title">Oficina Digital</h2>
      <span id="current-date"></span>
    </header>

    <main>

      <!-- ================================================ LISTAGEM ================================================ -->
      <div id="usuarios-section" class="section">
        <div class="card">

          <div id="TitleCard">
            <h3>Usuários</h3>
            <?php if ($pode_adicionar): ?>
              <button class="btn-blue" onclick="showSection('CadastroUsuario')">
                <i class="fa-solid fa-plus"></i> Novo Usuário
              </button>
            <?php else: ?>
              <span class="permissao-tag">
                <i class="fa-solid fa-lock"></i> Sem permissão para cadastrar
              </span>
            <?php endif; ?>
          </div>

          <!-- Info da sessão atual -->
          <div class="nivel-sessao-info">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Nível da sessão: <strong><?= htmlspecialchars($nivel_logado) ?></strong></span>
            <span>—</span>
            <span>
              <?= $pode_adicionar ? '✅ Cadastrar' : '🔒 Cadastrar' ?> &nbsp;
              <?= $pode_editar    ? '✅ Editar'    : '🔒 Editar' ?> &nbsp;
              <?= $pode_excluir   ? '✅ Excluir'   : '🔒 Excluir' ?>
              <?php if ($pode_ver_senha): ?>
                &nbsp; ✅ Ver expiração de senha
              <?php endif; ?>
            </span>
          </div>

          <div class="BarraPesquisa">
            <input type="text" placeholder="Buscar usuários..." id="search-input" oninput="filtrarTabela()">
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
                  <th>Email</th>
                  <th>Telefone</th>
                  <th>Nível</th>
                  <th>Último Login</th>
                  <th>Conta Criada</th>
                  <th class="col-senha-dev">Senha Expira</th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>

              <tbody id="usuarios-table">
              <?php
                $sqlUsuarios = "SELECT * FROM usuarios ORDER BY codusuario ASC";
                $usuarios = $conexao->query($sqlUsuarios)->fetchAll(PDO::FETCH_ASSOC);

                $icones_nivel = [
                  'DEV'       => 'fa-code',
                  'ADMIN'     => 'fa-crown',
                  'GERENTE'   => 'fa-briefcase',
                  'MECANICO'  => 'fa-wrench',
                  'ATENDENTE' => 'fa-headset',
                ];

                $hoje = new DateTime();

                foreach ($usuarios as $u):
                  $nivel = $u['nivel_usuario'];
                  $icone = $icones_nivel[$nivel] ?? 'fa-user';

                  // Último login
                  $ultimo_login_fmt = '—';
                  $login_class = 'login-antigo';
                  if (!empty($u['ultimo_login'])) {
                    $dt_login = new DateTime($u['ultimo_login']);
                    $diff = $hoje->diff($dt_login);
                    if ($diff->days == 0) {
                      $ultimo_login_fmt = 'Hoje às ' . $dt_login->format('H:i');
                      $login_class = 'login-hoje';
                    } elseif ($diff->days <= 3) {
                      $ultimo_login_fmt = 'Há ' . $diff->days . ' dia(s)';
                      $login_class = 'login-recente';
                    } else {
                      $ultimo_login_fmt = $dt_login->format('d/m/Y H:i');
                      $login_class = 'login-antigo';
                    }
                  }

                  // Conta criada
                  $conta_criada_fmt = '—';
                  if (!empty($u['conta_criada'])) {
                    $dt_criada = new DateTime($u['conta_criada']);
                    $conta_criada_fmt = $dt_criada->format('d/m/Y');
                  }

                  // Senha expira (só exibido para DEV)
                  $senha_fmt = '—';
                  $senha_class = 'senha-ok';
                  if (!empty($u['senha_expira_em'])) {
                    $dt_senha = new DateTime($u['senha_expira_em']);
                    $diff_senha = $hoje->diff($dt_senha);
                    $expirou = $hoje > $dt_senha;
                    if ($expirou) {
                      $senha_fmt = 'Expirada';
                      $senha_class = 'senha-expirou';
                    } elseif ($diff_senha->days <= 30) {
                      $senha_fmt = 'Expira em ' . $diff_senha->days . ' dias';
                      $senha_class = 'senha-alerta';
                    } else {
                      $senha_fmt = $dt_senha->format('d/m/Y');
                      $senha_class = 'senha-ok';
                    }
                  }
              ?>
              <tr>
                <td><?= str_pad($u['codusuario'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['telefone'] ?? '—') ?></td>
                <td>
                  <span class="badge-nivel badge-<?= $nivel ?>">
                    <i class="fa-solid <?= $icone ?>"></i>
                    <?= $nivel ?>
                  </span>
                </td>
                <td class="<?= $login_class ?>"><?= $ultimo_login_fmt ?></td>
                <td class="data-criacao"><?= $conta_criada_fmt ?></td>
                <td class="col-senha-dev <?= $senha_class ?>"><?= $senha_fmt ?></td>

                <!-- Botão Editar -->
                <td>
                  <?php if ($pode_editar): ?>
                    <a href="#" onclick="openEditUsuario(
                      '<?= $u['codusuario'] ?>',
                      '<?= addslashes($u['nome']) ?>',
                      '<?= addslashes($u['email']) ?>',
                      '<?= addslashes($u['telefone'] ?? '') ?>',
                      '<?= $u['nivel_usuario'] ?>',
                      '<?= $u['senha_expira_em'] ?? '' ?>'
                    )">
                      <i style="color:#2563eb" class="fa-solid fa-pencil"></i>
                    </a>
                  <?php else: ?>
                    <i class="fa-solid fa-pencil btn-bloqueado"></i>
                  <?php endif; ?>
                </td>

                <!-- Botão Excluir -->
                <td>
                  <?php if ($pode_excluir): ?>
                    <a href="#" onclick="confirmarExclusao('<?= $u['codusuario'] ?>', '<?= addslashes($u['nome']) ?>')">
                      <i style="color:#dc2626" class="fa-solid fa-trash"></i>
                    </a>
                  <?php else: ?>
                    <i class="fa-solid fa-trash btn-bloqueado"></i>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>


      <!-- ================================================ CADASTRO ================================================ -->
      <?php if ($pode_adicionar): ?>
      <div id="CadastroUsuario-section" class="section hidden">
        <div class="card form-card">

          <div class="form-header">
            <h2>Cadastrar Usuário</h2>
            <p>Preencha os dados do novo usuário do sistema</p>
          </div>

          <form method="POST" action="php/cadastrarUsuario.php">

            <div class="form-grid">

              <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required>
              </div>

              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
              </div>

              <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone">
              </div>

              <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
              </div>

              <div class="form-group">
                <label>Nível de Acesso</label>
                <select name="nivel_usuario">
                  <?php foreach ($niveis_disponiveis as $n): ?>
                    <option value="<?= $n ?>"><?= $n ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <?php if ($pode_ver_senha): ?>
              <div class="form-group">
                <label>Senha Expira Em</label>
                <input type="date" name="senha_expira_em">
              </div>
              <?php endif; ?>

            </div>

            <div class="form-actions">
              <button type="button" class="btn-red" onclick="showSection('usuarios')">Cancelar</button>
              <button type="reset" class="btn-blue">Limpar</button>
              <button type="submit" class="btn-green">Salvar Usuário</button>
            </div>

          </form>
        </div>
      </div>
      <?php endif; ?>


      <!-- ================================================ EDITAR ================================================ -->
      <?php if ($pode_editar): ?>
      <div id="EditarUsuario-section" class="section hidden">
        <div class="card form-card">

          <div class="form-header">
            <h2>Editar Usuário</h2>
            <p>Atualize os dados do usuário</p>
          </div>

          <form method="POST" action="php/editarUsuario.php">

            <input type="hidden" name="id" id="edit-id">

            <div class="form-grid">

              <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" id="edit-nome" required>
              </div>

              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="edit-email" required>
              </div>

              <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone" id="edit-telefone">
              </div>

              <div class="form-group">
                <label>Nova Senha <span style="color:#9ca3af;font-size:0.8rem">(deixe em branco para manter)</span></label>
                <input type="password" name="senha" id="edit-senha">
              </div>

              <div class="form-group">
                <label>Nível de Acesso
                  <?php if (!$pode_alterar_nivel): ?>
                    <span style="color:#9ca3af;font-size:0.75rem">(somente leitura)</span>
                  <?php endif; ?>
                </label>
                <select name="nivel_usuario" id="edit-nivel" <?= !$pode_alterar_nivel ? 'disabled' : '' ?>>
                  <?php foreach ($niveis_disponiveis as $n): ?>
                    <option value="<?= $n ?>"><?= $n ?></option>
                  <?php endforeach; ?>
                </select>
                <?php if (!$pode_alterar_nivel): ?>
                  <input type="hidden" name="nivel_usuario" id="edit-nivel-hidden">
                <?php endif; ?>
              </div>

              <?php if ($pode_ver_senha): ?>
              <div class="form-group">
                <label>Senha Expira Em</label>
                <input type="date" name="senha_expira_em" id="edit-senha-expira">
              </div>
              <?php endif; ?>

            </div>

            <div class="form-actions">
              <button type="button" class="btn-red" onclick="showSection('usuarios')">Cancelar</button>
              <button type="submit" class="btn-green">Atualizar Usuário</button>
            </div>

          </form>
        </div>
      </div>
      <?php endif; ?>


    </main>
  </div>


  <!-- ================================================ MODAL EXCLUIR ================================================ -->
  <div id="modal-excluir" class="modal-overlay">
    <div class="modal-box">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <h3>Excluir usuário?</h3>
      <p id="modal-texto">Esta ação não pode ser desfeita.</p>
      <div class="modal-actions">
        <button class="btn-red" id="btn-confirmar-excluir">Sim, excluir</button>
        <button class="btn-blue" onclick="fecharModal()">Cancelar</button>
      </div>
    </div>
  </div>


  <!-- ================================================ SCRIPTS ================================================ -->
  <script>

    // ── Seções ──
    function showSection(section) {
      document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'))
      const el = document.getElementById(section + '-section')
      if (el) el.classList.remove('hidden')
      closeMenu()
    }

    // ── Sidebar ──
    function toggleMenu() {
      document.getElementById('sidebar').classList.toggle('active')
      document.getElementById('overlay').classList.toggle('active')
    }
    function closeMenu() {
      document.getElementById('sidebar').classList.remove('active')
      document.getElementById('overlay').classList.remove('active')
    }

    // ── Abrir edição ──
    function openEditUsuario(id, nome, email, telefone, nivel, senhaExpira) {
      showSection('EditarUsuario')
      document.getElementById('edit-id').value          = id
      document.getElementById('edit-nome').value        = nome
      document.getElementById('edit-email').value       = email
      document.getElementById('edit-telefone').value    = telefone

      const selectNivel = document.getElementById('edit-nivel')
      if (selectNivel) {
        selectNivel.value = nivel
      }

      // Campo hidden para gerentes (sem permissão de alterar nível)
      const hiddenNivel = document.getElementById('edit-nivel-hidden')
      if (hiddenNivel) hiddenNivel.value = nivel

      const senhaExpiraEl = document.getElementById('edit-senha-expira')
      if (senhaExpiraEl) senhaExpiraEl.value = senhaExpira
    }

    // ── Modal de exclusão ──
    let urlExclusao = ''
    function confirmarExclusao(id, nome) {
      urlExclusao = 'php/excluirUsuario.php?id=' + id
      document.getElementById('modal-texto').textContent =
        'Você está prestes a excluir o usuário "' + nome + '". Esta ação não pode ser desfeita.'
      document.getElementById('modal-excluir').classList.add('active')
    }
    function fecharModal() {
      document.getElementById('modal-excluir').classList.remove('active')
    }
    document.getElementById('btn-confirmar-excluir').addEventListener('click', () => {
      window.location.href = urlExclusao
    })

    // ── Filtro de busca ──
    function filtrarTabela() {
      const termo = document.getElementById('search-input').value.toLowerCase()
      document.querySelectorAll('#usuarios-table tr').forEach(tr => {
        const texto = tr.textContent.toLowerCase()
        tr.style.display = texto.includes(termo) ? '' : 'none'
      })
    }

    // ── Data no header ──
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
      const now = new Date()
      const opcoes = { weekday:'long', year:'numeric', month:'long', day:'numeric' }
      const el = document.getElementById('current-date')
      if (el) el.textContent = now.toLocaleDateString('pt-BR', opcoes)
    }

  </script>

</body>
</html>
