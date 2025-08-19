// Funciones para el modal de validación de incidentes

// Función para alternar el menú de acciones
function toggleActionsMenu(incidentId) {
  const menu = document.getElementById(`actions-menu-${incidentId}`);
  const button = event.target.closest(".actions-button");
  const allMenus = document.querySelectorAll(".actions-menu");

  allMenus.forEach((m) => {
    if (m !== menu) {
      m.classList.remove("show");
    }
  });

  if (menu.classList.contains("show")) {
    menu.classList.remove("show");
  } else {
    const buttonRect = button.getBoundingClientRect();
    const menuWidth = 160; // Ancho mínimo del menú

    menu.style.left = buttonRect.right - menuWidth + "px";
    menu.style.top = buttonRect.bottom + 8 + "px";

    menu.classList.add("show");
  }
}

document.addEventListener("click", function (event) {
  if (!event.target.closest(".actions-wrapper")) {
    document.querySelectorAll(".actions-menu").forEach((menu) => {
      menu.classList.remove("show");
    });
  }
});

// Función para ver detalles del incidente
async function viewDetails(incidentId) {
  try {
    // Cerrar el menú de acciones
    document
      .getElementById(`actions-menu-${incidentId}`)
      .classList.remove("show");

    // Obtener los datos del incidente desde la tabla
    const row = document.querySelector(`tr[data-incident-id="${incidentId}"]`);
    if (!row) {
      console.error("No se encontró la fila del incidente");
      return;
    }

    // Extraer datos básicos de la fila
    const title = row.querySelector(".title-column").textContent;
    const type = row.querySelector(".type-badge").textContent;
    const location = row.querySelector(".location-column").textContent;
    const date = row.querySelector(".date-column").textContent;
    const reporter = row.querySelector(".reporter-column").textContent;

    // Llenar el modal con los datos básicos
    document.getElementById("modal-title").textContent = title;
    document.getElementById("modal-type").textContent = type;
    document.getElementById("modal-location").textContent = location;
    document.getElementById("modal-date").textContent = date;
    document.getElementById("modal-reporter").textContent = reporter;

    // Obtener datos completos del incidente desde la API
    try {
      const response = await fetch(`/api/incident/${incidentId}`);
      if (response.ok) {
        const incident = await response.json();

        // Llenar estadísticas desde la base de datos
        document.getElementById("modal-deaths").textContent =
          incident.deaths || "0";
        document.getElementById("modal-injured").textContent =
          incident.injuries || "0";
        document.getElementById("modal-losses").textContent =
          incident.estimated_loss
            ? `RD$ ${incident.estimated_loss.toLocaleString()}`
            : "N/A";
        document.getElementById("modal-coordinates").textContent =
          incident.latitude && incident.longitude
            ? `${incident.latitude}, ${incident.longitude}`
            : "N/A";

        // Llenar descripción desde la base de datos
        document.getElementById("modal-description").textContent =
          incident.description || "Sin descripción disponible";

        // Contenedor de comentarios (por ahora, es un contenedor vacío)
        const commentsContainer = document.getElementById("modal-comments");
        commentsContainer.innerHTML = "";
      } else {
        console.error(
          "Error al obtener datos del incidente desde la API, usando datos de la tabla",
        );
        // Si la API falla, mostrar datos por defecto
        showDefaultData();
      }
    } catch (error) {
      console.error(
        "Error al cargar detalles del incidente desde la API, usando datos de la tabla:",
        error,
      );
      // Si hay un error de red, mostrar datos por defecto
      showDefaultData();
    }

    // Mostrar el modal
    const modal = new bootstrap.Modal(
      document.getElementById("incidentDetailsModal"),
    );
    modal.show();
  } catch (error) {
    console.error("Error al cargar detalles del incidente:", error);
  }
}

// Función para mostrar datos por defecto cuando la API no funciona
function showDefaultData() {
  document.getElementById("modal-deaths").textContent = "N/A";
  document.getElementById("modal-injured").textContent = "N/A";
  document.getElementById("modal-losses").textContent = "N/A";
  document.getElementById("modal-coordinates").textContent = "N/A";
  document.getElementById("modal-description").textContent =
    "Sin descripción disponible";

  document.getElementById("modal-comments").innerHTML = "";
}

