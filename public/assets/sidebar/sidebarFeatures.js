const sidebar = document.getElementById("sidebar")
const toggleButton = document.getElementById("sidebar-toggle")
const toggleButtonMenuItem = document.getElementById("sidebar-toggle-menu-item")
const mobileHamburgerButton = document.getElementById("mobile-hamburger-button")
const appContainer = document.querySelector(".app-container")

// Comprobar si es un dispositivo móvil según el ancho de la pantalla
const isMobile = () => window.innerWidth <= 768

// Función para alternar el estado de la barra lateral
function toggleSidebar() {
  if (isMobile()) {
    sidebar.classList.toggle("open")
    document.body.style.overflow = sidebar.classList.contains("open") ? "hidden" : "auto"

    if (sidebar.classList.contains("open")) {
      toggleButton.style.display = "flex" // toggle button para cerrar sidebar
      mobileHamburgerButton.style.display = "none"
    } else {
      toggleButton.style.display = "none" // Hide main toggle
      mobileHamburgerButton.style.display = "flex"
    }

    toggleButtonMenuItem.style.display = "none"
  } else {
    // Desktop behavior: Esta clase maneja el comportamiento del toggle.
    sidebar.classList.toggle("collapsed")
    mobileHamburgerButton.style.display = "none" // Ocultar botón hamburguesa en pantallas pequeñas

    // Se manejará automaticamente en que lugar poner el boton dependiendo del estado del sidebar (si está desplegado o contraido)
    if (sidebar.classList.contains("collapsed")) {
      toggleButton.style.display = "none"
      toggleButtonMenuItem.style.display = "block"

      // Si los sub-items se dejan abiertos y se contrae el sidebar, se cerrarán los sub-items.
      document.querySelectorAll(".menu-item.has-submenu.expanded").forEach((item) => {
        item.classList.remove("expanded")
      })
    } else {
      toggleButton.style.display = "flex"
      toggleButtonMenuItem.style.display = "none"
    }
  }
}

// Función para manejar la alternancia del submenú
function toggleSubMenu(event) {
  event.preventDefault()
  const menuItem = event.currentTarget.closest(".menu-item")

  if (menuItem) {
    if (sidebar.classList.contains("collapsed") && !isMobile()) {
      // if está contraida, tenerla siempre abierta como estado principal
      sidebar.classList.remove("collapsed")

      toggleButtonMenuItem.style.display = "none"
      toggleButton.style.display = "flex"

      // cerrar sub-items abiertos
      document.querySelectorAll(".menu-item.expanded").forEach((item) => {
        if (item !== menuItem) {

            item.classList.remove("expanded")
        }
      })


      setTimeout(() => {
        menuItem.classList.toggle("expanded")
      }, 400)
    } else {
        
      document.querySelectorAll(".menu-item.expanded").forEach((item) => {
        if (item !== menuItem) {
          item.classList.remove("expanded")
        }
      })
      menuItem.classList.toggle("expanded")
    }
  }
}

// Initial load and resize listeners
function handleInitialAndResize() {
  if (isMobile()) {
    sidebar.classList.remove("collapsed")
    sidebar.classList.remove("open")
    document.body.style.overflow = "auto"
    toggleButton.style.display = "none" // Ocultar toggle en mobile
    mobileHamburgerButton.style.display = "flex" // Mostrar botón hamburguesa
    toggleButtonMenuItem.style.display = "none" // ocultar menú items contraidos en mobile
  } else {
    sidebar.classList.remove("open")
    document.body.style.overflow = "auto"
    mobileHamburgerButton.style.display = "none" // Ocultar botón hamburguesa en desktop

    if (sidebar.classList.contains("collapsed")) {
      toggleButton.style.display = "none"
      toggleButtonMenuItem.style.display = "block"
    } else {
      toggleButton.style.display = "flex"
      toggleButtonMenuItem.style.display = "none"
    }
  }
}

window.addEventListener("load", handleInitialAndResize)
window.addEventListener("resize", handleInitialAndResize)

toggleButton.addEventListener("click", toggleSidebar)
toggleButtonMenuItem.addEventListener("click", toggleSidebar)
mobileHamburgerButton.addEventListener("click", toggleSidebar)

appContainer.addEventListener("click", (event) => {
  if (
    isMobile() &&
    sidebar.classList.contains("open") &&
    !sidebar.contains(event.target) &&
    !toggleButton.contains(event.target) &&
    !mobileHamburgerButton.contains(event.target)
  ) {
    toggleSidebar()
  }
})

document.querySelectorAll(".menu-item.has-submenu > .menu-link").forEach((link) => {
  link.addEventListener("click", toggleSubMenu)
})
