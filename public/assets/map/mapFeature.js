document.addEventListener('DOMContentLoaded', function() {
    // Coordenadas iniciales
    const defaultCoords = [18.7357, -70.1627];
    const defaultZoom = 8;

    // Inicializar mapa
    const map = L.map('map').setView(defaultCoords, defaultZoom);

    // Capa base
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Ocultar spinner
    document.getElementById('map-loading').style.display = 'none';

    // ======= ICONOS PERSONALIZADOS POR TIPO =======
    const incidentIcons = {
        'Accidente': L.divIcon({
            className: 'incident-marker accident',
            html: '<i class="fas fa-car-crash"></i>',
            iconSize: [50, 50],
            iconAnchor: [36, 62]
        }),
        'Robo': L.divIcon({
            className: 'incident-marker robbery',
            html: '<i class="fas fa-mask"></i>',
            iconSize: [50, 50],
            iconAnchor: [36, 62]
        }),
        'Pelea': L.divIcon({
            className: 'incident-marker fight',
            html: '<i class="fa-solid fa-hand-fist"></i>',
            iconSize: [50, 50],
            iconAnchor: [36, 62]
        }),
        'Desastre': L.divIcon({
            className: 'incident-marker disaster',
            html: '<i class="fa-solid fa-house-flood-water"></i>',
            iconSize: [50, 50],
            iconAnchor: [36, 62]
        }),
        'Incendio': L.divIcon({
            className: 'incident-marker fire',
            html: '<i class="fa-solid fa-fire"></i>',
            iconSize: [50, 50],
            iconAnchor: [36, 62]
        }),
        'default': L.divIcon({
            className: 'incident-marker default',
            html: '<i class="fa-solid fa-triangle-exclamation"></i>',
            iconSize: [50, 50],
            iconAnchor: [36, 62]
        })
    };

    // ======= DATOS DE EJEMPLO (REEMPLAZAR CON DATOS REALES) =======
    const incidents = [
        {
            id: 1,
            title: "Accidente en Av. Churchill",
            type: "Accidente",
            lat: 18.4865,
            lng: -69.9300,
            description: "Colisión múltiple entre 3 vehículos..."
        },
        {
            id: 2,
            title: "Robo en Calle El Conde",
            type: "Robo",
            lat: 18.4730,
            lng: -69.8840,
            description: "Asalto a mano armada..."
        },
        {
            id: 3,
            title: "Inundación en Haina",
            type: "Desastre",
            lat: 18.4185,
            lng: -70.0310,
            description: "Inundaciones por desborde de río..."
        },
        {
            id: 4,
            title: "Pelea en Av. Churchill",
            type: "Pelea",
            lat: 18.7875,
            lng: -69.3335,
            description: "Altercado entre varios individuos..."
        }
    ];


    // ======= AÑADIR MARCADORES AL MAPA =======
    incidents.forEach(incident => {
        const icon = incidentIcons[incident.type] || incidentIcons['default'];
        
        const marker = L.marker([incident.lat, incident.lng], { icon })
            .addTo(map)
            .bindPopup(`
                <h6>${incident.title}</h6>
                <p><strong>Tipo:</strong> ${incident.type}</p>
                <button class="btn btn-sm btn-primary view-detail" 
                        data-id="${incident.id}">
                    Ver detalles
                </button>
            `);
        
        // Almacenar referencia para usar luego
        marker.incidentData = incident;
    });

    // ======= MANEJAR CLICK EN DETALLES =======
    map.on('popupopen', function(e) {
        const popup = e.popup;
        const button = popup.getElement().querySelector('.view-incidentModal');
        
        if(button) {
            button.addEventListener('click', function() {
                const incidentId = this.getAttribute('data-id');
                // Aquí implementarías la lógica para mostrar el modal
                console.log("Mostrar detalles para incidencia:", incidentId);
                // Ejemplo: abrirModal(incidentId);
            });
        }
    });
});