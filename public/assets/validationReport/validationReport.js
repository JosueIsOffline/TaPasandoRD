// Funciones para el modal de validación de incidentes

// Función para alternar el menú de acciones
function toggleActionsMenu(incidentId) {
  const menu = document.getElementById(`actions-menu-${incidentId}`);
  const button = event.target.closest('.actions-button');
  const allMenus = document.querySelectorAll('.actions-menu');
  
  allMenus.forEach(m => {
    if (m !== menu) {
      m.classList.remove('show');
    }
  });
  
  if (menu.classList.contains('show')) {
    menu.classList.remove('show');
  } else {
    const buttonRect = button.getBoundingClientRect();
    const menuWidth = 160; // Ancho mínimo del menú
    
    menu.style.left = (buttonRect.right - menuWidth) + 'px';
    menu.style.top = (buttonRect.bottom + 8) + 'px';
    
    menu.classList.add('show');
  }
}

document.addEventListener('click', function(event) {
  if (!event.target.closest('.actions-wrapper')) {
    document.querySelectorAll('.actions-menu').forEach(menu => {
      menu.classList.remove('show');
    });
  }
});

// Función para ver detalles del incidente
async function viewDetails(incidentId) {
  try {
    // Cerrar el menú de acciones
    document.getElementById(`actions-menu-${incidentId}`).classList.remove('show');
    
    // Obtener los datos del incidente desde la tabla
    const row = document.querySelector(`tr[data-incident-id="${incidentId}"]`);
    if (!row) {
      console.error('No se encontró la fila del incidente');
      return;
    }
    
    // Extraer datos básicos de la fila
    const title = row.querySelector('.title-column').textContent;
    const type = row.querySelector('.type-badge').textContent;
    const location = row.querySelector('.location-column').textContent;
    const date = row.querySelector('.date-column').textContent;
    const reporter = row.querySelector('.reporter-column').textContent;
    
    // Llenar el modal con los datos básicos
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-type').textContent = type;
    document.getElementById('modal-location').textContent = location;
    document.getElementById('modal-date').textContent = date;
    document.getElementById('modal-reporter').textContent = reporter;
    
    // Obtener datos completos del incidente desde la API
    try {
      const response = await fetch(`/api/incident/${incidentId}`);
      if (response.ok) {
        const incident = await response.json();
        
        // Llenar estadísticas desde la base de datos
        document.getElementById('modal-deaths').textContent = incident.deaths || '0';
        document.getElementById('modal-injured').textContent = incident.injuries || '0';
        document.getElementById('modal-losses').textContent = incident.estimated_loss ? `RD$ ${incident.estimated_loss.toLocaleString()}` : 'N/A';
        document.getElementById('modal-coordinates').textContent = 
          incident.latitude && incident.longitude ? 
          `${incident.latitude}, ${incident.longitude}` : 'N/A';
        
        // Llenar descripción desde la base de datos
        document.getElementById('modal-description').textContent = 
          incident.description || 'Sin descripción disponible';
        
        // Contenedor de comentarios (por ahora, es un contenedor vacío)
        const commentsContainer = document.getElementById('modal-comments');
        commentsContainer.innerHTML = '';
        
      } else {
        console.error('Error al obtener datos del incidente desde la API, usando datos de la tabla');
        // Si la API falla, mostrar datos por defecto
        showDefaultData();
      }
    } catch (error) {
      console.error('Error al cargar detalles del incidente desde la API, usando datos de la tabla:', error);
      // Si hay un error de red, mostrar datos por defecto
      showDefaultData();
    }
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('incidentDetailsModal'));
    modal.show();
    
  } catch (error) {
    console.error('Error al cargar detalles del incidente:', error);
  }
}

// Función para mostrar datos por defecto cuando la API no funciona
function showDefaultData() {
  document.getElementById('modal-deaths').textContent = 'N/A';
  document.getElementById('modal-injured').textContent = 'N/A';
  document.getElementById('modal-losses').textContent = 'N/A';
  document.getElementById('modal-coordinates').textContent = 'N/A';
  document.getElementById('modal-description').textContent = 'Sin descripción disponible';
  
  document.getElementById('modal-comments').innerHTML = '';
}

// Función para aprobar incidente
const approve = async (incidentId) => {
  const data = new FormData()
  data.append("incident_id", incidentId)
  data.append("comments", "Validado")
 
  try {
    const res = await fetch("http://localhost:3300/api/validator/approve", {
      method: "POST",
      body: data 
    })
    
    if (res.ok) {
      // Cerrar el menú de acciones
      document.getElementById(`actions-menu-${incidentId}`).classList.remove('show');
      // Recargar la página para mostrar los cambios
      window.location.reload();
    }
  } catch(e) {
   console.error("Something went wrong",e)
  }
}

// Función para rechazar incidente
const reject = async (incidentId) => {
  const data = new FormData()
  data.append("incident_id", incidentId)
  data.append("comments", "Rechazado")
 
  try {
    const res = await fetch("http://localhost:3300/api/validator/reject", {
      method: "POST",
      body: data 
    })
    
    if (res.ok) {
      // Cerrar el menú de acciones
      document.getElementById(`actions-menu-${incidentId}`).classList.remove('show');
      // Recargar la página para mostrar los cambios
      window.location.reload();
    }
  } catch(e) {
   console.error("Something went wrong",e) 
  }
}

// Función para limpiar búsqueda
function clearSearch() {
  const searchInput = document.querySelector('.search-input');
  const dataTable = document.getElementById('data-table');
  const searchEmptyState = document.getElementById('search-empty-state');
  const tableRows = document.querySelectorAll('tbody tr');
  
  if (searchInput) {
    searchInput.value = '';
  }
  
  // Mostrar todas las filas
  tableRows.forEach(row => {
    row.style.display = '';
  });
  
  // Ocultar estado vacío y mostrar tabla
  if (dataTable) dataTable.style.display = 'block';
  if (searchEmptyState) searchEmptyState.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
  // Funcionalidad de búsqueda
  const searchInput = document.querySelector('.search-input');
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const tableRows = document.querySelectorAll('tbody tr');
      const dataTable = document.getElementById('data-table');
      const searchEmptyState = document.getElementById('search-empty-state');
      
      let visibleRows = 0;
      
      tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
          row.style.display = '';
          visibleRows++;
        } else {
          row.style.display = 'none';
        }
      });
      
      if (visibleRows === 0 && searchTerm !== '') {
        if (dataTable) dataTable.style.display = 'none';
        if (searchEmptyState) searchEmptyState.style.display = 'block';
      } else {
        if (dataTable) dataTable.style.display = 'block';
        if (searchEmptyState) searchEmptyState.style.display = 'none';
      }
    });
  }
});
