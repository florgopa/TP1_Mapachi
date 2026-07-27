-- Ejecutar esto en phpMyAdmin (pestaña SQL, con la base "mapachi" seleccionada)
-- Agrega las tablas de reclamos y comprobantes SIN tocar lo que ya tenías.

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

-- Datos de prueba: reclamos y comprobantes del cliente de ejemplo (Flor Cliente, id 3)
-- sobre sus propiedades asociadas (id 1 y 6). Si tus IDs de propiedad/cliente son
-- distintos, ajustalos antes de correr esto (o simplemente omití estos INSERT).
INSERT INTO reclamos (property_id, cliente_id, titulo, descripcion, estado, respuesta) VALUES
(1, 3, 'Pérdida en el techo', 'Hay una filtración en el techo de la cocina desde la última lluvia.', 'en_proceso', 'Ya coordinamos con un gasista matriculado, pasa el jueves a revisar.'),
(6, 3, 'Problema con la llave del baño', 'La llave del baño principal gotea constantemente.', 'abierto', NULL);

INSERT INTO comprobantes (property_id, cliente_id, archivo, monto, estado, nota_admin) VALUES
(6, 3, NULL, 450.00, 'aprobado', 'Pago de alquiler de julio recibido correctamente.'),
(6, 3, NULL, 450.00, 'pendiente', NULL);
