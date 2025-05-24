-- Crear base de datos
CREATE DATABASE IF NOT EXISTS taller_mecanico;
USE taller_mecanico;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    rol ENUM('admin', 'editor') NOT NULL DEFAULT 'editor',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL
);

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion_corta VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    duracion INT NOT NULL COMMENT 'Duración en minutos',
    imagen VARCHAR(255) NOT NULL,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    orden INT NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion_corta VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255) NOT NULL,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    categoria VARCHAR(50) NOT NULL,
    marca VARCHAR(50) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de promociones
CREATE TABLE IF NOT EXISTS promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    descuento DECIMAL(5, 2) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    codigo VARCHAR(20) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de citas
CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100) NOT NULL,
    telefono_cliente VARCHAR(20) NOT NULL,
    servicio_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    vehiculo_marca VARCHAR(50) NOT NULL,
    vehiculo_modelo VARCHAR(50) NOT NULL,
    vehiculo_anio INT NOT NULL,
    comentarios TEXT NULL,
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE RESTRICT
);

-- Tabla de configuración del sitio
CREATE TABLE IF NOT EXISTS configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion VARCHAR(255) NULL,
    fecha_actualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar configuración inicial del sitio
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('nombre_sitio', 'Taller Mecánico', 'Nombre del sitio web'),
('descripcion_corta', 'Especialistas en cambio de aceite y venta de llantas', 'Descripción corta del sitio'),
('telefono', '123-456-7890', 'Teléfono de contacto'),
('email', 'contacto@tallermecanico.com', 'Email de contacto'),
('direccion', 'Av. Principal #123, Ciudad', 'Dirección física'),
('horario', 'Lunes a Viernes: 8:00 AM - 6:00 PM, Sábados: 8:00 AM - 2:00 PM', 'Horario de atención'),
('logo', 'img/logo.png', 'Ruta del logo'),
('facebook', 'https://facebook.com/tallermecanico', 'URL de Facebook'),
('instagram', 'https://instagram.com/tallermecanico', 'URL de Instagram'),
('twitter', 'https://twitter.com/tallermecanico', 'URL de Twitter'),
('email_from', 'noreply@tallermecanico.com', 'Email para envío de correos');

