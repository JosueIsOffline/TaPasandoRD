const sidebar = document.getElementById("sidebar");
const toggleButton = document.getElementById("sidebar-toggle");
const toggleButtonMenuItem = document.getElementById(
  "sidebar-toggle-menu-item",
);
const mobileHamburgerButton = document.getElementById(
  "mobile-hamburger-button",
);

// Array para almacenar referencias a los event listeners
let subMenuListeners = [];

// Comprobar si es un dispositivo móvil según el ancho de la pantalla
const isMobile = () => window.innerWidth <= 768;

// Función para alternar el estado de la barra lateral
function toggleSidebar() {
  if (isMobile()) {
    sidebar.classList.toggle("open");
    document.body.style.overflow = sidebar.classList.contains("open")
      ? "hidden"
      : "auto";

    if (sidebar.classList.contains("open")) {
      toggleButton.style.display = "flex"; // toggle button para cerrar sidebar
      mobileHamburgerButton.style.display = "none";
    } else {
      toggleButton.style.display = "none"; // Hide main toggle
      mobileHamburgerButton.style.display = "flex";
    }

    toggleButtonMenuItem.style.display = "none";
  } else {
    // Desktop behavior: Esta clase maneja el comportamiento del toggle.
    sidebar.classList.toggle("collapsed");
    mobileHamburgerButton.style.display = "none"; // Ocultar botón hamburguesa en pantallas pequeñas

    // Se manejará automaticamente en que lugar poner el boton dependiendo del estado del sidebar (si está desplegado o contraido)
    if (sidebar.classList.contains("collapsed")) {
      toggleButton.style.display = "none";
      toggleButtonMenuItem.style.display = "block";

      // Si los sub-items se dejan abiertos y se contrae el sidebar, se cerrarán los sub-items.
      document
        .querySelectorAll(".menu-item.has-submenu.expanded")
        .forEach((item) => {
          item.classList.remove("expanded");
        });
    } else {
      toggleButton.style.display = "flex";
      toggleButtonMenuItem.style.display = "none";
    }
  }
}

// Función para cerrar el sidebar (solo para mobile)
function closeSidebar() {
  if (isMobile() && sidebar.classList.contains("open")) {
    sidebar.classList.remove("open");
    document.body.style.overflow = "auto";
    toggleButton.style.display = "none";
    mobileHamburgerButton.style.display = "flex";
  }
}

// Función para manejar la alternancia del submenú
function toggleSubMenu(event) {
  event.preventDefault();
  event.stopPropagation(); // Prevenir propagación del evento

  const menuItem = event.currentTarget.closest(".menu-item");

  if (!menuItem) return;

  if (sidebar.classList.contains("collapsed") && !isMobile()) {
    // Si está contraída, expandir primero el sidebar
    sidebar.classList.remove("collapsed");

    toggleButtonMenuItem.style.display = "none";
    toggleButton.style.display = "flex";

    // Cerrar otros sub-items abiertos
    document.querySelectorAll(".menu-item.expanded").forEach((item) => {
      if (item !== menuItem) {
        item.classList.remove("expanded");
      }
    });

    // Esperar a que termine la animación del sidebar antes de expandir el submenu
    setTimeout(() => {
      menuItem.classList.add("expanded");
    }, 300); // Ajusta este tiempo según la duración de tu animación CSS
  } else {
    // Comportamiento normal: cerrar otros y alternar el actual
    document.querySelectorAll(".menu-item.expanded").forEach((item) => {
      if (item !== menuItem) {
        item.classList.remove("expanded");
      }
    });

    menuItem.classList.toggle("expanded");
  }
}

// Initial load and resize listeners
function handleInitialAndResize() {
  if (isMobile()) {
    sidebar.classList.remove("collapsed");
    sidebar.classList.remove("open");
    document.body.style.overflow = "auto";
    toggleButton.style.display = "none"; // Ocultar toggle en mobile
    mobileHamburgerButton.style.display = "flex"; // Mostrar botón hamburguesa
    toggleButtonMenuItem.style.display = "none"; // ocultar menú items contraidos en mobile
  } else {
    sidebar.classList.remove("open");
    document.body.style.overflow = "auto";
    mobileHamburgerButton.style.display = "none"; // Ocultar botón hamburguesa en desktop

    if (sidebar.classList.contains("collapsed")) {
      toggleButton.style.display = "none";
      toggleButtonMenuItem.style.display = "block";
    } else {
      toggleButton.style.display = "flex";
      toggleButtonMenuItem.style.display = "none";
    }
  }
}

// Función para remover listeners existentes
function removeSubMenuListeners() {
  subMenuListeners.forEach(({ element, listener }) => {
    element.removeEventListener("click", listener);
  });
  subMenuListeners = [];
}

// Función para inicializar los event listeners de los submenús
function initializeSubMenuListeners() {
  // Remover listeners existentes
  removeSubMenuListeners();

  // Agregar nuevos listeners
  document
    .querySelectorAll(".menu-item.has-submenu > .menu-link")
    .forEach((link) => {
      const listener = (event) => toggleSubMenu(event);
      link.addEventListener("click", listener);

      // Almacenar referencia para poder remover después
      subMenuListeners.push({
        element: link,
        listener: listener,
      });
    });
}

// Función para manejar clicks fuera del sidebar
function handleOutsideClick(event) {
  // Solo aplicar en mobile cuando el sidebar está abierto
  if (!isMobile() || !sidebar.classList.contains("open")) {
    return;
  }

  // Verificar si el click fue fuera del sidebar y los botones de control
  const isClickInsideSidebar = sidebar.contains(event.target);
  const isClickOnToggleButton =
    toggleButton && toggleButton.contains(event.target);
  const isClickOnHamburgerButton =
    mobileHamburgerButton && mobileHamburgerButton.contains(event.target);

  // Si el click no fue en el sidebar ni en los botones de control, cerrar el sidebar
  if (
    !isClickInsideSidebar &&
    !isClickOnToggleButton &&
    !isClickOnHamburgerButton
  ) {
    closeSidebar();
  }
}

// Event listeners principales
window.addEventListener("load", () => {
  handleInitialAndResize();
  initializeSubMenuListeners();
});

window.addEventListener("resize", () => {
  handleInitialAndResize();
  // Re-inicializar listeners después del resize
  initializeSubMenuListeners();
});

// Event listeners para los botones de toggle
if (toggleButton) {
  toggleButton.addEventListener("click", toggleSidebar);
}

if (toggleButtonMenuItem) {
  toggleButtonMenuItem.addEventListener("click", toggleSidebar);
}

if (mobileHamburgerButton) {
  mobileHamburgerButton.addEventListener("click", toggleSidebar);
}

// Event listener para clicks fuera del sidebar
document.addEventListener("click", handleOutsideClick);

// También puedes agregar listener para touch events en dispositivos móviles
document.addEventListener("touchstart", handleOutsideClick);
