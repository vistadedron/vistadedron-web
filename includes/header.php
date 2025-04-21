<?php
// includes/header.php
// Última modif: 2025-04-21 11:20
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vista de Dron</title>
  <link rel="stylesheet" href="/styles/main.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
  <header class="main-header" id="mainHeader">
    <div class="top-bar">
      <!-- Mini‑logo móvil -->
      <img src="/assets/logo_vd_final_compacto.png"
           alt="Mini logo" class="mini-logo" id="miniLogo"/>

      <!-- Nav principal -->
      <nav class="main-nav" id="navMenu">
        <ul id="mainMenu" class="main-menu">
          <li><a href="/">Inicio</a></li>
          <li><a href="/explorar">Explorar</a></li>
          <li><a href="/galeria">Galería</a></li>
          <li><a href="/colabora">Colabora</a></li>
          <li><a href="/contacto">Contacto</a></li>
        </ul>
      </nav>

      <!-- Buscador -->
      <div class="search-box">
        <input type="text" placeholder="Buscar..."/>
        <button>🔍</button>
      </div>

      <!-- Enlaces de Auth / Menú de Usuario -->
      <?php if (empty($_SESSION['user_id'])): ?>
        <div class="auth-links">
          <a href="/login" class="auth-link"><span class="icon">🔐</span> Iniciar sesión</a>
          <a href="/registro" class="auth-link"><span class="icon">📝</span> Registrarse</a>
        </div>
      <?php else: ?>
        <div class="user-dropdown">
          <button id="userToggle" class="user-toggle">
            <?= strtoupper(substr($_SESSION['nombre'] ?? 'U', 0, 1)) ?>
          </button>
          <ul id="userMenu" class="user-menu-list">
            <li><a href="/backoffice/perfil">Configurar cuenta</a></li>
            <li><a href="/logout">Cerrar sesión</a></li>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Botón hamburguesa -->
      <button class="hamburger" id="hamburger">&#9776;</button>
    </div>

    <!-- Resto del header... -->
    <video autoplay muted loop class="header-video">
      <source src="/assets/header-video.mp4" type="video/mp4"/>
      Tu navegador no soporta el vídeo.
    </video>
    <div class="header-overlay">
      <img src="/assets/logo_vd_final.png"
           alt="Vista de Dron" class="logo-header" id="logoHeader"/>
      <div class="title-carousel" id="titleCarousel">
        <div class="phrase active">Explora España con una nueva perspectiva</div>
        <!-- ...otras frases... -->
      </div>
    </div>
  </header>

  <script>
  document.addEventListener("DOMContentLoaded", function() {
    // Carrusel
    const phrases = document.querySelectorAll('.title-carousel .phrase');
    let idx = 0;
    setInterval(() => {
      phrases[idx].classList.remove('active');
      idx = (idx + 1) % phrases.length;
      phrases[idx].classList.add('active');
    }, 4000);

    // Scroll: mini‑logo
    const header = document.getElementById('mainHeader');
    const miniLogo = document.getElementById('miniLogo');
    const video = document.querySelector('.header-video');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 150) {
        header.classList.add('compact');
        miniLogo.classList.add('visible');
        video.style.opacity = '0.15';
      } else {
        header.classList.remove('compact');
        miniLogo.classList.remove('visible');
        video.style.opacity = '1';
      }
    });

    // Hamburguesa móvil
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    hamburger.addEventListener('click', () => {
      navMenu.classList.toggle('open');
    });

    // Toggle menú usuario
    const userToggle = document.getElementById('userToggle');
    const userMenu = document.getElementById('userMenu');
    if (userToggle) {
      userToggle.addEventListener('click', e => {
        e.stopPropagation();
        userMenu.classList.toggle('open');
      });
      document.addEventListener('click', () => {
        userMenu.classList.remove('open');
      });
    }
  });
  </script>