-- Insertar servicios de ejemplo
INSERT INTO servicios (nombre, slug, descripcion_corta, descripcion, precio, duracion, imagen, destacado, orden) VALUES
('Cambio de Aceite Básico', 'cambio-aceite-basico', 'Cambio de aceite y filtro para mantener tu motor en óptimas condiciones', 'Nuestro servicio de cambio de aceite básico incluye el reemplazo del aceite de motor y el filtro de aceite. Utilizamos aceites de alta calidad que cumplen con las especificaciones del fabricante de su vehículo. Este mantenimiento es esencial para prolongar la vida útil del motor y mantener un rendimiento óptimo.', 29.99, 30, 'img/servicios/cambio-aceite-basico.jpg', 1, 1),
('Cambio de Aceite Premium', 'cambio-aceite-premium', 'Cambio de aceite sintético de alta calidad para mayor protección y rendimiento', 'El cambio de aceite premium incluye aceite sintético de alta calidad, filtro de aceite premium, revisión de niveles de fluidos, inspección visual de frenos, batería y sistema de enfriamiento. Este servicio proporciona una protección superior para el motor, especialmente en condiciones extremas de temperatura y conducción.', 49.99, 45, 'img/servicios/cambio-aceite-premium.jpg', 1, 2),
('Venta e Instalación de Llantas', 'venta-instalacion-llantas', 'Amplia selección de llantas de las mejores marcas con instalación profesional', 'Ofrecemos una amplia variedad de llantas de las mejores marcas del mercado. Nuestro servicio incluye la instalación profesional, balanceo y alineación para garantizar un rendimiento óptimo y seguridad en la carretera. Contamos con opciones para todo tipo de vehículos y presupuestos.', 79.99, 60, 'img/servicios/llantas.jpg', 1, 3),
('Alineación y Balanceo', 'alineacion-balanceo', 'Servicio completo de alineación y balanceo para un manejo suave y seguro', 'La alineación y balanceo son fundamentales para el correcto funcionamiento de su vehículo. Este servicio ayuda a prevenir el desgaste irregular de las llantas, mejora la estabilidad del vehículo, reduce el consumo de combustible y proporciona una conducción más suave y segura.', 59.99, 60, 'img/servicios/alineacion-balanceo.jpg', 1, 4),
('Revisión de Frenos', 'revision-frenos', 'Inspección completa del sistema de frenos para garantizar tu seguridad', 'Nuestro servicio de revisión de frenos incluye la inspección de pastillas, discos, tambores, líquido de frenos y componentes hidráulicos. Garantizamos la seguridad de su vehículo con un sistema de frenos en óptimas condiciones. Recomendamos esta revisión al menos dos veces al año.', 39.99, 45, 'img/servicios/frenos.jpg', 0, 5);

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, slug, descripcion_corta, descripcion, precio, stock, imagen, destacado, categoria, marca) VALUES
('Llanta Michelin Primacy 4 205/55R16', 'llanta-michelin-primacy-4', 'Llanta de alto rendimiento para vehículos compactos y sedanes', 'La Michelin Primacy 4 ofrece un excelente rendimiento en mojado y seco, con una larga vida útil. Diseñada para vehículos compactos y sedanes, proporciona confort, seguridad y eficiencia de combustible. Incluye tecnología EverGrip para mantener un alto nivel de agarre durante toda la vida de la llanta.', 129.99, 20, 'img/productos/michelin-primacy.jpg', 1, 'Llantas', 'Michelin'),
('Llanta Bridgestone Turanza T005 225/45R17', 'llanta-bridgestone-turanza', 'Llanta premium para sedanes deportivos con excelente rendimiento en mojado', 'La Bridgestone Turanza T005 es una llanta premium diseñada para sedanes deportivos. Ofrece un excelente control en superficies mojadas, reducción de ruido y mayor comodidad de conducción. Su compuesto de sílice de última generación proporciona un agarre excepcional y resistencia al desgaste.', 149.99, 15, 'img/productos/bridgestone-turanza.jpg', 1, 'Llantas', 'Bridgestone'),
('Llanta Goodyear Eagle F1 Asymmetric 5 245/40R18', 'llanta-goodyear-eagle-f1', 'Llanta ultra-high performance para vehículos deportivos', 'La Goodyear Eagle F1 Asymmetric 5 es una llanta ultra-high performance diseñada para vehículos deportivos. Ofrece un agarre excepcional en curvas, distancias de frenado más cortas y mejor manejo en condiciones secas y mojadas. Su tecnología Active Braking mejora la seguridad en situaciones de frenado de emergencia.', 189.99, 10, 'img/productos/goodyear-eagle.jpg', 1, 'Llantas', 'Goodyear'),
('Aceite Mobil 1 5W-30 Sintético (4L)', 'aceite-mobil-1-5w30', 'Aceite sintético de alto rendimiento para motores modernos', 'Mobil 1 5W-30 es un aceite de motor totalmente sintético diseñado para mantener su motor funcionando como nuevo. Proporciona un rendimiento excepcional en temperaturas extremas, control de depósitos y protección contra el desgaste. Recomendado para vehículos de alto rendimiento y condiciones de conducción exigentes.', 49.99, 30, 'img/productos/mobil-1.jpg', 1, 'Aceites', 'Mobil'),
('Aceite Castrol EDGE 5W-40 Sintético (4L)', 'aceite-castrol-edge-5w40', 'Aceite sintético con tecnología Fluid Titanium para máxima protección', 'Castrol EDGE con tecnología Fluid Titanium transforma su estructura física bajo presión para proporcionar una protección superior contra el desgaste. Este aceite totalmente sintético está diseñado para motores de alto rendimiento y ayuda a maximizar el rendimiento del motor incluso en las condiciones más exigentes.', 54.99, 25, 'img/productos/castrol-edge.jpg', 0, 'Aceites', 'Castrol');

-- Insertar promociones de ejemplo
INSERT INTO promociones (titulo, descripcion, descuento, imagen, fecha_inicio, fecha_fin, codigo) VALUES
('Cambio de Aceite + Revisión Gratuita', 'Aprovecha nuestro cambio de aceite básico y recibe una revisión completa de 10 puntos totalmente gratis.', 15.00, 'img/promociones/promo-aceite.jpg', '2023-01-01', '2023-12-31', 'ACEITE15'),
('2x1 en Alineación y Balanceo', 'Por la compra de un servicio de alineación y balanceo, recibe otro completamente gratis para un segundo vehículo.', 50.00, 'img/promociones/promo-alineacion.jpg', '2023-01-01', '2023-12-31', 'ALINEA2X1'),
('20% de Descuento en Llantas', 'Renueva las llantas de tu vehículo con un 20% de descuento en todas las marcas disponibles.', 20.00, 'img/promociones/promo-llantas.jpg', '2023-01-01', '2023-12-31', 'LLANTAS20');
