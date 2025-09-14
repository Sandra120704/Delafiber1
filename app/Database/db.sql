
CREATE DATABASE delafiber;
USE delafiber;

CREATE TABLE departamentos (
    iddepartamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE provincias (
    idprovincia INT AUTO_INCREMENT PRIMARY KEY,
    iddepartamento INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT fk_provincia_departamento FOREIGN KEY (iddepartamento) REFERENCES departamentos(iddepartamento)
);

CREATE TABLE distritos (
    iddistrito INT AUTO_INCREMENT PRIMARY KEY,
    idprovincia INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT fk_distrito_provincia FOREIGN KEY (idprovincia) REFERENCES provincias(idprovincia)
);

CREATE TABLE personas (
    idpersona INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    dni VARCHAR(20) UNIQUE,
    correo VARCHAR(150),
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    iddistrito INT,
    CONSTRAINT fk_persona_distrito FOREIGN KEY (iddistrito) REFERENCES distritos(iddistrito)
);

CREATE TABLE roles (
    idrol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50) NOT NULL
);

CREATE TABLE usuarios (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    idrol INT NOT NULL,
    idpersona INT,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idrol) REFERENCES roles(idrol),
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona)
);

CREATE TABLE campanias (
    idcampania INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    presupuesto DECIMAL(9,2) DEFAULT 0,
    estado ENUM('Activa','Inactiva') DEFAULT 'Activa'
);

CREATE TABLE medios (
    idmedio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE difusiones (
    iddifusion INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    idmedio INT NOT NULL,
    leads_generados INT DEFAULT 0,
    CONSTRAINT fk_difusion_campania FOREIGN KEY (idcampania) REFERENCES campanias(idcampania),
    CONSTRAINT fk_difusion_medio FOREIGN KEY (idmedio) REFERENCES medios(idmedio)
);

CREATE TABLE origenes (
    idorigen INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE modalidades (
    idmodalidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE pipelines (
    idpipeline INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE etapas (
    idetapa INT AUTO_INCREMENT PRIMARY KEY,
    idpipeline INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    orden INT NOT NULL,
    CONSTRAINT fk_etapa_pipeline FOREIGN KEY (idpipeline) REFERENCES pipelines(idpipeline)
);

CREATE TABLE leads (
    idlead INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT NOT NULL,
    iddifusion INT,
    idetapa INT,
    idusuario INT,
    idorigen INT,
    estado ENUM('Convertido','Descartado') DEFAULT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lead_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_lead_difusion FOREIGN KEY (iddifusion) REFERENCES difusiones(iddifusion),
    CONSTRAINT fk_lead_etapa FOREIGN KEY (idetapa) REFERENCES etapas(idetapa),
    CONSTRAINT fk_lead_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_lead_origen FOREIGN KEY (idorigen) REFERENCES origenes(idorigen)
);

CREATE TABLE seguimiento (
    idseguimiento INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idusuario INT NOT NULL,
    idmodalidad INT NOT NULL,
    nota TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_seguimiento_lead FOREIGN KEY (idlead) REFERENCES leads(idlead),
    CONSTRAINT fk_seguimiento_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_seguimiento_modalidad FOREIGN KEY (idmodalidad) REFERENCES modalidades(idmodalidad)
);

CREATE TABLE tareas (
    idtarea INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idusuario INT NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado ENUM('Pendiente','En progreso','Completada') DEFAULT 'Pendiente',
    CONSTRAINT fk_tarea_lead FOREIGN KEY (idlead) REFERENCES leads(idlead),
    CONSTRAINT fk_tarea_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario)
);

DELIMITER //
CREATE TRIGGER trg_update_leads_generados
AFTER INSERT ON leads
FOR EACH ROW
BEGIN
    IF NEW.iddifusion IS NOT NULL THEN
        UPDATE difusiones
        SET leads_generados = leads_generados + 1
        WHERE iddifusion = NEW.iddifusion;
    END IF;
END;
//
DELIMITER ;

-- =========================
-- INSERTS DE PRUEBA
-- =========================

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

INSERT INTO origenes (nombre) VALUES 
('Campaña'), 
('Referido'), 
('Contacto Directo'), 
('Evento'), 
('Marketing Offline'), 
('Redes Sociales Orgánicas'), 
('Otro');

INSERT INTO campanias (nombre, descripcion, fecha_inicio, fecha_fin, presupuesto, estado) VALUES 
('Campaña Facebook Chincha', 'Captación de clientes por redes sociales', '2025-01-01', '2025-03-31', 1500.00, 'Activa');

INSERT INTO medios (nombre, descripcion) VALUES
('Facebook', 'Publicidad en Facebook'),
('Volanteo en Chincha', 'Distribución de volantes en la ciudad'),
('Referido', 'Lead por recomendación');

INSERT INTO difusiones (idcampania, idmedio) VALUES 
(1,1),
(1,2);

INSERT INTO pipelines (nombre, descripcion) VALUES 
('Ventas principales', 'Pipeline general de ventas');

INSERT INTO etapas (idpipeline, nombre, orden) VALUES
(1, 'CAPTACIÓN', 1),
(1, 'CONVERSIÓN', 2),
(1, 'VENTA', 3),
(1, 'FIDELIZACIÓN', 4);

INSERT INTO modalidades (nombre) VALUES 
('Llamada telefónica'), 
('WhatsApp'), 
('Correo electrónico'), 
('Reunión presencial');
