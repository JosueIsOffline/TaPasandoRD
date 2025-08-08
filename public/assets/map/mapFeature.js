document.addEventListener('DOMContentLoaded', function() {
    // Coordenadas iniciales (República Dominicana)
    const defaultCoords = [18.7357, -70.1627];
    const defaultZoom = 8;

    // Inicializar mapa
    const map = L.map('map').setView(defaultCoords, defaultZoom);

    // Añadir capa base (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Ocultar spinner cuando el mapa esté listo
    document.getElementById('map-loading').style.display = 'none';

    // Ejemplo: Añadir un marcador
    const marker = L.marker([18.4861, -69.9312]).addTo(map)
        .bindPopup('Santo Domingo Este<br>Accidente de tránsito');
});