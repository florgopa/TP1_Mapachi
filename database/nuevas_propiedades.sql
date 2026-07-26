-- Ejecutar esto en phpMyAdmin (pestaña SQL, con la base "mapachi" seleccionada)
-- Agrega 10 propiedades nuevas, variadas en zona/precio/dormitorios/tipo,
-- para probar bien los filtros del catálogo.

INSERT INTO properties (titulo, descripcion, tipo, precio, zona, direccion, dormitorios, banos, imagen, estado, agente_id, propietario_id) VALUES
('Monoambiente en Recoleta', 'Monoambiente luminoso a pasos del Botánico, ideal inversión.', 'alquiler', 380, 'Recoleta', 'Av. Las Heras 3100', 0, 1, NULL, 'disponible', 2, NULL),
('Casa quinta en Devoto', 'Casa amplia con parque y pileta, 4 dormitorios, cochera doble.', 'venta', 210000, 'Devoto', 'Av. Francisco Beiró 4500', 4, 3, NULL, 'disponible', 2, NULL),
('Departamento a estrenar en Colegiales', 'Departamento de 2 dormitorios a estrenar, amenities completos.', 'venta', 135000, 'Colegiales', 'Av. Álvarez Thomas 1200', 2, 2, NULL, 'disponible', 2, NULL),
('Loft en San Telmo', 'Loft de diseño en edificio histórico reciclado, techos altos.', 'venta', 98000, 'San Telmo', 'Defensa 950', 1, 1, NULL, 'disponible', 2, NULL),
('Departamento en alquiler en Almagro', 'Departamento de 2 ambientes cerca del subte, muy luminoso.', 'alquiler', 520, 'Almagro', 'Av. Rivadavia 4100', 1, 1, NULL, 'disponible', 2, NULL),
('Casa en Saavedra', 'Casa de 3 dormitorios con parrilla y patio, apta crédito hipotecario.', 'venta', 165000, 'Saavedra', 'Av. Ricardo Balbín 3200', 3, 2, NULL, 'reservada', 2, NULL),
('Departamento en pozo en Chacarita', 'Pozo con entrega en 18 meses, unidades de 1 y 2 dormitorios.', 'pozo', 72000, 'Chacarita', 'Av. Corrientes 6100', 1, 1, NULL, 'disponible', 2, NULL),
('PH en alquiler en Boedo', 'PH de 2 ambientes con patio, muy luminoso, apto mascotas.', 'alquiler', 410, 'Boedo', 'Av. San Juan 3400', 1, 1, NULL, 'disponible', 2, NULL),
('Departamento en Barracas', 'Departamento de 3 ambientes cerca de Parque Lezama.', 'venta', 92000, 'Barracas', 'Av. Montes de Oca 800', 2, 1, NULL, 'vendida', 2, NULL),
('Oficina en alquiler en Microcentro', 'Oficina de 60m² en piso alto, ideal para estudio profesional.', 'alquiler', 900, 'Microcentro', 'Av. Corrientes 800', 0, 1, NULL, 'disponible', 2, NULL);
