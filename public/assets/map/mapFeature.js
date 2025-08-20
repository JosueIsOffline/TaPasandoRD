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
        className: "incident-marker robbery",
        html: '<i class="fas fa-mask"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      3: L.divIcon({
        className: "incident-marker fight",
        html: '<i class="fa-solid fa-hand-fist"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      4: L.divIcon({
        className: "incident-marker disaster",
        html: '<i class="fa-solid fa-house-flood-water"></i>',
        iconSize: [50, 50],
        iconAnchor: [36, 62],
      }),

      5: L.divIcon({
        className: "incident-marker fire",
        html: '<i class="fa-solid fa-fire"></i>',
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

    async function abrirModal(incident) {
      document.getElementById("incidentTitle").textContent =
        incident.title || "";

      document.getElementById("incidentType").textContent =
        getCategoryName(incident.category_id) || "Sin categoría";

      document.getElementById("incidentStatus").textContent =
        incident.status || "Pendiente";

      document.getElementById("locationInfo").innerHTML =
        `<i class="fas fa-map-marker-alt me-1"></i> Provincia ID: ${incident.province_id}, Municipio ID: ${incident.municipality_id}`;

      document.getElementById("timeInfo").innerHTML =
        `<i class="fas fa-clock me-1"></i> ${incident.occurrence_date || ""}`;

      document.getElementById("incidentDescription").textContent =
        incident.description || "";

      document.getElementById("deathCount").textContent = incident.deaths ?? 0;

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

      await loadComments(incident.id);
      document
        .getElementById("postComment")
        .addEventListener("click", async () => {
          const content = document.getElementById("commentText");
          const data = new FormData();

          data.append("incident_id", incident.id);
          data.append("content", content.value);

          console.log(data.get("content"));
          const res = await fetch("/api/comment", {
            method: "POST",
            body: data,
          });

          await loadComments(incident.id);
          content.value = "";
        });

      new bootstrap.Modal(document.getElementById("incidentModal")).show();
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("incidentModal"),
      );
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

    //   const categoryResponse = await fetch("http://localhost:8000/api/categories");
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
        const fechaInicio = document.getElementById("filterFechaInicio").value;
        const fechaFin = document.getElementById("filterFechaFin").value;

        let url = new URL("http://localhost:8000/api/valid-incident");
        const response = await fetch(url);
        if (!response.ok) throw new Error("Error al obtener incidencias");
        if (provinciaId) url.searchParams.append("province_id", provinciaId);
        if (tipoId) url.searchParams.append("category_id", tipoId);
        if (fechaInicio) url.searchParams.append("start_date", fechaInicio);
        if (fechaFin) url.searchParams.append("end_date", fechaFin);

        const incidents = await response.json();

        markerCluster.clearLayers();

        incidents.forEach((incident) => {
          const icon =
            incidentIcons[incident.category_id] || incidentIcons["default"];
          const marker = L.marker([incident.latitude, incident.longitude], {
            icon,
          }).bindPopup(`
                            <h6>${incident.title}</h6>
                            <p><strong>Tipo:</strong> ${getCategoryName(incident.category_id)}</p>
                            <button class="btn btn-sm btn-primary view-detail" data-id="${incident.id}">Ver detalles</button>
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

function timeAgo(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const diff = (now - date) / 1000;

  if (diff < 60) return `Hace ${Math.floor(diff)} segundos`;
  if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
  if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
  if (diff < 2592000) return `Hace ${Math.floor(diff / 86400)} días`;

  return date.toLocaleDateString();
}

async function loadComments(id) {
  const commentsCont = document.getElementById("comments-container");

  try {
    const res = await fetch(`/api/comment/${id}`);
    const comments = await res.json();

    commentsCont.innerHTML = comments
      .map(
        (comment) =>
          `
      <div class="comment mb-3 p-3 bg-light rounded">
          <div class="d-flex justify-content-between align-items-start mb-2">
             <strong>${comment.user_name}</strong>
             <small class="text-muted">${timeAgo(comment.created_at)}</small>
          </div>
          <p class="mb-0">${comment.content}</p>
      </div>
`,
      )
      .join("");
  } catch (error) {
    console.error("Something went wrong loading comments", error);
  }
}
