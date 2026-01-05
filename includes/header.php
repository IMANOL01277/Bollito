<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['nombre'])) {
  header("Location: login.php");
  exit();
}

// Obtener la página actual para marcar el menú activo
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Bollito - Panel de Control</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }

    /* ========== SIDEBAR ========== */
    .sidebar {
      width: 280px;
      background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      flex-direction: column;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      transition: all 0.3s ease;
    }

    /* Logo y Header del Sidebar */
    .sidebar-header {
      padding: 25px 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .sidebar-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .logo-container {
      position: relative;
      z-index: 1;
    }

    .logo-icon {
      width: 60px;
      height: 60px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
      font-size: 2rem;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.3);
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }

    .sidebar-header h4 {
      color: white;
      font-weight: 700;
      font-size: 1.5rem;
      margin: 0;
      letter-spacing: -0.5px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .sidebar-header p {
      color: rgba(255, 255, 255, 0.9);
      font-size: 0.85rem;
      margin: 5px 0 0;
      font-weight: 300;
    }

    /* Navegación */
    .sidebar-nav {
      flex: 1;
      padding: 20px 15px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }

    .sidebar-nav::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
      background: transparent;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
    }

    .nav-section-title {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 25px 0 10px 15px;
      display: flex;
      align-items: center;
    }

    .nav-section-title::before {
      content: '';
      width: 3px;
      height: 12px;
      background: linear-gradient(180deg, #667eea, #764ba2);
      margin-right: 8px;
      border-radius: 2px;
    }

    .nav-link-custom {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 12px 15px;
      border-radius: 12px;
      margin-bottom: 5px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .nav-link-custom::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(180deg, #667eea, #764ba2);
      transform: scaleY(0);
      transition: transform 0.3s ease;
    }

    .nav-link-custom i {
      font-size: 1.2rem;
      margin-right: 12px;
      width: 24px;
      text-align: center;
      transition: all 0.3s ease;
    }

    .nav-link-custom:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      transform: translateX(5px);
    }

    .nav-link-custom:hover::before {
      transform: scaleY(1);
    }

    .nav-link-custom:hover i {
      transform: scale(1.1);
    }

    .nav-link-custom.active {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
      transform: translateX(5px);
    }

    .nav-link-custom.active i {
      animation: bounce 0.6s ease;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5px); }
    }

    /* Usuario Info */
    .user-info {
      padding: 20px;
      background: rgba(255, 255, 255, 0.05);
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
    }

    .user-profile {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding: 12px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .user-profile:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }

    .user-avatar {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 1.1rem;
      margin-right: 12px;
      box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
      border: 3px solid rgba(255, 255, 255, 0.2);
    }

    .user-details {
      flex: 1;
    }

    .user-name {
      color: white;
      font-weight: 600;
      font-size: 0.95rem;
      margin: 0;
      line-height: 1.3;
    }

    .user-role {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.8rem;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .role-badge {
      display: inline-flex;
      align-items: center;
      padding: 3px 8px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      font-size: 0.7rem;
    }

    .btn-logout {
      width: 100%;
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: white;
      border: none;
      padding: 12px;
      border-radius: 10px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }

    .btn-logout:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(239, 68, 68, 0.4);
    }

    /* ========== CONTENT AREA ========== */
    .content {
      margin-left: 280px;
      flex: 1;
      padding: 30px;
      min-height: 100vh;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Topbar en el contenido */
    .topbar {
      background: white;
      padding: 20px 25px;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .page-title {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .page-title i {
      font-size: 1.5rem;
      color: #667eea;
    }

    .topbar-actions {
      display: flex;
      gap: 10px;
    }

    .btn-topbar {
      padding: 10px 20px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Badges */
    .badge {
      font-weight: 600 !important;
      padding: 6px 12px !important;
      border-radius: 8px !important;
      font-size: 0.85rem !important;
    }

    .bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; }
    .bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important; }
    .bg-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; }
    .bg-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; }
    .bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }

    /* Mobile Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 0;
        transform: translateX(-100%);
      }
      
      .sidebar.show {
        width: 280px;
        transform: translateX(0);
      }
      
      .content {
        margin-left: 0;
        padding: 15px;
      }
    }

    /* Scrollbar personalizado para todo el contenido */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, #667eea, #764ba2);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, #764ba2, #667eea);
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <!-- Header -->
  <div class="sidebar-header">
    <div class="logo-container">
      <h4>Mi Bollito</h4>
      <p>Sistema de Gestión</p>
    </div>
  </div>

  <!-- Navegación -->
  <div class="sidebar-nav">
    <div class="nav-section-title">Principal</div>
    <a href="panel.php" class="nav-link-custom <?= $current_page === 'panel' ? 'active' : '' ?>">
      <i class="bi bi-house-door-fill"></i>
      <span>Inicio</span>
    </a>
    <a href="inventario.php" class="nav-link-custom <?= $current_page === 'inventario' ? 'active' : '' ?>">
      <i class="bi bi-box-seam-fill"></i>
      <span>Inventario</span>
    </a>
    <a href="estadisticas.php" class="nav-link-custom <?= $current_page === 'estadisticas' ? 'active' : '' ?>">
      <i class="bi bi-graph-up-arrow"></i>
      <span>Estadísticas</span>
    </a>

    <div class="nav-section-title">Operaciones</div>
    <a href="domicilios.php" class="nav-link-custom <?= $current_page === 'domicilios' ? 'active' : '' ?>">
      <i class="bi bi-bicycle"></i>
      <span>Domicilios</span>
    </a>
    
    <a href="devoluciones.php" class="nav-link-custom <?= $current_page === 'devoluciones' ? 'active' : '' ?>">
      <i class="bi bi-arrow-return-left"></i>
      <span>Devoluciones</span>
    </a>

    <a href="reportes.php" class="nav-link-custom <?= $current_page === 'devoluciones' ? 'active' : '' ?>">
      <i class="bi bi-clipboard-data"></i>
      <span>Reportes</span>
    </a>

    <?php if ($_SESSION['rol'] === 'administrador'): ?>
    <div class="nav-section-title">Administración</div>
    <a href="usuarios.php" class="nav-link-custom <?= $current_page === 'usuarios' ? 'active' : '' ?>">
      <i class="bi bi-people-fill"></i>
      <span>Usuarios</span>
    </a>
    <a href="categorias.php" class="nav-link-custom <?= $current_page === 'categorias' ? 'active' : '' ?>">
      <i class="bi bi-bookmark-fill"></i>
      <span>Categorías</span>
    </a>
    <a href="proveedores.php" class="nav-link-custom <?= $current_page === 'proveedores' ? 'active' : '' ?>">
      <i class="bi bi-truck"></i>
      <span>Proveedores</span>
    </a>
    <a href="vendedores.php" class="nav-link-custom <?= $current_page === 'vendedores' ? 'active' : '' ?>">
      <i class="bi bi-person-badge-fill"></i>
      <span>Vendedores</span>
    </a>
    <a href="promociones.php" class="nav-link-custom <?= $current_page === 'promociones' ? 'active' : '' ?>">
      <i class="bi bi-tag-fill"></i>
      <span>Promociones</span>
    </a>
    <?php endif; ?>
  </div>

  <!-- Usuario Info -->
  <div class="user-info">
    <div class="user-profile">
      <div class="user-avatar">
        <?= strtoupper(substr($_SESSION['nombre'], 0, 2)) ?>
      </div>
      <div class="user-details">
        <p class="user-name"><?= htmlspecialchars($_SESSION['nombre']) ?></p>
        <p class="user-role">
          <span class="role-badge">
            <?= $_SESSION['rol'] === 'administrador' ? '👑' : '👤' ?>
            <?= ucfirst($_SESSION['rol']) ?>
          </span>
        </p>
      </div>
    </div>
    <a href="logout.php" class="btn-logout">
      <i class="bi bi-box-arrow-right"></i>
      <span>Cerrar Sesión</span>
    </a>
  </div>
</div>

<!-- Content Area -->

<div class="content" id="content">
