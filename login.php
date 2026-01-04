<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Mi Bollito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    .login-card {
      background: #fff;
      border-radius: 20px;
      padding: 2.5rem;
      width: 100%;
      max-width: 450px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.3);
      animation: slideIn 0.5s ease-out;
      position: relative;
      overflow: hidden;
    }
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .login-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(106, 17, 203, 0.1), transparent);
      animation: rotate 4s linear infinite;
    }
    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .login-header {
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
      z-index: 1;
    }
    .login-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-size: 2rem;
      color: white;
      animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    .form-control {
      border-radius: 10px;
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      transition: all 0.3s ease;
      position: relative;
      z-index: 1;
    }
    .form-control:focus {
      border-color: #6a11cb;
      box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
      transform: translateY(-2px);
    }
    .btn-login {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      border: none;
      border-radius: 10px;
      padding: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      position: relative;
      z-index: 1;
      overflow: hidden;
    }
    .btn-login::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s;
    }
    .btn-login:hover::before {
      left: 100%;
    }
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(106, 17, 203, 0.4);
    }
    .alert {
      border-radius: 10px;
      animation: slideDown 0.5s ease-out;
      position: relative;
      z-index: 1;
    }
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .input-group-text {
      background: #f8f9fa;
      border: 2px solid #e0e0e0;
      border-right: none;
      border-radius: 10px 0 0 10px;
    }
    .input-group .form-control {
      border-left: none;
      border-radius: 0 10px 10px 0;
    }
  </style>
</head>
<body>

<div class="login-card">
  <div class="login-header">
    <div class="login-icon">
      <i class="bi bi-shop"></i>
    </div>
    <h3 class="fw-bold mb-1">Mi Bollito</h3>
    <p class="text-muted">Sistema de Gestión</p>
  </div>

  <?php if (isset($_GET['mensaje'])): ?>
    <div id="alerta" class="alert alert-success text-center">
      <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_GET['mensaje']) ?>
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div id="alerta" class="alert alert-danger text-center">
      <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

  <form action="validar_login.php" method="POST">
    <div class="mb-3">
      <label for="correo" class="form-label fw-semibold">Correo electrónico</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@gmail.com" required>
      </div>
    </div>
    
    <div class="mb-4">
      <label for="contraseña" class="form-label fw-semibold">Contraseña</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="••••••••" required>
      </div>
    </div>
    
    <button type="submit" class="btn btn-login btn-primary w-100 text-white">
      <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
    </button>
  </form>

  <div class="text-center mt-4">
    <p class="text-muted small mb-0">
      <i class="bi bi-info-circle me-1"></i>
      Si necesitas una cuenta, contacta al administrador
    </p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const alerta = document.getElementById('alerta');
  if (alerta) {
    setTimeout(() => {
      alerta.style.transition = "all 0.5s ease";
      alerta.style.opacity = "0";
      alerta.style.transform = "translateY(-20px)";
      setTimeout(() => alerta.remove(), 500);
    }, 3000);
  }

  // Animación en inputs
  document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.style.transform = 'scale(1.02)';
    });
    input.addEventListener('blur', function() {
      this.parentElement.style.transform = 'scale(1)';
    });
  });
</script>

</body>
</html>