-- Active: 1743133057434@@127.0.0.1@3306@delafiber
CREATE DATABASE delafiber;
USE delafiber;

-- ===============================
-- 1. UBICACIÓN
-- ===============================
CREATE TABLE departamentos (
    iddepartamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE provincias (
    idprovincia INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    iddepartamento INT NOT NULL,
    CONSTRAINT fk_provincia_departamento FOREIGN KEY (iddepartamento) REFERENCES departamentos(iddepartamento)
);

CREATE TABLE distritos (
    iddistrito INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    idprovincia INT NOT NULL,
    CONSTRAINT fk_distrito_provincia FOREIGN KEY (idprovincia) REFERENCES provincias(idprovincia)
);

CREATE TABLE personas (
    idpersona INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    dni VARCHAR(8) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    direccion VARCHAR(200),
    iddistrito INT NOT NULL,
    CONSTRAINT fk_persona_distrito FOREIGN KEY (iddistrito) REFERENCES distritos(iddistrito),
    CONSTRAINT unq_persona_dni UNIQUE (dni),
    CONSTRAINT unq_persona_correo UNIQUE (correo),
    CONSTRAINT unq_persona_telefono UNIQUE (telefono)
);


CREATE TABLE roles (
    idrol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(150),
    CONSTRAINT unq_rol_nombre UNIQUE (nombre)
);

CREATE TABLE usuarios (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    idrol INT NOT NULL,
    idpersona INT,
    activo TINYINT(1) DEFAULT 1,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idrol) REFERENCES roles(idrol),
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT unq_usuario_usuario UNIQUE (usuario)
);

CREATE TABLE campanias (
    idcampania INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    presupuesto DECIMAL(10,2),
    estado VARCHAR(50)
);

CREATE TABLE medios (
    idmedio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    CONSTRAINT unq_medio_nombre UNIQUE (nombre)
);
ALTER TABLE medios
ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE TABLE difusiones (
    iddifusion INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    idmedio INT NOT NULL,
    creado DATETIME DEFAULT CURRENT_TIMESTAMP,
    modificado DATETIME,
    CONSTRAINT fk_difusion_campania FOREIGN KEY (idcampania) REFERENCES campanias(idcampania),
    CONSTRAINT fk_difusion_medio FOREIGN KEY (idmedio) REFERENCES medios(idmedio),
    CONSTRAINT unq_difusion UNIQUE (idcampania, idmedio) 
);
ALTER TABLE difusiones
ADD COLUMN inversion DECIMAL(10,2) DEFAULT 0,
ADD COLUMN leads INT DEFAULT 0;
ALTER TABLE difusiones
ADD COLUMN leads_generados INT DEFAULT 0 AFTER inversion;


CREATE TABLE pipelines (
    idpipeline INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    CONSTRAINT unq_pipeline_nombre UNIQUE (nombre)
);

CREATE TABLE etapas (
    idetapa INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    orden INT NOT NULL,
    idpipeline INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_etapa_pipeline FOREIGN KEY (idpipeline) REFERENCES pipelines(idpipeline),
    CONSTRAINT unq_etapa_pipeline UNIQUE (idpipeline, nombre)
);

CREATE TABLE leads (
    idlead INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT NOT NULL,
    idcampania INT,
    idmedio INT,
    idetapa INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50),
    CONSTRAINT fk_leads_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_leads_campania FOREIGN KEY (idcampania) REFERENCES campanias(idcampania),
    CONSTRAINT fk_leads_medio FOREIGN KEY (idmedio) REFERENCES medios(idmedio),
    CONSTRAINT fk_leads_etapa FOREIGN KEY (idetapa) REFERENCES etapas(idetapa)
);
ALTER TABLE leads ADD COLUMN idusuario_registro INT;
ALTER TABLE leads 
ADD CONSTRAINT fk_leads_usuario FOREIGN KEY (idusuario_registro) REFERENCES usuarios(idusuario);


CREATE TABLE modalidades (
    idmodalidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT unq_modalidad_nombre UNIQUE (nombre)
);

CREATE TABLE seguimiento (
    idseguimiento INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idusuario INT NOT NULL,
    idmodalidad INT NOT NULL,
    comentario TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_seguimiento_lead FOREIGN KEY (idlead) REFERENCES leads(idlead),
    CONSTRAINT fk_seguimiento_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_seguimiento_modalidad FOREIGN KEY (idmodalidad) REFERENCES modalidades(idmodalidad)
);

