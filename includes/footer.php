</div> <!-- Cierre de content -->

<!-- Footer -->
<footer class="main-footer">
  <div class="footer-content">
    <div class="footer-info">
      <div class="footer-logo">
        <span class="footer-icon">🥟</span>
        <span class="footer-brand">Mi Bollito</span>
      </div>
      <p class="footer-text">Sistema de Gestión Empresarial</p>
      <p class="footer-copyright">© <?= date('Y') ?> Todos los derechos reservados</p>
    </div>
    
    <div class="footer-links">
      <div class="footer-section">
        <h6 class="footer-title">Navegación</h6>
        <ul class="footer-list">
          <li><a href="panel.php"><i class="bi bi-house-door me-2"></i>Inicio</a></li>
          <li><a href="inventario.php"><i class="bi bi-box-seam me-2"></i>Inventario</a></li>
          <li><a href="estadisticas.php"><i class="bi bi-graph-up me-2"></i>Estadísticas</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h6 class="footer-title">Soporte</h6>
        <ul class="footer-list">
          <li><a href="#"><i class="bi bi-question-circle me-2"></i>Ayuda</a></li>
          <li><a href="#"><i class="bi bi-file-text me-2"></i>Documentación</a></li>
          <li><a href="#"><i class="bi bi-envelope me-2"></i>Contacto</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h6 class="footer-title">Sistema</h6>
        <div class="system-stats">
          <div class="stat-item">
            <i class="bi bi-hdd-fill text-primary"></i>
            <span>v1.0.0</span>
          </div>
          <div class="stat-item">
            <i class="bi bi-clock-fill text-success"></i>
            <span id="currentTime"><?= date('H:i') ?></span>
          </div>
          <div class="stat-item">
            <i class="bi bi-calendar-fill text-info"></i>
            <span><?= date('d/m/Y') ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer-bottom">
    <div class="tech-stack">
      <span class="tech-badge">
        <i class="bi bi-server"></i> PostgreSQL
      </span>
      <span class="tech-badge">
        <i class="bi bi-filetype-php"></i> PHP
      </span>
      <span class="tech-badge">
        <i class="bi bi-bootstrap"></i> Bootstrap 5
      </span>
    </div>
    
    <div class="footer-social">
      <p class="mb-0 text-muted small">Desarrollado con <i class="bi bi-heart-fill text-danger"></i> para tu negocio</p>
    </div>
  </div>
</footer>

<!-- Botón de regreso arriba -->
<button class="btn-scroll-top" id="scrollTopBtn" onclick="scrollToTop()">
  <i class="bi bi-arrow-up"></i>
</button>

<style>
.main-footer {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  color: rgba(255, 255, 255, 0.8);
  margin-left: 280px;
  margin-top: 50px;
  border-radius: 20px 20px 0 0;
  box-shadow: 0 -5px 30px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
}

.main-footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
  background-size: 200% 100%;
  animation: gradientMove 3s ease infinite;
}

@keyframes gradientMove {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

.footer-content {
  display: grid;
  grid-template-columns: 1.5fr 2fr;
  gap: 40px;
  padding: 40px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 10px;
}

.footer-icon {
  font-size: 2rem;
  filter: drop-shadow(0 2px 8px rgba(102, 126, 234, 0.5));
}

.footer-brand {
  font-size: 1.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.footer-text {
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  font-size: 0.95rem;
}

.footer-copyright {
  color: rgba(255, 255, 255, 0.4);
  margin: 0;
  font-size: 0.85rem;
}

.footer-links {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.footer-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-title {
  color: white;
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.footer-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.footer-list a {
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  font-size: 0.9rem;
}

.footer-list a:hover {
  color: white;
  transform: translateX(5px);
}

.system-stats {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  transition: all 0.3s ease;
}

.stat-item:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateX(5px);
}

.stat-item i {
  font-size: 1.2rem;
}

.stat-item span {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.9rem;
  font-weight: 500;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px 40px;
  flex-wrap: wrap;
  gap: 20px;
}

.tech-stack {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.tech-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 15px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.tech-badge:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
}

.tech-badge i {
  font-size: 1.1rem;
}

.footer-social {
  display: flex;
  align-items: center;
}

/* Botón Scroll Top */
.btn-scroll-top {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: none;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  transition: all 0.3s ease;
  z-index: 999;
  animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.btn-scroll-top:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-scroll-top i {
  font-size: 1.3rem;
}

/* Responsive */
@media (max-width: 992px) {
  .main-footer {
    margin-left: 0;
  }
  
  .footer-content {
    grid-template-columns: 1fr;
    padding: 30px 20px;
  }
  
  .footer-links {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .footer-bottom {
    flex-direction: column;
    text-align: center;
    padding: 20px;
  }
}

@media (max-width: 768px) {
  .tech-stack {
    justify-content: center;
  }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Actualizar reloj en tiempo real
function updateTime() {
  const now = new Date();
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const timeElement = document.getElementById('currentTime');
  if (timeElement) {
    timeElement.textContent = `${hours}:${minutes}`;
  }
}

setInterval(updateTime, 1000);

// Mostrar/ocultar botón de scroll
const scrollTopBtn = document.getElementById('scrollTopBtn');

window.addEventListener('scroll', function() {
  if (window.pageYOffset > 300) {
    scrollTopBtn.style.display = 'flex';
  } else {
    scrollTopBtn.style.display = 'none';
  }
});

function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}

// Toggle sidebar en móvil
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');

// Agregar botón hamburguesa para móvil
if (window.innerWidth <= 768) {
  const hamburger = document.createElement('button');
  hamburger.className = 'btn-hamburger';
  hamburger.innerHTML = '<i class="bi bi-list"></i>';
  hamburger.style.cssText = `
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1001;
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    cursor: pointer;
  `;
  
  hamburger.addEventListener('click', function() {
    sidebar.classList.toggle('show');
  });
  
  document.body.appendChild(hamburger);
  
  // Cerrar sidebar al hacer click fuera
  content.addEventListener('click', function() {
    if (sidebar.classList.contains('show')) {
      sidebar.classList.remove('show');
    }
  });
}

// Animación de entrada para elementos
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
    }
  });
}, observerOptions);

document.querySelectorAll('.card, .table, .dashboard-card').forEach(el => {
  observer.observe(el);
});
</script>

</body>
</html>