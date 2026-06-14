<!DOCTYPE html>
<html lang="pt-br">

<?php
require_once("../Conexao/conexao.php");
session_start();

// Na sua aplicação real: $_SESSION['codusuario']
$id_logado = $_SESSION['codusuario'] ?? 1;

// Busca dados do usuário logado
$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE codusuario = :id");
$stmt->execute([':id' => $id_logado]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: ../index.html");
    exit;
}

// Ordens abertas por este usuário
$stmtOrdens = $conexao->prepare(
    "SELECT COUNT(*) FROM ordens_servico WHERE usuario_abertura = :id"
);
$stmtOrdens->execute([':id' => $id_logado]);
$total_ordens = $stmtOrdens->fetchColumn();

// Clientes cadastrados por este usuário
$stmtClientes = $conexao->prepare(
    "SELECT COUNT(*) FROM clientes WHERE usuario_cadastro = :id"
);
$stmtClientes->execute([':id' => $id_logado]);
$total_clientes = $stmtClientes->fetchColumn();

// Tempo no sistema
$conta_criada = new DateTime($usuario['conta_criada']);
$hoje = new DateTime();
$diff = $hoje->diff($conta_criada);
if ($diff->y >= 1) {
    $tempo_sistema = $diff->y . ' ano' . ($diff->y > 1 ? 's' : '');
} elseif ($diff->m >= 1) {
    $tempo_sistema = $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '');
} else {
    $tempo_sistema = $diff->days . ' dia' . ($diff->days != 1 ? 's' : '');
}

// Último login
$ultimo_login_fmt = 'Nunca acessou';
if (!empty($usuario['ultimo_login'])) {
    $dt = new DateTime($usuario['ultimo_login']);
    $ultimo_login_fmt = $dt->format('d/m/Y') . ' às ' . $dt->format('H:i');
}

// Conta criada
$conta_criada_fmt = $conta_criada->format('d/m/Y');

// Senha expira
$senha_expira_fmt = '—';
$senha_alerta = false;
$senha_expirou = false;
if (!empty($usuario['senha_expira_em'])) {
    $dt_exp = new DateTime($usuario['senha_expira_em']);
    $diff_exp = $hoje->diff($dt_exp);
    $senha_expirou = $hoje > $dt_exp;
    $senha_alerta  = !$senha_expirou && $diff_exp->days <= 30;
    $senha_expira_fmt = $dt_exp->format('d/m/Y');
}

// Mapeamento de nível
$nivel = $usuario['nivel_usuario'];
$nivel_labels = [
    'DEV'       => ['label' => 'Desenvolvedor',         'icon' => 'fa-code',     'cor' => '#7c3aed'],
    'ADMIN'     => ['label' => 'Administrador',          'icon' => 'fa-crown',    'cor' => '#dc2626'],
    'GERENTE'   => ['label' => 'Gerente',                'icon' => 'fa-briefcase','cor' => '#d97706'],
    'MECANICO'  => ['label' => 'Mecânico',               'icon' => 'fa-wrench',   'cor' => '#2563eb'],
    'ATENDENTE' => ['label' => 'Atendente',              'icon' => 'fa-headset',  'cor' => '#16a34a'],
];
$nivel_info = $nivel_labels[$nivel] ?? ['label' => $nivel, 'icon' => 'fa-user', 'cor' => '#64748b'];

// Iniciais para avatar
$partes = explode(' ', trim($usuario['nome']));
$iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));