// Función para aprobar incidente
const approve = async (incidentId) => {
  const data = new FormData();
  data.append("incident_id", incidentId);
  data.append("comments", "Validado");

  try {
    const res = await fetch("http://localhost:8000/api/validator/approve", {
      method: "POST",
      body: data,
    });

    if (res.ok) {
      // Cerrar el menú de acciones
      document
        .getElementById(`actions-menu-${incidentId}`)
        .classList.remove("show");

      // Mostrar cambio de estatus visualmente
      showStatusChange(incidentId, "Aprobado", "success");

      // Recargar la página después de 3 segundos (el bash solo se muestra por 3 segundos)
      setTimeout(() => {
        window.location.reload();
      }, 3000);
    }
  } catch (e) {
    console.error("Something went wrong", e);
  }
};

// Función para rechazar incidente
const reject = async (incidentId) => {
  const data = new FormData();
  data.append("incident_id", incidentId);
  data.append("comments", "Rechazado");

  try {
    const res = await fetch("http://localhost:8000/api/validator/reject", {
      method: "POST",
      body: data,
    });

    if (res.ok) {
      // Cerrar el menú de acciones
      document
        .getElementById(`actions-menu-${incidentId}`)
        .classList.remove("show");

      showStatusChange(incidentId, "Rechazado", "danger");

      setTimeout(() => {
        window.location.reload();
      }, 3000);
    }
  } catch (e) {
    console.error("Something went wrong", e);
  }
};

// Función para mostrar cambio de estatus
function showStatusChange(incidentId, newStatus, statusType) {
  const statusCell = document.querySelector(
    `tr[data-incident-id="${incidentId}"] .status-badge`,
  );

  if (statusCell) {
    statusCell.innerHTML = `
      <i class="fas fa-${statusType === "success" ? "check" : "times"}"></i>
      ${newStatus}
    `;

    // Cambiar clases CSS para el nuevo estatus
    statusCell.className = `status-badge status-${statusType}`;

    // Agregar animación de cambio
    statusCell.style.animation = "statusChange 0.5s ease-in-out";
  }
}

// Función para limpiar búsqueda
function clearSearch() {
  const searchInput = document.querySelector(".search-input");

  if (searchInput) {
    searchInput.value = "";
  }

  // Aplicar filtros
  applyFilters();
}

let currentDateFilter = "15";
let currentCategoryFilter = "all";

// Variables globales para paginación
let currentPage = 1;
const itemsPerPage = 4;
let allVisibleRows = [];

// Función para aplicar filtros
function applyFilters() {
  const tableRows = document.querySelectorAll("tbody tr");
  const dataTable = document.getElementById("data-table");
  const searchEmptyState = document.getElementById("search-empty-state");
  const paginationContainer = document.getElementById("pagination-container");

  // Resetear a la primera página cuando se aplican filtros
  currentPage = 1;

  // Filtrar filas visibles
  allVisibleRows = [];
  tableRows.forEach((row) => {
    let showRow = true;

    // Filtro por fecha
    if (currentDateFilter !== "all") {
      const dateCell = row.querySelector(".date-column div");
      if (dateCell) {
        const incidentDate = new Date(
          dateCell.textContent.split("/").reverse().join("-"),
        );
        const daysDiff = Math.floor(
          (Date.now() - incidentDate.getTime()) / (1000 * 60 * 60 * 24),
        );

        if (daysDiff > parseInt(currentDateFilter)) {
          showRow = false;
        }
      }
    }

    // Filtro por categoría
    if (currentCategoryFilter !== "all") {
      const categoryCell = row.querySelector(".type-badge");
      if (categoryCell) {
        const categoryText = categoryCell.textContent.toLowerCase();
        if (!categoryText.includes(currentCategoryFilter)) {
          showRow = false;
        }
      }
    }

    if (showRow) {
      allVisibleRows.push(row);
    }
  });

  // Mostrar/ocultar tabla y estado vacío
  if (allVisibleRows.length === 0) {
    if (dataTable) dataTable.style.display = "none";
    if (searchEmptyState) searchEmptyState.style.display = "block";
    if (paginationContainer) paginationContainer.style.display = "none";
  } else {
    if (dataTable) dataTable.style.display = "block";
    if (searchEmptyState) searchEmptyState.style.display = "none";

    // Mostrar paginación si hay más de 4 elementos
    if (allVisibleRows.length > itemsPerPage) {
      if (paginationContainer) paginationContainer.style.display = "flex";
      renderPagination();
    } else {
      if (paginationContainer) paginationContainer.style.display = "none";
    }

    // Aplicar paginación
    applyPagination();
  }
}

