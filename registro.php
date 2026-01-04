<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario - Mi Bollito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2575fc, #6a11cb);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .registro-card {
      background: #fff;
      border-radius: 20px;
      padding: 2.5rem;
      width: 100%;
      max-width: 500px;
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
    .registro-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(37, 117, 252, 0.1), transparent);
      animation: rotate 4s linear infinite;
    }
    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .registro-header {
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
      z-index: 1;
    }
    .registro-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #11998e, #38ef7d);
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
      border-color: #11998e;
      box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
      transform: translateY(-2px);
    }
    .progress {
      height: 8px;
      border-radius: 10px;
      overflow: hidden;
      position: relative;
      z-index: 1;
    }
    .btn-register {
      background: linear-gradient(135deg, #11998e, #38ef7d);
      border: none;
      border-radius: 10px;
      padding: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      position: relative;
      z-index: 1;
    }
    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
    }
    .btn-back {
      background: linear-gradient(135deg, #6b7280, #4b5563);
      border: none;
      border-radius: 10px;
      padding: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      position: relative;
      z-index: 1;
    }
    .btn-back:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(75, 85, 99, 0.4);
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
    .criteria-list {
      list-style: none;
      padding: 0;
      margin: 0.5rem 0 0;
      position: relative;
      z-index: 1;
    }
    .criteria-list li {
      font-size: 0.85rem;
      padding: 3px 0;
      transition: all 0.3s ease;
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

<div class="registro-card">
  <div class="registro-header">
    <div class="registro-icon">
      <i class="bi bi-person-plus-fill"></i>
    </div>
    <h3 class="fw-bold mb-1">Crear cuenta</h3>
    <p class="text-muted">Únete al sistema Mi Bollito</p>
  </div>

  <?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success text-center">
      <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_GET['mensaje']) ?>
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger text-center">
      <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

  <form action="guardar_registro.php" method="POST" id="registroForm" novalidate>
    <div class="mb-3">
      <label for="nombre" class="form-label fw-semibold">Nombre completo</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Juan Pérez" required>
      </div>
    </div>

    <div class="mb-3">
      <label for="correo" class="form-label fw-semibold">Correo electrónico</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@gmail.com" required>
      </div>
    </div>

    <div class="mb-3">
      <label for="contraseña" class="form-label fw-semibold">Contraseña</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="••••••••" required>
      </div>
      <div class="progress mt-2">
        <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
      </div>
      <ul class="criteria-list mt-2" id="passwordCriteria">
        <li id="length" class="text-danger">• Mínimo 8 caracteres</li>
        <li id="uppercase" class="text-danger">• Al menos una letra mayúscula</li>
        <li id="special" class="text-danger">• Al menos un carácter especial (!@#$%^&*)</li>
      </ul>
    </div>

    <div class="mb-4">
      <label for="confirmar" class="form-label fw-semibold">Confirmar contraseña</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
        <input type="password" class="form-control" id="confirmar" name="confirmar" placeholder="••••••••" required>
      </div>
      <div id="matchMessage" class="small mt-1"></div>
    </div>

    <button type="submit" class="btn btn-register w-100 text-white mb-2">
      <i class="bi bi-check-circle me-2"></i>Registrarse
    </button>
    
    <a href="login.php" class="btn btn-back w-100 text-white">
      <i class="bi bi-arrow-left me-2"></i>Volver al inicio de sesión
    </a>
  </form>

  <div class="text-center mt-4">
    <p class="text-muted small mb-0">
      <i class="bi bi-shield-lock me-1"></i>
      Tus datos están protegidos y encriptados
    </p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const passwordInput = document.getElementById("contraseña");
  const confirmInput = document.getElementById("confirmar");
  const form = document.getElementById("registroForm");
  const matchMessage = document.getElementById("matchMessage");
  const strengthBar = document.getElementById("passwordStrength");
  const criteria = {
    length: document.getElementById("length"),
    uppercase: document.getElementById("uppercase"),
    special: document.getElementById("special")
  };

  // 🔹 Validación de seguridad en tiempo real
  passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    let strength = 0;

    if (password.length >= 8) {
      criteria.length.classList.replace("text-danger", "text-success");
      criteria.length.innerHTML = "✅ Mínimo 8 caracteres";
      strength++;
    } else {
      criteria.length.classList.replace("text-success", "text-danger");
      criteria.length.innerHTML = "• Mínimo 8 caracteres";
    }

    if (/[A-Z]/.test(password)) {
      criteria.uppercase.classList.replace("text-danger", "text-success");
      criteria.uppercase.innerHTML = "✅ Al menos una letra mayúscula";
      strength++;
    } else {
      criteria.uppercase.classList.replace("text-success", "text-danger");
      criteria.uppercase.innerHTML = "• Al menos una letra mayúscula";
    }

    if (/[\W_]/.test(password)) {
      criteria.special.classList.replace("text-danger", "text-success");
      criteria.special.innerHTML = "✅ Al menos un carácter especial";
      strength++;
    } else {
      criteria.special.classList.replace("text-success", "text-danger");
      criteria.special.innerHTML = "• Al menos un carácter especial";
    }

    const colors = ["bg-danger", "bg-warning", "bg-success"];
    strengthBar.className = "progress-bar " + (colors[strength - 1] || "bg-danger");
    strengthBar.style.width = `${(strength / 3) * 100}%`;
    
    // Actualizar mensaje de coincidencia cuando cambia la contraseña
    if (confirmInput.value) {
      updateMatchMessage();
    }
  });

  // 🔹 Validar coincidencia de contraseñas
  confirmInput.addEventListener("input", updateMatchMessage);
  
  function updateMatchMessage() {
    if (confirmInput.value === "") {
      matchMessage.textContent = "";
      return;
    }
    
    if (confirmInput.value === passwordInput.value) {
      matchMessage.textContent = "✅ Las contraseñas coinciden";
      matchMessage.className = "small mt-1 text-success fw-semibold";
    } else {
      matchMessage.textContent = "❌ Las contraseñas no coinciden";
      matchMessage.className = "small mt-1 text-danger fw-semibold";
    }
  }

  // 🔹 Evitar envío si las contraseñas no coinciden o no cumplen requisitos
  form.addEventListener("submit", (e) => {
    const password = passwordInput.value;
    
    // Validar requisitos de contraseña
    if (password.length < 8 || !/[A-Z]/.test(password) || !/[\W_]/.test(password)) {
      e.preventDefault();
      alert("❌ La contraseña debe cumplir todos los requisitos de seguridad.");
      return;
    }
    
    // Validar coincidencia
    if (passwordInput.value !== confirmInput.value) {
      e.preventDefault();
      alert("❌ Las contraseñas no coinciden. Por favor, verifica.");
      return;
    }
  });
  
  // Animación en inputs
  document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.style.transform = 'scale(1.02)';
    });
    input.addEventListener('blur', function() {
      this.parentElement.style.transform = 'scale(1)';
    });
  });
  
  // Auto-ocultar alertas
  setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      alert.style.transition = 'all 0.5s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-20px)';
      setTimeout(() => alert.remove(), 500);
    });
  }, 4000);
</script>

</body>
</html>