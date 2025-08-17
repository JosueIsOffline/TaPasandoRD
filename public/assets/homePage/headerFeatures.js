/**
 * Transición del header cuando el título del hero se desplaza fuera de la vista
 * Implementa una transición donde el título y logo aparecen en el header
 * cuando el hero title ya no es completamente visible
 */

document.addEventListener('DOMContentLoaded', function() {
  console.log('Header transition: Inicializando...');
  
  const header = document.getElementById('siteHeader');
  const heroTitle = document.getElementById('heroTitle');
  
  if (!header || !heroTitle) {
    console.warn('Header transition: Elementos necesarios no encontrados');
    console.log('Header:', header);
    console.log('HeroTitle:', heroTitle);
    return;
  }

  console.log('Header transition: Elementos encontrados correctamente');

  // Estados del header
  const HEADER_STATES = {
    HIDDEN: 'hidden',    // Header transparente, marca oculta
    PEEK: 'peek',        // Header semi-transparente, marca semi-visible
    SHOW: 'show'         // Header visible, marca completamente visible
  };

  let currentState = HEADER_STATES.HIDDEN;
  let ticking = false;

  /* Aplica el estado visual al header */
  function setHeaderState(state) {
    if (currentState === state) return;
    
    console.log('Header transition: Cambiando estado de', currentState, 'a', state);
    
    // Remover todas las clases de estado
    header.classList.remove('site-header--peek', 'site-header--show');
    
    // Aplicar nueva clase según el estado
    switch (state) {
      case HEADER_STATES.PEEK:
        header.classList.add('site-header--peek');
        break;
      case HEADER_STATES.SHOW:
        header.classList.add('site-header--show');
        break;
      case HEADER_STATES.HIDDEN:
      default:
        break;
    }
    
    currentState = state;
  }

  /*Calcula el estado del header basado en la posición del hero title*/
  function calculateHeaderState() {
    const rect = heroTitle.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    
    // Si el título está completamente fuera de la vista
    if (rect.bottom <= 0) {
      return HEADER_STATES.SHOW;
    }
    
    // Si el título está parcialmente visible
    const visibleHeight = Math.min(rect.bottom, viewportHeight) - Math.max(rect.top, 0);
    const totalHeight = rect.height;
    const visibilityRatio = visibleHeight / totalHeight;
    
    if (visibilityRatio < 0.5) {
      return HEADER_STATES.PEEK;
    }
    
    // Si el título está mayormente visible
    return HEADER_STATES.HIDDEN;
  }

  /* Función principal que actualiza el estado del header */
  function updateHeaderState() {
    if (ticking) return;
    
    ticking = true;
    requestAnimationFrame(() => {
      const newState = calculateHeaderState();
      setHeaderState(newState);
      ticking = false;
    });
  }


  function initIntersectionObserver() {
    if (!('IntersectionObserver' in window)) {
      console.log('Header transition: IntersectionObserver no disponible, usando fallback');
      return false;
    }

    console.log('Header transition: Usando IntersectionObserver');

    const observer = new IntersectionObserver((entries) => {
      const entry = entries[0];
      
      if (!entry.isIntersecting) {
        // El título no está intersectando con el viewport
        setHeaderState(HEADER_STATES.SHOW);
      } else {

        const ratio = entry.intersectionRatio;
        
        if (ratio < 0.5) {
          setHeaderState(HEADER_STATES.PEEK);
        } else {
          setHeaderState(HEADER_STATES.HIDDEN);
        }
      }
    }, {
      root: null,
      threshold: [0, 0.25, 0.5, 0.75, 1],
      rootMargin: '0px'
    });

    observer.observe(heroTitle);
    return true;
  }

  /* Inicializa el sistema de transición */
  function init() {
    // Intentar usar IntersectionObserver primero
    const observerAvailable = initIntersectionObserver();

    // Estado inicial
    updateHeaderState();
    
    window.addEventListener('load', updateHeaderState, { once: true });
    
  }

  init();
});