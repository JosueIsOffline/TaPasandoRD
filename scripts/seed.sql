--
-- Seed file for the database schema
--

-- --------------------------------------------------------
-- Populate the 'roles' table
-- --------------------------------------------------------
INSERT INTO roles (name) VALUES
('reportero'),
('validador'),
('admin');

-- --------------------------------------------------------
-- Populate the 'users' table
-- --------------------------------------------------------
-- password for all users is 'password123' (hashed)
INSERT INTO users (name, email, password, photo_url, supplier_auth, role_id) VALUES
('Juan Pérez', 'juan.perez@example.com', '$2y$10$eW5z5wMZVcY7gOVYmF/s..eRXLWheRwr6tELhDWkNoHquVKmEhnfG', 'https://placehold.co/100x100/A3E4D7/000000?text=JP', 'local', 1),
('Ana García', 'ana.garcia@example.com', '$2y$10$eW5z5wMZVcY7gOVYmF/s..eRXLWheRwr6tELhDWkNoHquVKmEhnfG', 'https://placehold.co/100x100/D7BDE2/000000?text=AG', 'local', 2),
('Carlos Valdés', 'carlos.valdes@example.com', '$2y$10$eW5z5wMZVcY7gOVYmF/s..eRXLWheRwr6tELhDWkNoHquVKmEhnfG', 'https://placehold.co/100x100/FAD7A0/000000?text=CV', 'local', 3),
('Sofía Ramos', 'sofia.ramos@example.com', '$2y$10$eW5z5wMZVcY7gOVYmF/s..eRXLWheRwr6tELhDWkNoHquVKmEhnfG', 'https://placehold.co/100x100/A9CCE3/000000?text=SR', 'local', 1),
('Pedro Sánchez', 'pedro.sanchez@example.com', '$2y$10$eW5z5wMZVcY7gOVYmF/s..eRXLWheRwr6tELhDWkNoHquVKmEhnfG', 'https://placehold.co/100x100/E8DAEF/000000?text=PS', 'local', 2);

-- --------------------------------------------------------
-- Populate the 'provinces' table
-- --------------------------------------------------------
INSERT INTO provinces (name, code) VALUES
('Santo Domingo', 'SDO'),
('Santiago', 'STGO'),
('La Vega', 'LV');

-- --------------------------------------------------------
-- Populate the 'municipalities' table
-- --------------------------------------------------------
INSERT INTO municipalities (name, code, province_id) VALUES
('Santo Domingo Este', 'SDE', 1),
('Santo Domingo Oeste', 'SDO', 1),
('Santiago de los Caballeros', 'STGC', 2),
('Jarabacoa', 'JAB', 3);

-- --------------------------------------------------------
-- Populate the 'neighborhoods' table
-- --------------------------------------------------------
INSERT INTO neighborhoods (name, municipality_id) VALUES
('Villa Duarte', 1),
('Los Alcarrizos', 2),
('Barrio Obrero', 3),
('Manabao', 4);

-- --------------------------------------------------------
-- Populate the 'categories' table
-- --------------------------------------------------------
INSERT INTO categories (name, icon_color, icon) VALUES
('Accidente de Tráfico', '#FF5733', 'car'),
('Inundación', '#337AFF', 'water'),
('Incendio', '#FFC300', 'fire'),
('Robo', '#C70039', 'robbery');

-- --------------------------------------------------------
-- Populate the 'incidents' table
-- --------------------------------------------------------
INSERT INTO incidents (occurrence_date, title, description, latitude, longitude, deaths, injuries, estimated_loss, social_media_url, photo_url, status, validation_date, rejection_reason, province_id, municipality_id, neighborhood_id, category_id, reported_by, validated_by) VALUES
('2025-07-28 10:30:00', 'Choque de dos vehículos en la avenida principal', 'Un accidente automovilístico ha causado congestión. No hay heridos graves.', 18.4900, -69.8900, 0, 2, 5000.00, 'http://twitter.com/post123', 'https://placehold.co/100x100/F0F3F4/000000?text=Foto1', 'validado', '2025-07-28 11:00:00', NULL, 1, 1, 1, 1, 1, 2),
('2025-07-29 15:45:00', 'Inundación repentina en la calle 23', 'Fuertes lluvias causaron que las calles se inunden, afectando varios hogares.', 19.4660, -70.7000, 0, 0, 15000.00, 'http://facebook.com/event456', 'https://placehold.co/100x100/F0F3F4/000000?text=Foto2', 'en revisión', NULL, NULL, 2, 3, 3, 2, 4, NULL),
('2025-07-30 08:00:00', 'Incendio en un edificio de apartamentos', 'Un cortocircuito provocó un incendio en el tercer piso del edificio. Los bomberos están en la escena.', 18.5000, -69.9000, 1, 3, 50000.00, NULL, 'https://placehold.co/100x100/F0F3F4/000000?text=Foto3', 'pendiente', NULL, NULL, 1, 2, 2, 3, 1, NULL),
('2025-07-31 22:15:00', 'Reporte de robo en una tienda de conveniencia', 'Dos individuos armados robaron la caja registradora de una tienda. La policía ya fue notificada.', 18.5050, -69.9500, 0, 0, 2500.00, NULL, 'https://placehold.co/100x100/F0F3F4/000000?text=Foto4', 'rechazado', '2025-08-01 09:00:00', 'Información insuficiente para validar.', 1, 1, 1, 4, 4, 5);

-- --------------------------------------------------------
-- Populate the 'incidentValidations' table
-- --------------------------------------------------------
INSERT INTO incidentValidations (incident_id, validator_id, status, comments) VALUES
(1, 2, 'Aprovado', 'Reporte detallado y fotos adjuntas. Verificado.'),
(4, 5, 'Rechazado', 'No se encontró evidencia adicional. Falta de información.');

-- --------------------------------------------------------
-- Populate the 'incidentCategories' table
-- --------------------------------------------------------
-- This table is for many-to-many relationships, though the current schema
-- seems to have a single category per incident.
-- Adding a second category to an existing incident to demonstrate the table's purpose.
INSERT INTO incidentCategories (incident_id, category_id) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------
-- Populate the 'comments' table
-- --------------------------------------------------------
INSERT INTO comments (incident_id, user_id, content) VALUES
(1, 4, 'Yo pasé por ahí y vi el accidente. Se formó un gran tapón.'),
(2, 2, 'Necesitamos más detalles y fotos para validar este incidente.'),
(3, 1, 'Esperemos que los afectados estén bien.');
