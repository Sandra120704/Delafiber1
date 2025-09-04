-- Active: 1755358617783@@127.0.0.1@3306@delafiber
CREATE DATABASE delafiber;
USE delafiber;

CREATE TABLE departamentos (
    iddepartamento INT AUTO_INCREMENT PRIMARY KEY,
    departamento VARCHAR(50) NOT NULL
);

CREATE TABLE provincias (
    idprovincia INT AUTO_INCREMENT PRIMARY KEY,
    provincia VARCHAR(50) NOT NULL,
    iddepartamento INT NOT NULL,
    CONSTRAINT fk_provincia_departamento FOREIGN KEY (iddepartamento) REFERENCES departamentos(iddepartamento)
);

CREATE TABLE distritos (
    iddistrito INT AUTO_INCREMENT PRIMARY KEY,
    distrito VARCHAR(50) NOT NULL,
    idprovincia INT NOT NULL,
    CONSTRAINT fk_distrito_provincia FOREIGN KEY (idprovincia) REFERENCES provincias(idprovincia)
);

CREATE TABLE personas (
    idpersona INT AUTO_INCREMENT PRIMARY KEY,
    apellidos VARCHAR(100) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    telprimario VARCHAR(20) NOT NULL,
    telalternativo VARCHAR(20),
    email VARCHAR(150),
    direccion TEXT,
    referencia TEXT,
    iddistrito INT NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_persona_distrito FOREIGN KEY (iddistrito) REFERENCES distritos(iddistrito)
);


CREATE TABLE roles (
    idrol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150)
);

CREATE TABLE usuarios (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    idrol INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modificado TIMESTAMP NULL,
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idrol) REFERENCES roles(idrol)
);

CREATE TABLE campanias (
    idcampania INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fechainicio DATE NOT NULL,
    fechafin DATE NOT NULL,
    inversion DECIMAL(9,2),
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME
);

CREATE TABLE medios (
    idmedio INT AUTO_INCREMENT PRIMARY KEY,
    tipo_medio ENUM('REDES SOCIALES','PRESENCIAL') NOT NULL,
    medio VARCHAR(100) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME
);

CREATE TABLE difusiones (
    iddifusion INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    idmedio INT NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_difusion_campania FOREIGN KEY (idcampania) REFERENCES campanias(idcampania),
    CONSTRAINT fk_difusion_medio FOREIGN KEY (idmedio) REFERENCES medios(idmedio)
);


CREATE TABLE pipelines (
    idpipeline INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(150)
);

CREATE TABLE etapas (
    idetapa INT AUTO_INCREMENT PRIMARY KEY,
    idpipeline INT NOT NULL,
    nombreetapa VARCHAR(100) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_etapa_pipeline FOREIGN KEY (idpipeline) REFERENCES pipelines(idpipeline)
);
ALTER TABLE etapas ADD COLUMN orden INT NOT NULL DEFAULT 0;

CREATE TABLE leads (    
    idlead INT AUTO_INCREMENT PRIMARY KEY,
    iddifusion INT NOT NULL,
    idpersona INT NOT NULL,
    idusuarioregistro INT NOT NULL,
    idusuarioresponsable INT NOT NULL,
    idetapa INT NOT NULL,
    fechasignacion DATE NOT NULL,
    estatus_global ENUM('nuevo', 'en proceso', 'ganado', 'perdido') DEFAULT 'nuevo',
    fecharegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_leads_difusion FOREIGN KEY (iddifusion) REFERENCES difusiones(iddifusion),
    CONSTRAINT fk_leads_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_leads_usuario_registro FOREIGN KEY (idusuarioregistro) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_leads_usuario_responsable FOREIGN KEY (idusuarioresponsable) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_leads_etapa FOREIGN KEY (idetapa) REFERENCES etapas(idetapa)
);


CREATE TABLE modalidades_contacto (
    idmodalidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE seguimientos (
    idseguimiento INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idmodalidad INT,
    fecha DATE NOT NULL,
    hora TIME,
    comentarios TEXT,
    resultado_contacto ENUM('interesado','no contesta','rechazado','equivocado'),
    proxima_accion VARCHAR(150),
    proxima_fecha DATE,
    estado ENUM('pendiente','realizado') DEFAULT 'pendiente',
    iniciador ENUM('cliente','asesor') DEFAULT 'asesor',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_seguimiento_lead FOREIGN KEY (idlead) REFERENCES leads(idlead),
    CONSTRAINT fk_seguimiento_modalidad FOREIGN KEY (idmodalidad) REFERENCES modalidades_contacto(idmodalidad)
);

CREATE TABLE tareas (
    idtarea INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idusuario INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    fecha_vencimiento DATETIME NOT NULL,
    estado ENUM('pendiente','completada','vencida') DEFAULT 'pendiente',
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tarea_lead FOREIGN KEY (idlead) REFERENCES leads(idlead),
    CONSTRAINT fk_tarea_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario)
);



