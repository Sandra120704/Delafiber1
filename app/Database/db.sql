-- Active: 1743133057434@@127.0.0.1@3306@delafiber

DROP DATABASE delafiber;

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

ALTER TABLE personas
ADD COLUMN referencias VARCHAR(255);

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
    iddifusion INT NOT NULL,
    idetapa INT NOT NULL,
    idusuario INT NOT NULL,
    idorigen INT NOT NULL,
    estado ENUM('Convertido','Descartado') DEFAULT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lead_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_lead_difusion FOREIGN KEY (iddifusion) REFERENCES difusiones(iddifusion),
    CONSTRAINT fk_lead_etapa FOREIGN KEY (idetapa) REFERENCES etapas(idetapa),
    CONSTRAINT fk_lead_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_lead_origen FOREIGN KEY (idorigen) REFERENCES origenes(idorigen)
);
ALTER TABLE leads ADD COLUMN idlead INT AUTO_INCREMENT PRIMARY KEY;

ALTER TABLE leads MODIFY idetapa INT NOT NULL DEFAULT 1;

SELECT t.idtarea, l.idlead FROM tareas t JOIN leads l ON t.idlead = l.idlead

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
);DESCRIBE leads;

DROP TRIGGER IF EXISTS trg_update_leads_generados;

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
ALTER TABLE leads ADD UNIQUE (idpersona);


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

ALTER TABLE leads DROP FOREIGN KEY fk_lead_difusion;
ALTER TABLE leads DROP COLUMN iddifusion;

-- Paso 2: Añadir las nuevas columnas para el origen, la campaña, la modalidad, el medio y el usuario de registro.
-- Se añaden las columnas en un orden lógico para mantener la estructura legible.
ALTER TABLE leads ADD COLUMN idcampania INT NULL AFTER idpersona;
ALTER TABLE leads ADD COLUMN medio_comunicacion VARCHAR(100) NULL AFTER idcampania;
ALTER TABLE leads ADD COLUMN idmodalidad INT NULL AFTER medio_comunicacion;
ALTER TABLE leads ADD COLUMN idusuario_registro INT NULL AFTER idusuario;
ALTER TABLE leads ADD COLUMN referido_por INT NULL AFTER idusuario_registro;

-- Paso 3: Añadir las llaves foráneas para las nuevas relaciones.
-- Esto asegura la integridad referencial de los datos.
ALTER TABLE leads ADD CONSTRAINT fk_lead_campania FOREIGN KEY (idcampania) REFERENCES campanias(idcampania);
ALTER TABLE leads ADD CONSTRAINT fk_lead_modalidad FOREIGN KEY (idmodalidad) REFERENCES modalidades(idmodalidad);
ALTER TABLE leads ADD CONSTRAINT fk_lead_usuario_registro FOREIGN KEY (idusuario_registro) REFERENCES usuarios(idusuario);
ALTER TABLE leads ADD CONSTRAINT fk_lead_referido_por FOREIGN KEY (referido_por) REFERENCES personas(idpersona);

-- Paso 4: Añadir las columnas de fecha que el modelo de CodeIgniter usará automáticamente.
ALTER TABLE leads ADD COLUMN fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE leads ADD COLUMN fecha_modificacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Nota: Tu tabla ya tenía la columna 'idorigen' y su llave foránea.
-- El script no necesita agregarla, pero se incluyó en el LeadModel actualizado para reflejar la estructura completa.
-- Verificar si existe la tabla leads
SHOW TABLES LIKE 'leads';

-- Ver la estructura de la tabla leads
DESCRIBE leads;

-- Ver todas las tablas de tu base de datos
SHOW TABLES;
SELECT COUNT(*) FROM leads;
SELECT * FROM leads LIMIT 5;
SELECT * FROM tareas LIMIT 5;
ALTER TABLE leads ADD COLUMN idlead INT AUTO_INCREMENT PRIMARY KEY;
-- Insertar leads de prueba
INSERT INTO leads (idpersona, idetapa, idusuario, idorigen, idcampania, idmodalidad) VALUES
(1, 1, 1, 1, 1, 1),
(2, 2, 2, 2, 1, 2),
(3, 1, 3, 1, 1, 1),
(4, 3, 4, 3, 1, 3);
INSERT INTO tareas (idlead, idusuario, descripcion, fecha_inicio, fecha_fin, estado) VALUES
(1, 1, 'Llamar al cliente para agendar cita de instalación', '2025-01-20', '2025-01-22', 'Pendiente'),
(2, 2, 'Enviar cotización por correo electrónico', '2025-01-21', '2025-01-23', 'En progreso'),
(3, 3, 'Realizar visita técnica para evaluar instalación', '2025-01-22', '2025-01-24', 'Completada');
-- Ver los IDs de leads existentes
SELECT idlead, idpersona FROM leads;
ALTER TABLE tareas ADD COLUMN tipo_tarea VARCHAR(50) DEFAULT 'llamada';
ALTER TABLE tareas ADD COLUMN titulo VARCHAR(200) NOT NULL;
ALTER TABLE tareas ADD COLUMN prioridad ENUM('baja','media','alta','urgente') DEFAULT 'media';
ALTER TABLE tareas ADD COLUMN fecha_vencimiento DATETIME NULL;
ALTER TABLE tareas ADD COLUMN fecha_completado DATETIME NULL;
ALTER TABLE tareas ADD COLUMN notas_resultado TEXT NULL;
ALTER TABLE usuarios ADD COLUMN activo BOOLEAN DEFAULT TRUE;
-- ===== ESTRUCTURA DE BASE DE DATOS MEJORADA =====
-- Sistema de Campañas con funcionalidades avanzadas