CREATE TABLE tareas (
    idtarea INT AUTO_INCREMENT PRIMARY KEY,
    idusuario INT NOT NULL,
    idlead INT NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_programada DATETIME NOT NULL,
    fecha_realizada DATETIME,
    estado VARCHAR(50) DEFAULT 'pendiente',
    CONSTRAINT fk_tarea_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_tarea_lead FOREIGN KEY (idlead) REFERENCES leads(idlead)
);

INSERT INTO departamentos (nombre) VALUES ('Ica');

INSERT INTO provincias (nombre, iddepartamento) VALUES ('Chincha', 1);

INSERT INTO distritos (nombre, idprovincia) VALUES 
('Chincha Alta', 1),
('Sunampe', 1),
('Grocio Prado', 1),
('Pueblo Nuevo', 1);


INSERT INTO personas (nombres, apellidos, dni, correo, telefono, direccion, iddistrito) VALUES
('Juan', 'Perez', '12345678', 'juan.perez@gmail.com', '999111222', 'Av. Los Incas 123', 1),
('Maria', 'Lopez', '87654321', 'maria.lopez@yahoo.com', '999222333', 'Calle Principal 456', 2),
('Carlos', 'Garcia', '11223344', 'carlos.garcia@hotmail.com', '999333444', 'Jr. Libertad 789', 3),
('Ana', 'Torres', '44332211', 'ana.torres@gmail.com', '999444555', 'Urb. Las Flores Mz A Lt 10', 4);


INSERT INTO roles (nombre, descripcion) VALUES
('admin', 'Acceso total al sistema'),
('vendedor', 'Gestiona leads y clientes'),
('supervisor', 'Supervisa y controla reportes');

INSERT INTO usuarios (usuario, clave, idrol, idpersona) VALUES
('jperez', '123456', 1, 1),
('mlopez', '123456', 2, 2),
('cgarcia', '123456', 2, 3),
('atorres', '123456', 2, 4);

INSERT INTO campanias (nombre, descripcion, fecha_inicio, fecha_fin, presupuesto, estado) VALUES 
('Campaña Facebook Chincha', 'Captación de clientes por redes sociales', '2025-01-01', '2025-03-31', 1500.00, 'activo');


INSERT INTO medios (nombre, descripcion) VALUES
('Facebook Ads', 'Publicidad en Facebook'),
('Volanteo en Chincha', 'Distribución de volantes en la ciudad');


INSERT INTO difusiones (idcampania, idmedio) VALUES
(1, 1),
(1, 2);


INSERT INTO pipelines (nombre, descripcion) VALUES ('Ventas principales', 'Pipeline general de ventas');

INSERT INTO etapas (nombre, orden, idpipeline) VALUES
('CAPTACIÓN', 1, 1),
('CONVERSIÓN', 2, 1),
('VENTA', 3, 1),
('FIDELIZACIÓN', 4, 1);

INSERT INTO leads (idpersona, idcampania, idmedio, idetapa, estado) VALUES
(1, 1, 1, 1, 'nuevo'),
(2, 1, 1, 2, 'en proceso'),
(3, 1, 2, 3, 'en proceso'),
(4, 1, 2, 4, 'perdido');

INSERT INTO modalidades (nombre) VALUES
('Llamada telefónica'),
('WhatsApp'),
('Correo electrónico'),
('Reunión presencial');


INSERT INTO seguimiento (idlead, idusuario, idmodalidad, comentario) VALUES
(1, 2, 1, 'Se llamó al cliente, interesado en promoción'),
(2, 3, 2, 'Contacto por WhatsApp, pendiente de respuesta'),
(3, 4, 4, 'Se realizó reunión presencial, interesado en contratar'),
(4, 1, 3, 'Se envió correo, cliente no respondió');

INSERT INTO tareas (idusuario, idlead, descripcion, fecha_programada, estado) VALUES
(2, 1, 'Llamar a Juan Pérez para seguimiento', '2025-09-05 10:00:00', 'pendiente'),
(3, 2, 'Enviar correo a Maria Lopez', '2025-09-05 12:00:00', 'pendiente'),
(4, 3, 'Visita a Carlos Garcia', '2025-09-06 09:00:00', 'pendiente'),
(1, 4, 'Revisar caso Ana Torres', '2025-09-06 15:00:00', 'pendiente');

SELECT l.idlead, 
       CONCAT(p.nombres, ' ', p.apellidos) AS persona,
       u.usuario AS registrado_por,
       e.nombre AS etapa,
       l.estado, l.fecha_registro
FROM leads l
JOIN personas p ON p.idpersona = l.idpersona
JOIN usuarios u ON u.idusuario = l.idusuario
LEFT JOIN etapas e ON e.idetapa = l.idetapa;
