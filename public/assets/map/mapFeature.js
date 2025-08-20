document.addEventListener("DOMContentLoaded", function () {
  (async function initMap() {
    const defaultCoords = [18.7357, -70.1627];
    const defaultZoom = 8;

    const map = L.map("map").setView(defaultCoords, defaultZoom);
    const markerCluster = L.markerClusterGroup();

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);

    document.getElementById("map-loading").style.display = "none";

    const incidentIcons = {
      1: L.divIcon({
        className: "incident-marker accident",
        html: '<i class="fas fa-car-crash"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      2: L.divIcon({
        className: "incident-marker disaster",
        html: '<i class="fa-solid fa-house-flood-water"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      3: L.divIcon({
        className: "incident-marker fire",
        html: '<i class="fa-solid fa-fire"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      4: L.divIcon({
        className: "incident-marker robbery",
        html: '<i class="fas fa-mask"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      5: L.divIcon({
        className: "incident-marker fight",
        html: '<i class="fa-solid fa-hand-fist"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      default: L.divIcon({
        className: "incident-marker default",
        html: '<i class="fa-solid fa-triangle-exclamation"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),
    };

    function abrirModal(incident) {
        document.getElementById("incidentTitle").textContent =
            incident.title || "";

        document.getElementById("incidentType").textContent =
            getCategoryName(incident.category_id) || "Sin categoría";

        document.getElementById("incidentStatus").textContent =
            incident.status || "Pendiente";

        const provinceNames = {
          1: "Santo Domingo",
          2: "Santiago",
          3: "La Vega",
          4: "Provincia 4",
        };

        const municipioNames = {
          1: "Santo Domingo Este",
          2: "Santo Domingo Oeste",
          3: "Santiago de los Caballeros",
          4: "Jarabacoa",
        };

        document.getElementById("locationInfo").innerHTML =
            `<i class="fas fa-map-marker-alt me-1"></i> <span class="fw-bold">Provincia: </span> ${provinceNames[incident.province_id] || incident.province_id}<br> 
             <i class="fas fa-map-marker-alt me-1"></i> <span class="fw-bold"> Municipio: </span> ${municipioNames[incident.municipality_id] || incident.municipality_id}`;
            

        document.getElementById("timeInfo").innerHTML =
            `<i class="fas fa-clock me-1"></i> ${incident.occurrence_date || ""}`;

        document.getElementById("incidentDescription").textContent =
            incident.description || "";

        document.getElementById("deathCount").textContent =
            incident.deaths ?? 0;

        document.getElementById("injuredCount").textContent =
            incident.injuries ?? 0;

        document.getElementById("lossAmount").textContent =
            `RD$${incident.estimated_loss || "0"}`;

        document.getElementById("coordinatesDisplay").textContent =
            `Latitud: ${incident.latitude} | Longitud: ${incident.longitude}`;

        document.getElementById("reportedBy").textContent =
            `Usuario ${incident.reported_by || "Desconocido"}`;

        document.getElementById("reportDate").textContent =
            incident.created_at || "-";

        document.getElementById("reportStatus").textContent =
            incident.status || "Pendiente";

        document.getElementById("coordinatesDisplay").textContent =
        `Latitud: ${incident.latitude} | Longitud: ${incident.longitude}`;

        const viewOnMapBtn = document.getElementById("viewOnMapBtn");
        if (viewOnMapBtn) {
            viewOnMapBtn.onclick = function () {
                // Centra el mapa en las coordenadas del incidente y hace zoom
                map.setView([incident.latitude, incident.longitude], 16);
                // Opcional: cerrar el modal si quieres
                const modal = bootstrap.Modal.getInstance(document.getElementById('incidentModal'));
                if (modal) modal.hide();
            };
        }

        // Imagen
        if (incident.photo_url) {
            document.getElementById("incidentPhoto").src = incident.photo_url;
        }

       if (incident.social_media_url) {

          const contenedor = document.getElementById("referencias-container");
          contenedor.innerHTML = "";

          const boton = document.createElement("a");
          boton.href = incident.social_media_url;
          boton.target = "_blank";
          boton.className = "btn btn-outline-primary btn-sm me-2";
          boton.textContent = "Ver publicación";

          contenedor.appendChild(boton);
      }


        new bootstrap.Modal(document.getElementById("incidentModal")).show();
        const modal = bootstrap.Modal.getInstance(document.getElementById('incidentModal'));
        modal.hide();
}


    const getCategoryName = (categoryId) => {
      const names = {
        1: "Accidente de Tráfico",
        2: "Inundación",
        3: "Incendio",
        4: "Robo",
      };

      return names[categoryId];
    };

  // Función para obtener provincias y categorías desde el backend por una API
    // // Cargar las provincias y tipos de incidencia
    // async function loadFilters() {
    //   try {
    //   const response = await fetch("http://localhost:8000/api/provinces");
    //   if (!response.ok) throw new Error("Error al obtener provincias");

    //   const provinces = await response.json();
    //   const provinceSelect = document.getElementById("filterProvincia");
    //   provinceSelect.innerHTML = '<option value="">Todas las provincias</option>';
    //   provinces.forEach((province) => {
    //     const option = document.createElement("option");
    //     option.value = province.id;
    //     option.textContent = province.name;
    //     provinceSelect.appendChild(option);
    //   });

    //   const categoryResponse = await fetch("http://localhost:8000/categories");
    //   if (!categoryResponse.ok) throw new Error("Error al obtener categorías");

    //   const categories = await categoryResponse.json();
    //   const tipoSelect = document.getElementById("filterTipo");
    //   tipoSelect.innerHTML = '<option value="">Todos los tipos</option>';
    //   categories.forEach((category) => {
    //     const option = document.createElement("option");
    //     option.value = category.id;
    //     option.textContent = category.name;
    //     tipoSelect.appendChild(option);
    //   });
    //   } catch (error) {
    //   console.error("Error al cargar filtros:", error);
    //   }
    // }
    // await loadFilters();

    async function loadIncidencias() {

      try {
        const provinciaId = document.getElementById("filterProvincia").value;
        const tipoId = document.getElementById("filterTipo").value;


        let url = new URL("http://localhost:8000/api/valid-incident");
        if (provinciaId) url.searchParams.append("province_id", provinciaId);
        if (tipoId) url.searchParams.append("category_id", tipoId);

        // console.log("URL de la API:", url.toString()); // <-- Línea de depuración
        
        const response = await fetch(url);
        if (!response.ok) throw new Error("Error al obtener incidencias");
        const incidents = await response.json();

        markerCluster.clearLayers();

        incidents.forEach((incident) => {
          const icon =
            incidentIcons[incident.category_id] || incidentIcons["default"];
            const marker = L.marker([incident.latitude, incident.longitude], {
            icon,
            }).bindPopup(`
              <div class="incident-popup">
                <h5 class="mb-1 fw-bold">${incident.title || "Incidente"}</h5>
                <p class="mb-1">
                <span class="badge bg-info text-dark">${getCategoryName(incident.category_id) || "Sin categoría"}</span>
                </p>
                <p class="mb-1 text-muted" style="font-size: 0.95em;">
                <i class="fas fa-clock me-1"></i> ${incident.occurrence_date || ""}
                </p>
                <button class="btn btn-sm btn-outline-primary view-detail mt-2" data-id="${incident.id}">
                <i class="fas fa-eye me-1"></i> Ver detalles
                </button>
              </div>
            `);
          marker.incidentData = incident;
          markerCluster.addLayer(marker);
        });

        map.addLayer(markerCluster);

        if (incidents.length > 0) {
          map.fitBounds(markerCluster.getBounds());
        }
      } catch (error) {
        console.error("Error al cargar incidencias:", error);
      }
    }

    map.on("popupopen", function (e) {
      const popup = e.popup;
      const button = popup.getElement().querySelector(".view-detail");
      if (button) {
        button.addEventListener("click", function () {
          const incidentId = this.getAttribute("data-id");
          const marker = markerCluster
            .getLayers()
            .find((m) => m.incidentData.id == incidentId);
          if (marker) abrirModal(marker.incidentData);
        });
      }
    });

    document
      .getElementById("btnFiltrar")
      .addEventListener("click", loadIncidencias);

    await loadIncidencias();
  })();
});