-- 1. Tabla campanias mejorada
ALTER TABLE campanias; 
ADD COLUMN prioridad ENUM('alta', 'media', 'baja') DEFAULT 'media' AFTER estado;
ADD COLUMN categoria VARCHAR(50) DEFAULT 'general' AFTER prioridad,
ADD COLUMN tags TEXT AFTER categoria,
ADD COLUMN creado_por INT AFTER responsable,
ADD COLUMN fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER fecha_creacion,
ADD COLUMN fecha_finalizacion TIMESTAMP NULL AFTER fecha_actualizacion,
ADD COLUMN objetivo_leads INT DEFAULT 0 AFTER presupuesto,
ADD COLUMN objetivo_roi DECIMAL(5,2) DEFAULT 0.00 AFTER objetivo_leads,
ADD COLUMN meta_descripcion TEXT AFTER notas,
ADD INDEX idx_estado (estado),
ADD INDEX idx_prioridad (prioridad),
ADD INDEX idx_categoria (categoria),
ADD INDEX idx_responsable (responsable),
ADD INDEX idx_creado_por (creado_por),
ADD INDEX idx_fechas (fecha_inicio, fecha_fin);

-- Actualizar el ENUM de estado para incluir más opciones
ALTER TABLE campanias 
MODIFY COLUMN estado ENUM('borrador', 'activa', 'pausada', 'finalizada', 'cancelada') DEFAULT 'borrador';

-- Agregar foreign keys si no existen
ALTER TABLE campanias 
ADD CONSTRAINT fk_campana_responsable 
FOREIGN KEY (responsable) REFERENCES usuarios(idusuario) ON DELETE SET NULL,
ADD CONSTRAINT fk_campana_creado_por 
FOREIGN KEY (creado_por) REFERENCES usuarios(idusuario) ON DELETE SET NULL;

-- 2. Tabla difusiones mejorada
ALTER TABLE difusiones
ADD COLUMN objetivo_leads INT DEFAULT 0 AFTER leads_generados,
ADD COLUMN cpc DECIMAL(10,2) DEFAULT 0.00 AFTER objetivo_leads,
ADD COLUMN cpm DECIMAL(10,2) DEFAULT 0.00 AFTER cpc,
ADD COLUMN impresiones INT DEFAULT 0 AFTER cpm,
ADD COLUMN clics INT DEFAULT 0 AFTER impresiones,
ADD COLUMN conversiones INT DEFAULT 0 AFTER clics,
ADD COLUMN estado ENUM('activo', 'pausado', 'finalizado') DEFAULT 'activo' AFTER conversiones,
ADD COLUMN actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER creado,
ADD INDEX idx_campania (idcampania),
ADD INDEX idx_medio (idmedio),
ADD INDEX idx_estado (estado);

-- 3. Nueva tabla: campana_actividad (log de actividades)
CREATE TABLE campana_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    usuario_id INT,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT,
    datos_previos JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_campania (idcampania),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha),
    INDEX idx_accion (accion),
    
    FOREIGN KEY (idcampania) REFERENCES campanias(idcampania) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(idusuario) ON DELETE SET NULL
);

-- 4. Nueva tabla: campana_archivos (archivos adjuntos)
CREATE TABLE campana_archivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta VARCHAR(500) NOT NULL,
    tipo_mime VARCHAR(100),
    tamanio INT,
    tipo_archivo ENUM('imagen', 'documento', 'video', 'audio', 'otro') DEFAULT 'otro',
    descripcion TEXT,
    subido_por INT,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_campania (idcampania),
    INDEX idx_tipo (tipo_archivo),
    INDEX idx_subido_por (subido_por),
    
    FOREIGN KEY (idcampania) REFERENCES campanias(idcampania) ON DELETE CASCADE,
    FOREIGN KEY (subido_por) REFERENCES usuarios(idusuario) ON DELETE SET NULL
);

-- 5. Nueva tabla: campana_metricas_diarias (métricas históricas)
CREATE TABLE campana_metricas_diarias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    fecha DATE NOT NULL,
    leads_dia INT DEFAULT 0,
    inversion_dia DECIMAL(10,2) DEFAULT 0.00,
    impresiones_dia INT DEFAULT 0,
    clics_dia INT DEFAULT 0,
    conversiones_dia INT DEFAULT 0,
    roi_dia DECIMAL(5,2) DEFAULT 0.00,
    ctr DECIMAL(5,2) DEFAULT 0.00,
    cpc_promedio DECIMAL(10,2) DEFAULT 0.00,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_campana_fecha (idcampania, fecha),
    INDEX idx_campania (idcampania),
    INDEX idx_fecha (fecha),
    
    FOREIGN KEY (idcampania) REFERENCES campanias(idcampania) ON DELETE CASCADE
);