// Función para actualizar filtro de fecha
function updateDateFilter(value, text) {
  currentDateFilter = value;
  document.getElementById("date-filter-btn").textContent = text;
  document.getElementById("date-filter-menu").classList.remove("show");
  applyFilters();
}

// Función para actualizar filtro de categoría
function updateCategoryFilter(value, text) {
  currentCategoryFilter = value;
  document.getElementById("category-filter-btn").textContent = text;
  document.getElementById("category-filter-menu").classList.remove("show");
  applyFilters();
}

// Función para aplicar paginación
function applyPagination() {
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;

  // Ocultar todas las filas
  allVisibleRows.forEach((row) => {
    row.style.display = "none";
  });

  // Mostrar solo las filas de la página actual
  for (let i = startIndex; i < endIndex && i < allVisibleRows.length; i++) {
    allVisibleRows[i].style.display = "";
  }

  // Actualizar texto de paginación
  updatePaginationText();
}

// Función para renderizar controles de paginación
function renderPagination() {
  const totalPages = Math.ceil(allVisibleRows.length / itemsPerPage);
  const pageNumbersContainer = document.getElementById("page-numbers");
  const prevBtn = document.getElementById("prev-btn");
  const nextBtn = document.getElementById("next-btn");

  pageNumbersContainer.innerHTML = "";

  // Generar números de página
  for (let i = 1; i <= totalPages; i++) {
    const pageNumber = document.createElement("div");
    pageNumber.className = "page-number";
    pageNumber.textContent = i;

    if (i === currentPage) {
      pageNumber.classList.add("active");
    }

    pageNumber.addEventListener("click", () => {
      goToPage(i);
    });

    pageNumbersContainer.appendChild(pageNumber);
  }

  // Actualizar estado de botones
  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage === totalPages;
}

// Función para ir a una página específica
function goToPage(page) {
  currentPage = page;
  applyPagination();
  renderPagination();
}

// Función para ir a la página anterior
function previousPage() {
  if (currentPage > 1) {
    currentPage--;
    applyPagination();
    renderPagination();
  }
}

// Función para ir a la página siguiente
function nextPage() {
  const totalPages = Math.ceil(allVisibleRows.length / itemsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    applyPagination();
    renderPagination();
  }
}

// Función para actualizar texto de paginación
function updatePaginationText() {
  const startIndex = (currentPage - 1) * itemsPerPage + 1;
  const endIndex = Math.min(currentPage * itemsPerPage, allVisibleRows.length);
  const totalItems = allVisibleRows.length;

  const paginationText = document.getElementById("pagination-text");
  paginationText.textContent = `Mostrando ${startIndex}-${endIndex} de ${totalItems} reportes`;
}

// Función para inicializar paginación por defecto
function initializePagination() {
  const tableRows = document.querySelectorAll("tbody tr");
  const paginationContainer = document.getElementById("pagination-container");

  // Inicializar con todas las filas visibles
  allVisibleRows = Array.from(tableRows);

  // Mostrar paginación si hay más de 4 elementos
  if (allVisibleRows.length > itemsPerPage) {
    if (paginationContainer) paginationContainer.style.display = "flex";
    renderPagination();
  } else {
    if (paginationContainer) paginationContainer.style.display = "none";
  }

  // Aplicar paginación inicial
  applyPagination();
}

