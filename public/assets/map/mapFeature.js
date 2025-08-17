document.addEventListener('DOMContentLoaded', function () {
    (async function initMap() {
        const defaultCoords = [18.7357, -70.1627];
        const defaultZoom = 8;

        const map = L.map('map').setView(defaultCoords, defaultZoom);
        const markerCluster = L.markerClusterGroup();

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        document.getElementById('map-loading').style.display = 'none';

        const incidentIcons = {
            'Accidente': L.divIcon({ 
                className: 'incident-marker accident', 
                html: '<i class="fas fa-car-crash"></i>', 
                iconSize: [50, 50], 
                iconAnchor: [36, 62] }),

            'Robo': L.divIcon({ 
                className: 'incident-marker robbery', 
                html: '<i class="fas fa-mask"></i>', 
                iconSize: [50, 50], 
                iconAnchor: [36, 62] }),

            'Pelea': L.divIcon({ 
                className: 'incident-marker fight', 
                html: '<i class="fa-solid fa-hand-fist"></i>', 
                iconSize: [50, 50], 
                iconAnchor: [36, 62] }),

            'Desastre': L.divIcon({ 
                className: 'incident-marker disaster', 
                html: '<i class="fa-solid fa-house-flood-water"></i>', 
                iconSize: [50, 50], 
                iconAnchor: [36, 62] }),

            'Incendio': L.divIcon({ 
                className: 'incident-marker fire', 
                html: '<i class="fa-solid fa-fire"></i>', 
                iconSize: [50, 50], 
                iconAnchor: [36, 62] }),

            'default': L.divIcon({ 
                className: 'incident-marker default', 
                html: '<i class="fa-solid fa-triangle-exclamation"></i>', 
                iconSize: [50, 50], 
                iconAnchor: [36, 62] })
        };

        function abrirModal(incident) {
            document.getElementById('incidentTitle').textContent = incident.title || '';
            document.getElementById('incidentType').textContent = incident.type || '';
            document.getElementById('incidentStatus').textContent = incident.status || 'Pendiente';
            document.getElementById('locationInfo').innerHTML = `<i class="fas fa-map-marker-alt me-1"></i> ${incident.location || ''}`;
            document.getElementById('timeInfo').innerHTML = `<i class="fas fa-clock me-1"></i> ${incident.date || ''}`;
            document.getElementById('incidentDescription').textContent = incident.description || '';
            document.getElementById('deathCount').textContent = incident.deaths || 0;
            document.getElementById('injuredCount').textContent = incident.injured || 0;
            document.getElementById('lossAmount').textContent = incident.loss || '$0';
            document.getElementById('coordinatesDisplay').textContent = `Latitud: ${incident.lat} | Longitud: ${incident.lng}`;
            document.getElementById('reportedBy').textContent = incident.reportedBy || 'Desconocido';
            document.getElementById('reporterEmail').textContent = incident.reporterEmail || '-';
            document.getElementById('reportDate').textContent = incident.reportDate || '-';
            document.getElementById('reportStatus').textContent = incident.reportStatus || 'Pendiente';
            new bootstrap.Modal(document.getElementById('incidentModal')).show();
        }

        async function loadIncidencias() {
            const provincia = document.getElementById('filterProvincia').value;
            const tipo = document.getElementById('filterTipo').value;
            const fechaInicio = document.getElementById('filterFechaInicio').value;
            const fechaFin = document.getElementById('filterFechaFin').value;

            let query = `?provincia=${encodeURIComponent(provincia)}&tipo=${encodeURIComponent(tipo)}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
            try {
                const response = await fetch(`/api/incidencias${query}`);
                if (!response.ok) throw new Error("Error al obtener incidencias");

                const incidents = await response.json();

                markerCluster.clearLayers();

                incidents.forEach(incident => {
                    const icon = incidentIcons[incident.type] || incidentIcons['default'];
                    const marker = L.marker([incident.lat, incident.lng], { icon })
                        .bindPopup(`
                            <h6>${incident.title}</h6>
                            <p><strong>Tipo:</strong> ${incident.type}</p>
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

        map.on('popupopen', function (e) {
            const popup = e.popup;
            const button = popup.getElement().querySelector('.view-detail');
            if (button) {
                button.addEventListener('click', function () {
                    const incidentId = this.getAttribute('data-id');
                    const marker = markerCluster.getLayers().find(m => m.incidentData.id == incidentId);
                    if (marker) abrirModal(marker.incidentData);
                });
            }
        });

        document.getElementById('btnFiltrar').addEventListener('click', loadIncidencias);

        await loadIncidencias();
    })();
});