INSERT INTO departamentos (departamento) VALUES ('Ica');

INSERT INTO provincias (provincia, iddepartamento) VALUES ('Chincha', 1);

INSERT INTO distritos (distrito, idprovincia) VALUES 
('Chincha Alta', 1),
('Sunampe', 1),
('Grocio Prado', 1),
('Pueblo Nuevo', 1);

SELECT * FROM personas;
INSERT INTO personas (apellidos, nombres, telprimario, telalternativo, email, direccion, referencia, iddistrito) VALUES
('Perez', 'Juan', '999111222', NULL, 'juan.perez@gmail.com', 'Av. Los Incas 123', 'Frente al mercado', 1),
('Lopez', 'Maria', '999222333', '988776655', 'maria.lopez@yahoo.com', 'Calle Principal 456', 'Cerca al colegio', 2),
('Garcia', 'Carlos', '999333444', NULL, 'carlos.garcia@hotmail.com', 'Jr. Libertad 789', 'Esquina con Av. Grau', 3),
('Torres', 'Ana', '999444555', NULL, 'ana.torres@gmail.com', 'Urb. Las Flores Mz A Lt 10', 'Altura parque central', 4);

INSERT INTO roles (nombre, descripcion) VALUES
('admin', 'Acceso total al sistema'),
('vendedor', 'Gestiona leads y clientes'),
('supervisor', 'Supervisa y controla reportes');

INSERT INTO usuarios (idpersona, username, password, idrol) VALUES
(1, 'jperez', '123456', 1),
(2, 'mlopez', '123456', 2),
(3, 'cgarcia', '123456', 2),
(4, 'atorres', '123456', 2);

INSERT INTO campanias (nombre, descripcion, fechainicio, fechafin, inversion, estado) VALUES 
('Campaña Facebook Chincha', 'Captación de clientes por redes sociales', '2025-01-01', '2025-03-31', 1500.00, 'activo');

INSERT INTO medios (tipo_medio, medio) VALUES
('REDES SOCIALES', 'Facebook Ads'),
('PRESENCIAL', 'Volanteo en Chincha');
DESCRIBE medios;

INSERT INTO difusiones (idcampania, idmedio) VALUES
(1, 1),
(1, 2);

INSERT INTO pipelines (nombre, descripcion) VALUES
('Ventas principales', 'Pipeline general de ventas');

INSERT INTO etapas (idpipeline, nombreetapa, activo) VALUES 
(1, 'CAPTACIÓN', TRUE),
(1, 'CONVERSIÓN', TRUE),
(1, 'VENTA', TRUE),
(1, 'FIDELIZACIÓN', TRUE);

INSERT INTO leads (iddifusion, idpersona, idusuarioregistro, idusuarioresponsable, idetapa, fechasignacion, estatus_global) VALUES
(1, 1, 1, 2, 1, '2025-02-01', 'nuevo'),
(1, 2, 1, 3, 2, '2025-02-05', 'en proceso'),
(2, 3, 1, 4, 3, '2025-02-10', 'en proceso'),
(2, 4, 1, 1, 4, '2025-02-15', 'perdido');


INSERT INTO modalidades_contacto (nombre) VALUES
('Llamada telefónica'),
('WhatsApp'),
('Correo electrónico'),
('Reunión presencial');

-- Consulta de prueba
SELECT l.idlead, p.nombres, p.apellidos, e.nombreetapa, l.estatus_global
FROM leads l
INNER JOIN personas p ON l.idpersona = p.idpersona
INNER JOIN etapas e ON l.idetapa = e.idetapa;
SELECT c.*, GROUP_CONCAT(m.medio SEPARATOR ', ') as medios
FROM campanias c
LEFT JOIN difusiones d ON c.idcampania = d.idcampania
LEFT JOIN medios m ON d.idmedio = m.idmedio
GROUP BY c.idcampania;