document.addEventListener("DOMContentLoaded", function () {
  // Funcionalidad de búsqueda
  const searchInput = document.querySelector(".search-input");
  if (searchInput) {
    searchInput.addEventListener("input", function (e) {
      const searchTerm = e.target.value.toLowerCase();
      const tableRows = document.querySelectorAll("tbody tr");
      const dataTable = document.getElementById("data-table");
      const searchEmptyState = document.getElementById("search-empty-state");

      let visibleRows = 0;

      // Resetear a la primera página cuando se busca
      currentPage = 1;

      // Filtrar filas visibles
      allVisibleRows = [];
      tableRows.forEach((row) => {
        let showRow = true;

        // Aplicar filtros de fecha y categoría
        if (currentDateFilter !== "all") {
          const dateCell = row.querySelector(".date-column div");
          if (dateCell) {
            const incidentDate = new Date(
              dateCell.textContent.split("/").reverse().join("-"),
            );
            const daysDiff = Math.floor(
              (Date.now() - incidentDate.getTime()) / (1000 * 60 * 60 * 24),
            );

            if (daysDiff > parseInt(currentDateFilter)) {
              showRow = false;
            }
          }
        }

        if (currentCategoryFilter !== "all") {
          const categoryCell = row.querySelector(".type-badge");
          if (categoryCell) {
            const categoryText = categoryCell.textContent.toLowerCase();
            if (!categoryText.includes(currentCategoryFilter)) {
              showRow = false;
            }
          }
        }

        // Aplicar búsqueda de texto
        if (showRow && searchTerm !== "") {
          const text = row.textContent.toLowerCase();
          if (!text.includes(searchTerm)) {
            showRow = false;
          }
        }

        if (showRow) {
          allVisibleRows.push(row);
        }
      });

      // Aplicar paginación
      if (allVisibleRows.length > itemsPerPage) {
        document.getElementById("pagination-container").style.display = "flex";
        renderPagination();
      } else {
        document.getElementById("pagination-container").style.display = "none";
      }

      applyPagination();

      if (allVisibleRows.length === 0 && searchTerm !== "") {
        if (dataTable) dataTable.style.display = "none";
        if (searchEmptyState) searchEmptyState.style.display = "block";
        if (document.getElementById("pagination-container")) {
          document.getElementById("pagination-container").style.display =
            "none";
        }
      } else {
        if (dataTable) dataTable.style.display = "block";
        if (searchEmptyState) searchEmptyState.style.display = "none";
      }
    });
  }

  // Funcionalidad de filtros dropdown
  const dateFilterBtn = document.getElementById("date-filter-btn");
  const dateFilterMenu = document.getElementById("date-filter-menu");
  const categoryFilterBtn = document.getElementById("category-filter-btn");
  const categoryFilterMenu = document.getElementById("category-filter-menu");

  // Toggle filtro de fecha
  dateFilterBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    dateFilterMenu.classList.toggle("show");
    categoryFilterMenu.classList.remove("show");
  });

  // Toggle filtro de categoría
  categoryFilterBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    categoryFilterMenu.classList.toggle("show");
    dateFilterMenu.classList.remove("show");
  });

  document
    .querySelectorAll("#date-filter-menu .filter-option")
    .forEach((option) => {
      option.addEventListener("click", function () {
        const value = this.getAttribute("data-value");
        const text = this.textContent;
        updateDateFilter(value, text);
      });
    });

  document
    .querySelectorAll("#category-filter-menu .filter-option")
    .forEach((option) => {
      option.addEventListener("click", function () {
        const value = this.getAttribute("data-value");
        const text = this.textContent;
        updateCategoryFilter(value, text);
      });
    });

  // Cerrar dropdowns al hacer clic fuera
  document.addEventListener("click", function (e) {
    if (!e.target.closest(".filter-dropdown")) {
      dateFilterMenu.classList.remove("show");
      categoryFilterMenu.classList.remove("show");
    }
  });

  initializePagination();
});
