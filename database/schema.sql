-- =========================================================
-- Mapachi Inmobiliaria - Esquema de Base de Datos
-- =========================================================

CREATE DATABASE IF NOT EXISTS mapachi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mapachi;

-- ---------------------------------------------------------
-- Roles del sistema
-- ---------------------------------------------------------
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO roles (id, nombre) VALUES
    (1, 'administrador'),
    (2, 'agente'),
    (3, 'cliente');

-- ---------------------------------------------------------
-- Usuarios
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    role_id INT NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- Passwords de prueba: "123456" (hasheadas con password_hash de PHP - bcrypt)
-- admin@mapachi.com     -> 123456
-- agente@mapachi.com    -> 123456
-- cliente@mapachi.com   -> 123456
INSERT INTO users (nombre, email, password, telefono, role_id) VALUES
    ('Admin Sistema', 'admin@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1122334455', 1),
    ('Franco Agente', 'agente@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1133445566', 2),
    ('Flor Cliente', 'cliente@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1144556677', 3);

-- ---------------------------------------------------------
-- Propiedades (entidad principal del CRUD)
-- ---------------------------------------------------------
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo ENUM('venta', 'alquiler', 'pozo') NOT NULL,
    precio DECIMAL(12,2) NOT NULL,
    zona VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NOT NULL,
    dormitorios INT DEFAULT 0,
    banos INT DEFAULT 0,
    imagen VARCHAR(255) DEFAULT NULL,
    estado ENUM('disponible', 'alquilada', 'vendida', 'reservada') NOT NULL DEFAULT 'disponible',
    agente_id INT NOT NULL,
    propietario_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agente_id) REFERENCES users(id),
    FOREIGN KEY (propietario_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO properties (titulo, descripcion, tipo, precio, zona, direccion, dormitorios, banos, imagen, estado, agente_id, propietario_id) VALUES
('Casa con jardín en Belgrano', 'Hermosa casa de 3 habitaciones, 2 baños y jardín privado. Ideal para familia.', 'venta', 120000, 'Belgrano', 'Av. Cabildo 2447', 3, 2, 'casa1.png', 'disponible', 2, 3),
('Departamento vista al río en Núñez', 'Moderno departamento de 2 habitaciones con vista al río, luminoso.', 'venta', 85000, 'Núñez', 'Av. Libertador 6200', 2, 1, 'depto2.png', 'disponible', 2, NULL),
('Local comercial en Caballito', 'Local amplio en excelente ubicación céntrica, ideal para comercio.', 'alquiler', 1500, 'Caballito', 'Av. Rivadavia 5200', 0, 1, 'local3.png', 'disponible', 2, NULL),
('Departamento en pozo Palermo Soho', 'Emprendimiento nuevo, entrega estimada 2027. Financiación en cuotas.', 'pozo', 95000, 'Palermo', 'Thames 1450', 2, 2, NULL, 'disponible', 2, NULL),
('PH en Villa del Parque', 'PH de 3 ambientes, patio propio, apto crédito.', 'venta', 78000, 'Villa del Parque', 'Nogoyá 2200', 2, 1, NULL, 'reservada', 2, NULL),
('Departamento en alquiler Flores', 'Departamento 1 ambiente, apto profesional, a estrenar.', 'alquiler', 450, 'Flores', 'Av. Directorio 1800', 1, 1, NULL, 'alquilada', 2, 3);

-- ---------------------------------------------------------
-- Mensajes de contacto (formulario público)
-- ---------------------------------------------------------
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    mensaje TEXT NOT NULL,
    leido TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
