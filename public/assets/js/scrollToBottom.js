/* Funcionalidad para el botón de flecha que desplaza a la siguiente sección */

document.addEventListener('DOMContentLoaded', function() {
  const scrollArrow = document.getElementById('scrollArrow');
  
  if (!scrollArrow) return;

  // Función para desplazarse suavemente a la siguiente sección
  scrollArrow.addEventListener('click', function() {
    const targetSection = document.getElementById('nuevosReportes');
    
    if (targetSection) {
      targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      // Fallback: desplazarse al final de la página
      window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }
  });

  // Ocultar/mostrar el botón según la posición del scroll
  window.addEventListener('scroll', function() {
    const isNearEnd = window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 100;
    scrollArrow.style.opacity = isNearEnd ? '0' : '1';
    scrollArrow.style.pointerEvents = isNearEnd ? 'none' : 'auto';
  });

  scrollArrow.addEventListener('mousedown', () => {
    scrollArrow.style.transform = 'translateX(-50%) scale(0.95)';
  });

  Object.assign(scrollArrow.style, {
    position: 'absolute',
    bottom: '30px',
    left: '50%',
    transform: 'translateX(-50%)'
  });
});