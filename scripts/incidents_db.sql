-- Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    supplier_auth ENUM('Google', 'Microsoft', 'local') NOT NULL,
    password VARCHAR(255) NULL,
    photo_url VARCHAR(255),
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    role ENUM('Reportero', 'Validador', 'Admin'),
    INDEX idx_email (email),
    INDEX idx_rol (roles),
    INDEX idx_active (active)
);

-- Locations
CREATE TABLE provinces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_province_name (name)
);

CREATE TABLE municipalities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    province_id INT,
    FOREIGN KEY (province_id) REFERENCES provinces(id),
    UNIQUE INDEX idx_municipalities_name (name, province_id)
);

CREATE TABLE neighborhoods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    municipality_id INT,
    FOREIGN KEY (municipality_id) REFERENCES municipalities(id)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- type of incidents
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon_color VARCHAR(10),
    icon VARCHAR(10),
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- incidents
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    occurrence_date DATETIME NOT NULL,
    title VARCHAR(255),
    description TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    deaths INT DEFAULT 0,
    injuries INT DEFAULT 0,
    estimated_loss DECIMAL(12,2),
    social_media_url VARCHAR(100),
    photo_url VARCHAR(100),
    status ENUM('pendiente', 'en revisión', 'validado', 'rechazado')
    validation_date DATETIME,
    rejection_reason TEXT,
    province_id INT,
    municipality_id INT,
    neighborhood_id INT,
    category_id INT,
    reported_by INT,
    validated_by INT,
    FOREIGN KEY (province_id) REFERENCES provinces(id),
    FOREIGN KEY (municipality_id) REFERENCES municipalities(id),
    FOREIGN KEY (neighborhood_id) REFERENCES neighborhoods(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (reported_by) REFERENCES users(id),
    FOREIGN KEY (validated_by) REFERENCES users(id),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_municipality (municipality_id),
    INDEX idx_category (category_id),
    INDEX idx_user (reported_by)
);

-- Relations: Incidents ↔ Validations & Categories
CREATE TABLE incidentValidations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT,
    validator_id INT,
    status ENUM('Aprovado', 'Rechazado'),
    comments TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id),
    FOREIGN KEY (validator_id) REFERENCES incident_validations(id)
);

CREATE TABLE incidentCategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT,
    category_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Comments
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT,
    user_id INT,
    content TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