// Mensagens de feedback
$sucesso = $_GET['sucesso'] ?? '';
$erro    = $_GET['erro'] ?? '';
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil – ERP Isaias Motos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">

  <style>
    /* ── Variáveis específicas desta página ── */
    :root {
      --nivel-cor: <?= $nivel_info['cor'] ?>;
    }

    /* ── Toast de feedback ── */
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 12px 20px;
      border-radius: 8px;
      font-size: 0.88rem;
      font-weight: 600;
      z-index: 9999;
      display: flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      animation: slideIn 0.3s ease, fadeOut 0.4s ease 3.5s forwards;
    }
    .toast-sucesso { background: #16a34a; color: #fff; }
    .toast-erro    { background: #dc2626; color: #fff; }
    @keyframes slideIn  { from { transform: translateX(120%); opacity:0; } to { transform: translateX(0); opacity:1; } }
    @keyframes fadeOut  { to   { opacity: 0; transform: translateX(120%); } }

    /* ── Hero do perfil ── */
    .perfil-hero {
      display: flex;
      align-items: center;
      gap: 24px;
      padding: 28px;
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
      margin-bottom: 20px;
      position: relative;
      overflow: hidden;
    }
    /* Faixa de cor do nível no topo do card */
    .perfil-hero::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: var(--nivel-cor);
    }

    /* Avatar com iniciais */
    .perfil-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--nivel-cor);
      color: #fff;
      font-size: 1.8rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      letter-spacing: -1px;
      box-shadow: 0 4px 16px color-mix(in srgb, var(--nivel-cor) 35%, transparent);
    }

    .perfil-info { flex: 1; }
    .perfil-info h2 {
      font-size: 1.4rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 4px;
    }

    .badge-nivel-hero {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      background: color-mix(in srgb, var(--nivel-cor) 12%, #fff);
      color: var(--nivel-cor);
      border: 1px solid color-mix(in srgb, var(--nivel-cor) 35%, transparent);
      margin-bottom: 10px;
    }

    .perfil-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      font-size: 0.82rem;
      color: #64748b;
    }
    .perfil-meta span { display: flex; align-items: center; gap: 6px; }
    .perfil-meta i { color: #94a3b8; }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.78rem;
      font-weight: 600;
      color: #16a34a;
    }
    .status-dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: #16a34a;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%,100% { box-shadow: 0 0 0 0 rgba(22,163,74,0.4); }
      50%      { box-shadow: 0 0 0 5px rgba(22,163,74,0); }
    }

    /* ── Grid de stats ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }
    @media (max-width: 900px)  { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 500px)  { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      transition: box-shadow 0.2s;
    }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.09); }

    .stat-icon {
      width: 48px; height: 48px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem;
      flex-shrink: 0;
    }
    .stat-icon.blue   { background: #eff6ff; color: #2563eb; }
    .stat-icon.purple { background: #f5f3ff; color: #7c3aed; }
    .stat-icon.yellow { background: #fffbeb; color: #d97706; }
    .stat-icon.green  { background: #f0fdf4; color: #16a34a; }
    .stat-icon.nivel  { background: color-mix(in srgb, var(--nivel-cor) 10%, #fff); color: var(--nivel-cor); }

    .stat-texto span {
      display: block;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #94a3b8;
      margin-bottom: 2px;
    }
    .stat-texto h3 {
      font-size: 1.4rem;
      font-weight: 800;
      color: #0f172a;
      margin: 0;
    }

    /* ── Grid inferior (dados + ações) ── */
    .inferior-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    @media (max-width: 768px) { .inferior-grid { grid-template-columns: 1fr; } }

    /* ── Card de dados ── */
    .dados-card {
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      padding: 24px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .dados-card h3 {
      font-size: 0.95rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f1f5f9;
    }
    .dado-linha {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      padding: 12px 0;
      border-bottom: 1px solid #f8fafc;
    }
    .dado-linha:last-child { border-bottom: none; }
    .dado-icone {
      width: 34px; height: 34px;
      border-radius: 8px;
      background: #f8fafc;
      display: flex; align-items: center; justify-content: center;
      color: #64748b;
      font-size: 0.85rem;
      flex-shrink: 0;
    }
    .dado-texto label {
      display: block;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #94a3b8;
      margin-bottom: 2px;
    }
    .dado-texto p {
      margin: 0;
      font-size: 0.9rem;
      font-weight: 600;
      color: #1e293b;
    }
    .senha-alerta-text { color: #d97706 !important; }
    .senha-expirou-text { color: #dc2626 !important; }

    /* ── Card de ações ── */
    .acoes-card {
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      padding: 24px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      display: flex;
      flex-direction: column;
    }
    .acoes-card h3 {
      font-size: 0.95rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f1f5f9;
    }
    .acoes-lista { display: flex; flex-direction: column; gap: 10px; flex: 1; }

    .acao-btn {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 16px;
      border-radius: 10px;
      border: 1px solid #e2e8f0;
      background: #f8fafc;
      cursor: pointer;
      transition: all 0.18s;
      text-decoration: none;
      color: #1e293b;
      font-size: 0.9rem;
      font-weight: 600;
      width: 100%;
      text-align: left;
    }
    .acao-btn:hover { background: #f1f5f9; border-color: #cbd5e1; transform: translateX(2px); }
    .acao-btn .acao-icon {
      width: 36px; height: 36px;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 0.9rem;
      flex-shrink: 0;
    }
    .acao-btn .acao-desc {
      font-size: 0.75rem;
      font-weight: 400;
      color: #64748b;
      display: block;
      margin-top: 1px;
    }
    .acao-btn .acao-arrow { margin-left: auto; color: #cbd5e1; font-size: 0.8rem; }

    .acao-btn.verde  .acao-icon { background: #f0fdf4; color: #16a34a; }
    .acao-btn.azul   .acao-icon { background: #eff6ff; color: #2563eb; }
    .acao-btn.vermelho { border-color: #fecaca; }
    .acao-btn.vermelho:hover { background: #fef2f2; border-color: #f87171; }
    .acao-btn.vermelho .acao-icon { background: #fef2f2; color: #dc2626; }
    .acao-btn.vermelho .acao-arrow { color: #fca5a5; }

    /* ── Modal alterar senha ── */
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
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .modal-box h3 { margin: 0 0 6px; font-size: 1.1rem; color: #0f172a; }
    .modal-box p  { margin: 0 0 20px; font-size: 0.85rem; color: #64748b; }
    .modal-campo { margin-bottom: 14px; }
    .modal-campo label { display: block; font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .modal-campo input {
      width: 100%; padding: 10px 12px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 0.9rem;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.15s;
    }
    .modal-campo input:focus { border-color: #2563eb; }
    .modal-acoes { display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end; }
    .modal-erro { color: #dc2626; font-size: 0.8rem; margin-top: 8px; display: none; }
  </style>
</head>

<body>

  <!-- ── Toasts de feedback ── -->
  <?php if ($sucesso === 'senha'): ?>
    <div class="toast toast-sucesso"><i class="fa-solid fa-check-circle"></i> Senha alterada com sucesso!</div>
  <?php elseif ($sucesso === 'perfil'): ?>
    <div class="toast toast-sucesso"><i class="fa-solid fa-check-circle"></i> Perfil atualizado com sucesso!</div>
  <?php elseif ($erro === 'senha_atual'): ?>
    <div class="toast toast-erro"><i class="fa-solid fa-circle-xmark"></i> Senha atual incorreta.</div>
  <?php elseif ($erro === 'senha_diferente'): ?>
    <div class="toast toast-erro"><i class="fa-solid fa-circle-xmark"></i> As senhas novas não coincidem.</div>
  <?php endif; ?>

  <!-- ── Overlay ── -->
  <div id="overlay" class="overlay" onclick="closeMenu()"></div>

  <!-- ── Sidebar ── -->
  <div id="sidebar" class="sidebar hidden-mobile">
    <div style="height:80px;display:flex;align-items:center;justify-content:center;background:#020617">
      <img src="../Imagens/IsaiasmotosW.png" style="height:50px;margin-right:10px">
    </div>
    <nav>
      <ul>
        <li><a href="Inicio.php"><i class="fa-solid fa-bars"></i>Dashboard</a></li>
        <li><a href="clientes.php"><i class="fa-solid fa-users"></i>Clientes</a></li>
        <li><a href="motos.php"><i class="fa-solid fa-motorcycle"></i>Motos</a></li>
        <li><a href="pecas.php"><i class="fa-solid fa-gear"></i>Peças</a></li>
        <li><a href="servicos.php"><i class="fa-solid fa-wrench"></i>Serviços</a></li>
        <li><a href="ordens.php"><i class="fa-solid fa-clipboard"></i>Ordens</a></li>
        <li><a href="relatorios.php"><i class="fa-solid fa-file-lines"></i>Relatórios</a></li>
        <li><a href="meu-usuario.php" class="active"><i class="fa-solid fa-user"></i>Meu Usuário</a></li>
        <li><a href="usuarios.php"><i class="fa-solid fa-users"></i>Usuários</a></li>
        <li><a href="configuracoes.php"><i class="fa-solid fa-gear"></i>Configurações</a></li>
      </ul>
    </nav>
  </div>

  <!-- ── Conteúdo ── -->
  <div class="main-content">

    <header class="main-header">
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
      <h2 id="page-title">Oficina Digital</h2>
      <span id="current-date"></span>
    </header>

    <main>
      <div id="usuario-section" class="section">

        <!-- ── Hero ── -->
        <div class="perfil-hero">
          <div class="perfil-avatar"><?= $iniciais ?></div>

          <div class="perfil-info">
            <div class="badge-nivel-hero">
              <i class="fa-solid <?= $nivel_info['icon'] ?>"></i>
              <?= $nivel_info['label'] ?>
            </div>
            <h2><?= htmlspecialchars($usuario['nome']) ?></h2>
            <div class="perfil-meta">
              <span><i class="fa-solid fa-envelope"></i><?= htmlspecialchars($usuario['email']) ?></span>
              <span><i class="fa-solid fa-phone"></i><?= htmlspecialchars($usuario['telefone'] ?? '—') ?></span>
              <span class="status-pill"><span class="status-dot"></span>Online agora</span>
            </div>
          </div>
        </div>

        <!-- ── Stats ── -->
        <div class="stats-grid">

          <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-file-lines"></i></div>
            <div class="stat-texto">
              <span>Ordens Abertas</span>
              <h3><?= $total_ordens ?></h3>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-users"></i></div>
            <div class="stat-texto">
              <span>Clientes Cadastrados</span>
              <h3><?= $total_clientes ?></h3>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon purple"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="stat-texto">
              <span>Tempo no Sistema</span>
              <h3><?= $tempo_sistema ?></h3>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon nivel"><i class="fa-solid <?= $nivel_info['icon'] ?>"></i></div>
            <div class="stat-texto">
              <span>Nível de Acesso</span>
              <h3><?= $nivel ?></h3>
            </div>
          </div>

        </div>

        <!-- ── Grid inferior ── -->
        <div class="inferior-grid">

          <!-- Dados da conta -->
          <div class="dados-card">
            <h3><i class="fa-solid fa-circle-info" style="color:#2563eb;margin-right:8px"></i>Dados da Conta</h3>

            <div class="dado-linha">
              <div class="dado-icone"><i class="fa-solid fa-envelope"></i></div>
              <div class="dado-texto">
                <label>Email</label>
                <p><?= htmlspecialchars($usuario['email']) ?></p>
              </div>
            </div>

            <div class="dado-linha">
              <div class="dado-icone"><i class="fa-solid fa-phone"></i></div>
              <div class="dado-texto">
                <label>Telefone</label>
                <p><?= htmlspecialchars($usuario['telefone'] ?? '—') ?></p>
              </div>
            </div>

            <div class="dado-linha">
              <div class="dado-icone"><i class="fa-solid fa-clock"></i></div>
              <div class="dado-texto">
                <label>Último Login</label>
                <p><?= $ultimo_login_fmt ?></p>
              </div>
            </div>

            <div class="dado-linha">
              <div class="dado-icone"><i class="fa-solid fa-calendar-plus"></i></div>
              <div class="dado-texto">
                <label>Conta Criada Em</label>
                <p><?= $conta_criada_fmt ?></p>
              </div>
            </div>

            <div class="dado-linha">
              <div class="dado-icone"><i class="fa-solid fa-key"></i></div>
              <div class="dado-texto">
                <label>Senha Expira Em</label>
                <p class="<?= $senha_expirou ? 'senha-expirou-text' : ($senha_alerta ? 'senha-alerta-text' : '') ?>">
                  <?php if ($senha_expirou): ?>
                    <i class="fa-solid fa-circle-exclamation"></i> Senha expirada — redefina agora
                  <?php elseif ($senha_alerta): ?>
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= $senha_expira_fmt ?> (expira em breve)
                  <?php else: ?>
                    <?= $senha_expira_fmt ?>
                  <?php endif; ?>
                </p>
              </div>
            </div>

          </div>

          <!-- Ações -->
          <div class="acoes-card">
            <h3><i class="fa-solid fa-sliders" style="color:#7c3aed;margin-right:8px"></i>Gerenciar Conta</h3>

            <div class="acoes-lista">

              <button class="acao-btn verde" onclick="abrirModalSenha()">
                <div class="acao-icon"><i class="fa-solid fa-key"></i></div>
                <div>
                  Alterar Senha
                  <span class="acao-desc">Defina uma nova senha para sua conta</span>
                </div>
                <i class="fa-solid fa-chevron-right acao-arrow"></i>
              </button>

              <button class="acao-btn azul" onclick="showSection('EditarPerfil')">
                <div class="acao-icon"><i class="fa-solid fa-user-pen"></i></div>
                <div>
                  Editar Perfil
                  <span class="acao-desc">Atualize nome, email e telefone</span>
                </div>
                <i class="fa-solid fa-chevron-right acao-arrow"></i>
              </button>

              <a class="acao-btn azul" href="../index.html">
                <div class="acao-icon"><i class="fa-solid fa-right-left"></i></div>
                <div>
                  Trocar Conta
                  <span class="acao-desc">Entrar com outro usuário</span>
                </div>
                <i class="fa-solid fa-chevron-right acao-arrow"></i>
              </a>

              <a class="acao-btn vermelho" href="#">
                <div class="acao-icon"><i class="fa-solid fa-right-from-bracket"></i></div>
                <div>
                  Logout
                  <span class="acao-desc">Encerrar sessão atual</span>
                </div>
                <i class="fa-solid fa-chevron-right acao-arrow"></i>
              </a>

            </div>
          </div>

        </div>

        <!-- ── Seção Editar Perfil (inline) ── -->
        <div id="EditarPerfil-section" class="section hidden" style="margin-top:20px">
          <div class="dados-card">
            <h3><i class="fa-solid fa-user-pen" style="color:#2563eb;margin-right:8px"></i>Editar Perfil</h3>
            <form method="POST" action="php/editarMeuPerfil.php">
              <div class="form-grid">
                <div class="form-group">
                  <label>Nome Completo</label>
                  <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                </div>
                <div class="form-group">
                  <label>Telefone</label>
                  <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>">
                </div>
              </div>
              <div class="form-actions">
                <button type="button" class="btn-red" onclick="showSection('usuario')">Cancelar</button>
                <button type="submit" class="btn-green">Salvar Alterações</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- ── Modal Alterar Senha ── -->
  <div id="modal-senha" class="modal-overlay">
    <div class="modal-box">
      <h3><i class="fa-solid fa-key" style="color:#16a34a;margin-right:8px"></i>Alterar Senha</h3>
      <p>Preencha os campos abaixo para definir uma nova senha.</p>
      <form method="POST" action="php/alterarSenha.php" onsubmit="return validarSenha()">
        <div class="modal-campo">
          <label>Senha Atual</label>
          <input type="password" name="senha_atual" id="senha_atual" placeholder="••••••••">
        </div>
        <div class="modal-campo">
          <label>Nova Senha</label>
          <input type="password" name="nova_senha" id="nova_senha" placeholder="••••••••">
        </div>
        <div class="modal-campo">
          <label>Confirmar Nova Senha</label>
          <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="••••••••">
        </div>
        <p class="modal-erro" id="modal-erro-texto"></p>
        <div class="modal-acoes">
          <button type="button" class="btn-red" onclick="fecharModalSenha()">Cancelar</button>
          <button type="submit" class="btn-green">Alterar Senha</button>
        </div>
      </form>
    </div>
  </div>

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

    // ── Modal senha ──
    function abrirModalSenha() {
      document.getElementById('modal-senha').classList.add('active')
    }
    function fecharModalSenha() {
      document.getElementById('modal-senha').classList.remove('active')
    }
    function validarSenha() {
      const nova      = document.getElementById('nova_senha').value
      const confirmar = document.getElementById('confirmar_senha').value
      const erroEl    = document.getElementById('modal-erro-texto')
      if (nova !== confirmar) {
        erroEl.textContent = 'As senhas não coincidem.'
        erroEl.style.display = 'block'
        return false
      }
      if (nova.length < 6) {
        erroEl.textContent = 'A senha precisa ter ao menos 6 caracteres.'
        erroEl.style.display = 'block'
        return false
      }
      erroEl.style.display = 'none'
      return true
    }

    // ── Fechar modal clicando fora ──
    document.getElementById('modal-senha').addEventListener('click', function(e) {
      if (e.target === this) fecharModalSenha()
    })

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
      const el = document.getElementById('current-date')
      if (el) el.textContent = now.toLocaleDateString('pt-BR', {
        weekday:'long', year:'numeric', month:'long', day:'numeric'
      })
    }
  </script>

</body>
</html>