-- 6. Nueva tabla: campana_objetivos (objetivos específicos por campaña)
CREATE TABLE campana_objetivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    tipo_objetivo ENUM('leads', 'roi', 'impresiones', 'clics', 'conversiones', 'presupuesto') NOT NULL,
    valor_objetivo DECIMAL(10,2) NOT NULL,
    valor_actual DECIMAL(10,2) DEFAULT 0.00,
    porcentaje_cumplimiento DECIMAL(5,2) DEFAULT 0.00,
    fecha_limite DATE,
    estado ENUM('pendiente', 'en_progreso', 'cumplido', 'no_cumplido') DEFAULT 'pendiente',
    notas TEXT,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_campania (idcampania),
    INDEX idx_tipo (tipo_objetivo),
    INDEX idx_estado (estado),
    
    FOREIGN KEY (idcampania) REFERENCES campanias(idcampania) ON DELETE CASCADE
);

-- 7. Nueva tabla: campana_comentarios (comentarios y notas)
CREATE TABLE campana_comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    usuario_id INT NOT NULL,
    comentario TEXT NOT NULL,
    tipo ENUM('comentario', 'nota', 'alerta', 'recordatorio') DEFAULT 'comentario',
    es_privado BOOLEAN DEFAULT FALSE,
    fecha_recordatorio TIMESTAMP NULL,
    estado ENUM('activo', 'resuelto', 'archivado') DEFAULT 'activo',
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_campania (idcampania),
    INDEX idx_usuario (usuario_id),
    INDEX idx_tipo (tipo),
    INDEX idx_fecha_recordatorio (fecha_recordatorio),
    
    FOREIGN KEY (idcampania) REFERENCES campanias(idcampania) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(idusuario) ON DELETE CASCADE
);

-- 8. Tabla medios mejorada (si no tiene estos campos)
ALTER TABLE medios 
ADD COLUMN IF NOT EXISTS tipo VARCHAR(50) DEFAULT 'digital',
ADD COLUMN IF NOT EXISTS descripcion TEXT,
ADD COLUMN IF NOT EXISTS configuracion JSON,
ADD COLUMN IF NOT EXISTS estado ENUM('activo', 'inactivo') DEFAULT 'activo',
ADD COLUMN IF NOT EXISTS orden_mostrar INT DEFAULT 0,
ADD INDEX IF NOT EXISTS idx_tipo (tipo),
ADD INDEX IF NOT EXISTS idx_estado (estado);

-- 9. Nueva tabla: plantillas_campana (plantillas reutilizables)
CREATE TABLE plantillas_campana (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50) DEFAULT 'general',
    configuracion JSON NOT NULL,
    medios_incluidos JSON,
    es_publica BOOLEAN DEFAULT FALSE,
    creado_por INT NOT NULL,
    uso_contador INT DEFAULT 0,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_categoria (categoria),
    INDEX idx_creado_por (creado_por),
    INDEX idx_publica (es_publica),
    
    FOREIGN KEY (creado_por) REFERENCES usuarios(idusuario) ON DELETE CASCADE
);

-- 10. Vista para dashboard de métricas
CREATE VIEW vista_dashboard_campanas AS
SELECT 
    c.idcampania,
    c.nombre,
    c.descripcion,
    c.estado,
    c.prioridad,
    c.categoria,
    c.fecha_inicio,
    c.fecha_fin,
    c.presupuesto,
    c.objetivo_leads,
    c.objetivo_roi,
    u.nombre as responsable_nombre,
    u.email as responsable_email,
    uc.nombre as creado_por_nombre,
    COUNT(DISTINCT d.idmedio) as medios_count,
    COALESCE(SUM(d.inversion), 0) as inversion_total,
    COALESCE(SUM(d.leads_generados), 0) as leads_total,
    COALESCE(SUM(d.impresiones), 0) as impresiones_total,
    COALESCE(SUM(d.clics), 0) as clics_total,
    COALESCE(SUM(d.conversiones), 0) as conversiones_total,
    CASE 
        WHEN COALESCE(SUM(d.inversion), 0) > 0

        ALTER TABLE campanias ADD COLUMN responsable INT NULL;
ALTER TABLE campanias 
    ADD CONSTRAINT fk_campania_responsable 
    FOREIGN KEY (responsable) REFERENCES usuarios(idusuario);
    ALTER TABLE campanias ADD COLUMN fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE campanias ADD COLUMN presupuesto DECIMAL(9,2) DEFAULT 0 AFTER fecha_fin;

ALTER TABLE difusiones 
ADD COLUMN presupuesto DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER idmedio;
