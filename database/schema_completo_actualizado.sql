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
-- admin@mapachi.com      -> 123456
-- agente@mapachi.com     -> 123456
-- cliente@mapachi.com    -> 123456 (inquilina de la propiedad 6)
-- dueno1@mapachi.com     -> 123456 (propietario)
-- dueno2@mapachi.com     -> 123456 (propietario)
-- inquilino2@mapachi.com -> 123456 (inquilino de la propiedad 11)
INSERT INTO users (nombre, email, password, telefono, role_id) VALUES
    ('Admin Sistema', 'admin@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1122334455', 1),
    ('Franco Agente', 'agente@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1133445566', 2),
    ('Flor Cliente', 'cliente@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1144556677', 3),
    ('Marcos Dueño', 'dueno1@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1155667788', 3),
    ('Lucía Dueña', 'dueno2@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1166778899', 3),
    ('Tomás Inquilino', 'inquilino2@mapachi.com', '$2y$10$5kqRCl.kwOgFw6hGxC88q.3oSS1Bkq/BRtfedgTBAifojEyxhWhIu', '1177889900', 3);

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
    propietario_id INT DEFAULT NULL COMMENT 'Dueño de la propiedad',
    inquilino_id INT DEFAULT NULL COMMENT 'Inquilino actual, solo aplica si tipo=alquiler y está ocupada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agente_id) REFERENCES users(id),
    FOREIGN KEY (propietario_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (inquilino_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO properties (titulo, descripcion, tipo, precio, zona, direccion, dormitorios, banos, imagen, estado, agente_id, propietario_id, inquilino_id) VALUES
('Casa con jardín en Belgrano', 'Hermosa casa de 3 habitaciones, 2 baños y jardín privado. Ideal para familia.', 'venta', 120000, 'Belgrano', 'Av. Cabildo 2447', 3, 2, 'casa1.png', 'disponible', 2, 4, NULL),
('Departamento vista al río en Núñez', 'Moderno departamento de 2 habitaciones con vista al río, luminoso.', 'venta', 85000, 'Núñez', 'Av. Libertador 6200', 2, 1, 'depto2.png', 'disponible', 2, 5, NULL),
('Local comercial en Caballito', 'Local amplio en excelente ubicación céntrica, ideal para comercio.', 'alquiler', 1500, 'Caballito', 'Av. Rivadavia 5200', 0, 1, 'local3.png', 'disponible', 2, 4, NULL),
('Departamento en pozo Palermo Soho', 'Emprendimiento nuevo, entrega estimada 2027. Financiación en cuotas.', 'pozo', 95000, 'Palermo', 'Thames 1450', 2, 2, NULL, 'disponible', 2, NULL, NULL),
('PH en Villa del Parque', 'PH de 3 ambientes, patio propio, apto crédito.', 'venta', 78000, 'Villa del Parque', 'Nogoyá 2200', 2, 1, NULL, 'reservada', 2, 5, NULL),
('Departamento en alquiler Flores', 'Departamento 1 ambiente, apto profesional, a estrenar.', 'alquiler', 450, 'Flores', 'Av. Directorio 1800', 1, 1, NULL, 'alquilada', 2, 4, 3),
('Monoambiente en Recoleta', 'Monoambiente luminoso a pasos del Botánico, ideal inversión.', 'alquiler', 380, 'Recoleta', 'Av. Las Heras 3100', 0, 1, NULL, 'disponible', 2, 5, NULL),
('Casa quinta en Devoto', 'Casa amplia con parque y pileta, 4 dormitorios, cochera doble.', 'venta', 210000, 'Devoto', 'Av. Francisco Beiró 4500', 4, 3, NULL, 'disponible', 2, NULL, NULL),
('Departamento a estrenar en Colegiales', 'Departamento de 2 dormitorios a estrenar, amenities completos.', 'venta', 135000, 'Colegiales', 'Av. Álvarez Thomas 1200', 2, 2, NULL, 'disponible', 2, 4, NULL),
('Loft en San Telmo', 'Loft de diseño en edificio histórico reciclado, techos altos.', 'venta', 98000, 'San Telmo', 'Defensa 950', 1, 1, NULL, 'disponible', 2, NULL, NULL),
('Departamento en alquiler en Almagro', 'Departamento de 2 ambientes cerca del subte, muy luminoso.', 'alquiler', 520, 'Almagro', 'Av. Rivadavia 4100', 1, 1, NULL, 'alquilada', 2, 5, 6),
('Casa en Saavedra', 'Casa de 3 dormitorios con parrilla y patio, apta crédito hipotecario.', 'venta', 165000, 'Saavedra', 'Av. Ricardo Balbín 3200', 3, 2, NULL, 'reservada', 2, NULL, NULL),
('Departamento en pozo en Chacarita', 'Pozo con entrega en 18 meses, unidades de 1 y 2 dormitorios.', 'pozo', 72000, 'Chacarita', 'Av. Corrientes 6100', 1, 1, NULL, 'disponible', 2, NULL, NULL),
('PH en alquiler en Boedo', 'PH de 2 ambientes con patio, muy luminoso, apto mascotas.', 'alquiler', 410, 'Boedo', 'Av. San Juan 3400', 1, 1, NULL, 'disponible', 2, 4, NULL),
('Departamento en Barracas', 'Departamento de 3 ambientes cerca de Parque Lezama.', 'venta', 92000, 'Barracas', 'Av. Montes de Oca 800', 2, 1, NULL, 'vendida', 2, 5, NULL),
('Oficina en alquiler en Microcentro', 'Oficina de 60m² en piso alto, ideal para estudio profesional.', 'alquiler', 900, 'Microcentro', 'Av. Corrientes 800', 0, 1, NULL, 'disponible', 2, 5, NULL);


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

-- ---------------------------------------------------------
-- Reclamos: el cliente los levanta sobre una propiedad asociada a él,
-- el agente/admin responde y cambia el estado.
-- ---------------------------------------------------------
CREATE TABLE reclamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    cliente_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    estado ENUM('abierto', 'en_proceso', 'resuelto') NOT NULL DEFAULT 'abierto',
    respuesta TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Comprobantes de pago: el cliente sube el archivo, el agente/admin
-- lo aprueba o rechaza.
-- ---------------------------------------------------------
CREATE TABLE comprobantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    cliente_id INT NOT NULL,
    archivo VARCHAR(255) DEFAULT NULL,
    monto DECIMAL(12,2) DEFAULT NULL,
    estado ENUM('pendiente', 'aprobado', 'rechazado') NOT NULL DEFAULT 'pendiente',
    nota_admin VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Contratos de alquiler: vinculan una propiedad, un inquilino y un dueño,
-- con los términos (monto inicial, duración, frecuencia de ajuste por ICL).
-- ---------------------------------------------------------
CREATE TABLE contratos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    inquilino_id INT NOT NULL,
    propietario_id INT NOT NULL,
    monto_inicial DECIMAL(12,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    duracion_meses INT NOT NULL,
    frecuencia_ajuste_meses INT NOT NULL DEFAULT 3 COMMENT 'Cada cuantos meses se ajusta por ICL (3, 6, 12...)',
    estado ENUM('activo', 'finalizado', 'rescindido') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (inquilino_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (propietario_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Cuotas: una por cada mes del contrato. El monto de los meses dentro del
-- primer periodo queda fijo (monto_inicial); los meses posteriores a cada
-- ajuste quedan en NULL ("a convenir") hasta que se calculen por ICL o
-- se carguen a mano.
-- ---------------------------------------------------------
CREATE TABLE cuotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contrato_id INT NOT NULL,
    numero_cuota INT NOT NULL,
    mes_vencimiento DATE NOT NULL,
    monto DECIMAL(12,2) DEFAULT NULL,
    icl_base DECIMAL(12,4) DEFAULT NULL COMMENT 'Valor del ICL usado como referencia inicial de este periodo',
    icl_actual DECIMAL(12,4) DEFAULT NULL COMMENT 'Valor del ICL al momento del ajuste',
    estado ENUM('a_convenir', 'pendiente', 'pagado', 'vencido') NOT NULL DEFAULT 'pendiente',
    archivo VARCHAR(255) DEFAULT NULL,
    fecha_pago DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Cache simple para no golpear la API del BCRA en cada request:
-- guardamos los valores de ICL ya consultados por fecha.
CREATE TABLE icl_cache (
    fecha DATE PRIMARY KEY,
    valor DECIMAL(12,4) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Configuraciones varias de la app (ej: el id de variable ICL descubierto en el BCRA)
CREATE TABLE app_settings (
    clave VARCHAR(50) PRIMARY KEY,
    valor VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Datos de prueba: reclamos y comprobantes de Flor Cliente (id 3), inquilina
-- de la propiedad 6 (la única asociada a su cuenta ahora).
INSERT INTO reclamos (property_id, cliente_id, titulo, descripcion, estado, respuesta) VALUES
(6, 3, 'Pérdida en el techo', 'Hay una filtración en el techo de la cocina desde la última lluvia.', 'en_proceso', 'Ya coordinamos con un gasista matriculado, pasa el jueves a revisar.'),
(6, 3, 'Problema con la llave del baño', 'La llave del baño principal gotea constantemente.', 'abierto', NULL);

INSERT INTO comprobantes (property_id, cliente_id, archivo, monto, estado, nota_admin) VALUES
(6, 3, NULL, 450.00, 'aprobado', 'Pago de alquiler de julio recibido correctamente.'),
(6, 3, NULL, 450.00, 'pendiente', NULL);


-- Contrato 1: Flor Cliente (inquilina, id 3) alquila la propiedad 6, dueño Marcos (id 4)
INSERT INTO contratos (id, property_id, inquilino_id, propietario_id, monto_inicial, fecha_inicio, duracion_meses, frecuencia_ajuste_meses, estado) VALUES
(1, 6, 3, 4, 450.0, '2026-01-17', 24, 3, 'activo');

INSERT INTO cuotas (contrato_id, numero_cuota, mes_vencimiento, monto, estado, fecha_pago) VALUES
(1, 1, '2026-01-17', 450.0, 'pagado', '2026-01-17'),
(1, 2, '2026-02-17', 450.0, 'pagado', '2026-02-17'),
(1, 3, '2026-03-17', 450.0, 'pagado', '2026-03-17'),
(1, 4, '2026-04-17', 585.0, 'pagado', '2026-04-17'),
(1, 5, '2026-05-17', 585.0, 'pagado', '2026-05-17'),
(1, 6, '2026-06-17', 585.0, 'pagado', '2026-06-17'),
(1, 7, '2026-07-17', NULL, 'a_convenir', NULL),
(1, 8, '2026-08-17', NULL, 'a_convenir', NULL),
(1, 9, '2026-09-17', NULL, 'a_convenir', NULL),
(1, 10, '2026-10-17', NULL, 'a_convenir', NULL),
(1, 11, '2026-11-17', NULL, 'a_convenir', NULL),
(1, 12, '2026-12-17', NULL, 'a_convenir', NULL),
(1, 13, '2027-01-17', NULL, 'a_convenir', NULL),
(1, 14, '2027-02-17', NULL, 'a_convenir', NULL),
(1, 15, '2027-03-17', NULL, 'a_convenir', NULL),
(1, 16, '2027-04-17', NULL, 'a_convenir', NULL),
(1, 17, '2027-05-17', NULL, 'a_convenir', NULL),
(1, 18, '2027-06-17', NULL, 'a_convenir', NULL),
(1, 19, '2027-07-17', NULL, 'a_convenir', NULL),
(1, 20, '2027-08-17', NULL, 'a_convenir', NULL),
(1, 21, '2027-09-17', NULL, 'a_convenir', NULL),
(1, 22, '2027-10-17', NULL, 'a_convenir', NULL),
(1, 23, '2027-11-17', NULL, 'a_convenir', NULL),
(1, 24, '2027-12-17', NULL, 'a_convenir', NULL);

-- Contrato 2: Tomás Inquilino (id 6) alquila la propiedad 11 (Almagro), dueña Lucía (id 5)
INSERT INTO contratos (id, property_id, inquilino_id, propietario_id, monto_inicial, fecha_inicio, duracion_meses, frecuencia_ajuste_meses, estado) VALUES
(2, 11, 6, 5, 520.0, '2026-03-01', 12, 6, 'activo');

INSERT INTO cuotas (contrato_id, numero_cuota, mes_vencimiento, monto, estado, fecha_pago) VALUES
(2, 1, '2026-03-01', 520.0, 'pagado', '2026-03-01'),
(2, 2, '2026-04-01', 520.0, 'pagado', '2026-04-01'),
(2, 3, '2026-05-01', 520.0, 'pagado', '2026-05-01'),
(2, 4, '2026-06-01', 520.0, 'pagado', '2026-06-01'),
(2, 5, '2026-07-01', 520.0, 'pagado', '2026-07-01'),
(2, 6, '2026-08-01', 520.0, 'pendiente', NULL),
(2, 7, '2026-09-01', NULL, 'a_convenir', NULL),
(2, 8, '2026-10-01', NULL, 'a_convenir', NULL),
(2, 9, '2026-11-01', NULL, 'a_convenir', NULL),
(2, 10, '2026-12-01', NULL, 'a_convenir', NULL),
(2, 11, '2027-01-01', NULL, 'a_convenir', NULL),
(2, 12, '2027-02-01', NULL, 'a_convenir', NULL);

-- Propiedades nuevas (segunda tanda), sin imagen todavía — se agregan después.
INSERT INTO properties (titulo, descripcion, tipo, precio, zona, direccion, dormitorios, banos, imagen, estado, agente_id, propietario_id, inquilino_id) VALUES
('Departamento reciclado en Belgrano R', 'Departamento de 3 dormitorios totalmente reciclado, balcón corrido y cochera.', 'venta', 145000, 'Belgrano', 'Virrey del Pino 2200', 3, 2, NULL, 'disponible', 2, NULL, NULL),
('Casa a la calle en Parque Chas', 'Casa de 3 dormitorios en el barrio más tranquilo de la ciudad, patio con parrilla.', 'venta', 135000, 'Parque Chas', 'Berramendi 3300', 3, 2, NULL, 'reservada', 2, NULL, NULL),
('Departamento en alquiler en Villa Urquiza', 'Departamento de 2 dormitorios a metros del subte B, luminoso y con balcón.', 'alquiler', 480, 'Villa Urquiza', 'Av. Triunvirato 4500', 2, 1, NULL, 'disponible', 2, NULL, NULL),
('Local a la calle en Once', 'Local comercial de 80m² sobre avenida, alto tránsito peatonal.', 'alquiler', 1200, 'Once', 'Av. Pueyrredón 400', 0, 1, NULL, 'disponible', 2, NULL, NULL),
('PH en pozo en Villa Crespo', 'PH a estrenar en desarrollo nuevo, entrega estimada 2028.', 'pozo', 88000, 'Villa Crespo', 'Av. Corrientes 4900', 2, 1, NULL, 'disponible', 2, NULL, NULL),
('Departamento de lujo en Belgrano Chico', 'Departamento de 4 dormitorios en torre premium, amenities de categoría.', 'venta', 210000, 'Belgrano', 'Av. del Libertador 3800', 4, 3, NULL, 'disponible', 2, NULL, NULL),
('Casa en Palermo Hollywood', 'Casa de diseño de 4 dormitorios, pileta y jardín. Zona exclusiva.', 'venta', 320000, 'Palermo', 'Fitz Roy 1700', 4, 3, NULL, 'vendida', 2, NULL, NULL),
('Departamento en alquiler en Caballito', 'Departamento de 1 dormitorio a estrenar, cocina integrada.', 'alquiler', 500, 'Caballito', 'Av. Pedro Goyena 900', 1, 1, NULL, 'alquilada', 2, NULL, NULL),
('Departamento en pozo en Núñez', 'Pozo con vista abierta, unidades de 2 y 3 dormitorios, entrega 2027.', 'pozo', 110000, 'Núñez', 'Av. Cabildo 4200', 3, 2, NULL, 'disponible', 2, NULL, NULL);
