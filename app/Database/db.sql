-- Active: 1743133057434@@127.0.0.1@3306@mascotas
CREATE DATABASE Delafiber;

USE Delafiber;

-- Departamentos
CREATE TABLE departamento(
    idDepartamento INT AUTO_INCREMENT PRIMARY KEY,
    departamento   VARCHAR(50) NOT NULL
) ENGINE = InnoDB;

-- Provincias
CREATE TABLE provincias(
    idprovincias    INT AUTO_INCREMENT PRIMARY KEY,
    provincias      VARCHAR(50) NOT NULL,
    iddepartamento  INT NOT NULL,
    CONSTRAINT fk_departamento FOREIGN KEY (iddepartamento) REFERENCES departamento(idDepartamento)
) ENGINE = InnoDB;

-- Distritos
CREATE TABLE distritos(
    iddistrito      INT AUTO_INCREMENT PRIMARY KEY,
    distrito        VARCHAR(50) NOT NULL,
    idprovincias    INT NOT NULL,
    CONSTRAINT fk_provincias FOREIGN KEY (idprovincias) REFERENCES provincias(idprovincias)
) ENGINE = InnoDB;

-- Persona
CREATE TABLE persona(
    idpersona       INT AUTO_INCREMENT PRIMARY KEY,
    apellidos       VARCHAR(80) NOT NULL,
    nombres         VARCHAR(80) NOT NULL,
    telprimario     VARCHAR(9) NOT NULL,
    telalternativo  VARCHAR(9) NOT NULL,
    email           VARCHAR(100),
    direccion       VARCHAR(100) NOT NULL,
    referencias     VARCHAR(150),
    iddistrito      INT NOT NULL,
    CONSTRAINT fk_distritos FOREIGN KEY (iddistrito) REFERENCES distritos(iddistrito),
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Usuarios
CREATE TABLE usuarios (
    idusuario       INT PRIMARY KEY AUTO_INCREMENT,
    idpersona       INT NOT NULL UNIQUE,
    nombreusuario   VARCHAR(50) NOT NULL UNIQUE,
    claveacceso     VARCHAR(200) NOT NULL,
    estado          ENUM('activo','inactivo') DEFAULT 'activo',
    CONSTRAINT fk_persona FOREIGN KEY (idpersona) REFERENCES persona(idpersona),
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Campañas
CREATE TABLE campañas (
    idcampaña       INT PRIMARY KEY AUTO_INCREMENT,
    nombre          VARCHAR(150) NOT NULL,
    descripcion     TEXT,
    fechainicio     DATE NOT NULL,
    fechafin        DATE NOT NULL,
    inversion       DECIMAL(9,2),
    estado          ENUM('activo','inactivo') DEFAULT 'activo',
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Medios
CREATE TABLE medios (
    idmedio         INT PRIMARY KEY AUTO_INCREMENT,
    tipo            ENUM('REDES SOCIALES','PRESENCIAL') NOT NULL,
    medio           VARCHAR(100) NOT NULL,
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Difusiones
CREATE TABLE difusiones (
    iddifusion      INT PRIMARY KEY AUTO_INCREMENT,
    idcampaña       INT NOT NULL,
    idmedio         INT NOT NULL,
    CONSTRAINT fk_campaña FOREIGN KEY (idcampaña) REFERENCES campañas(idcampaña),
    CONSTRAINT fk_medio FOREIGN KEY (idmedio) REFERENCES medios(idmedio),
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Leads
CREATE TABLE leads (
    idlead          INT PRIMARY KEY AUTO_INCREMENT,
    iddifusion      INT NOT NULL,
    idpersona       INT NOT NULL,
    fecharegistro    DATETIME DEFAULT CURRENT_TIMESTAMP,
    idusuarioregistro INT NOT NULL,
    CONSTRAINT fk_difusion FOREIGN KEY (iddifusion) REFERENCES difusiones(iddifusion),
    CONSTRAINT fk_personas FOREIGN KEY (idpersona) REFERENCES persona(idpersona),
    CONSTRAINT fk_usuario FOREIGN KEY (idusuarioregistro) REFERENCES usuarios(idusuario),
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Etapas
CREATE TABLE etapas (
    idetapa         INT PRIMARY KEY AUTO_INCREMENT,
    idleadasignado  INT NOT NULL,
    nombreetapa     VARCHAR(100) NOT NULL,
    fechainicio     DATE NOT NULL,
    fechafin        DATE NOT NULL,
    estado          ENUM('en_proceso','finalizado') DEFAULT 'en_proceso',
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Leads Asignados
CREATE TABLE leadasignados (
    idleadasigando  INT PRIMARY KEY AUTO_INCREMENT,
    idusuarioresponsable INT NOT NULL,
    idlead          INT NOT NULL,
    fechasignacion  DATE NOT NULL,
    CONSTRAINT fk_usuarios FOREIGN KEY (idusuarioresponsable) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_lead FOREIGN KEY (idlead) REFERENCES leads(idlead),    
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB;

-- Seguimientos
CREATE TABLE seguimientos (
    idseguimiento    INT PRIMARY KEY AUTO_INCREMENT,
    modalidadcontacto VARCHAR(50),
    fecha            DATE NOT NULL,
    hora             TIME NOT NULL,
    comentarios      TEXT,
    idetapa         INT NOT NULL,
    CONSTRAINT fk_seguimiento FOREIGN KEY (idetapa) REFERENCES etapas(idetapa),
    creado          DATETIME NOT NULL DEFAULT NOW(),
    modificado      DATETIME
) ENGINE = InnoDB; 

/** Inserciones De Registros **/

INSERT INTO departamento (departamento) VALUES 
    ('Ica');

INSERT INTO provincias (provincias, iddepartamento) VALUES 
    ('Chincha', 1),
    ('Ica', 1),
    ('Pisco', 1);

INSERT INTO distritos (distrito, idprovincias) VALUES 
    ('Chincha Alta', 1),
    ('El Carmen', 1),
    ('Grocio Prado', 1),
    ('Chincha Baja', 1),
    ('Sunampe', 1);

INSERT INTO persona (apellidos, nombres, telprimario, telalternativo, email, direccion, referencias, iddistrito) VALUES 
    ('García Pérez', 'María Elena', '987654321', '912345678', 'maria.garcia@example.com', 'Av. Ejército 456', 'Frente al parque', 1),
    ('Rodríguez Vargas', 'Carlos Alberto', '976543218', '923456789', 'carlos.rodriguez@example.com', 'Jr. Unión 123', 'Cerca del mercado', 2),
    ('López Medina', 'Ana Cecilia', '965432187', '934567890', 'ana.lopez@example.com', 'Calle Los Pinos 789', 'Esquina principal', 3),
    ('Martínez Ríos', 'Luis Fernando', '954321876', '945678901', 'luis.martinez@example.com', 'Av. La Marina 321', 'Al lado del colegio', 4),
    ('Díaz Cordero', 'Sofía Patricia', '943218765', '956789012', 'sofia.diaz@example.com', 'Urb. San Nicolas', 'Portón verde', 5);


INSERT INTO usuarios (idpersona, nombreusuario, claveacceso, estado) VALUES 
    (1, 'maria.garcia', 'password123', 'activo'),
    (2, 'carlos.rodriguez', 'securepass', 'activo'),
    (3, 'ana.lopez', 'test1234', 'inactivo'),
    (4, 'luis.martinez', 'mipassword', 'activo'),
    (5, 'sofia.diaz', 'clave123', 'activo');


INSERT INTO campañas (nombre, descripcion, fechainicio, fechafin, inversion, estado) VALUES 
    ('Fibra Óptica Primavera 2025', 'Promoción para nuevos clientes de fibra óptica', '2025-09-01', '2025-09-30', 1500.00, 'activo'),
    ('Internet para tu negocio', 'Campaña dirigida a PYMES', '2025-10-01', '2025-10-31', 2500.00, 'activo'),
    ('Navidad con Delafiber', 'Ofertas especiales por navidad', '2025-12-01', '2025-12-31', 1200.00, 'inactivo'),
    ('Verano Full Internet', 'Promoción veraniega', '2026-01-05', '2026-02-28', 1800.00, 'activo');


INSERT INTO medios (tipo, medio) VALUES 
    ('REDES SOCIALES', 'Facebook'),
    ('REDES SOCIALES', 'Instagram'),
    ('REDES SOCIALES', 'LinkedIn'),
    ('PRESENCIAL', 'Ferias'),
    ('PRESENCIAL', 'Eventos corporativos');


INSERT INTO difusiones (idcampaña, idmedio) VALUES 
    (1, 1),
    (1, 4),
    (2, 2),
    (2, 5),
    (3, 3),
    (4, 1),
    (4, 2);


INSERT INTO leads (iddifusion, idpersona, idusuarioregistro) VALUES 
    (1, 2, 1),
    (2, 3, 2),
    (3, 4, 3),
    (4, 5, 1),
    (5, 1, 4),
    (6, 2, 5),
    (7, 3, 2);


INSERT INTO leadasignados (idusuarioresponsable, idlead, fechasignacion) VALUES 
    (1, 1, '2025-09-01'),
    (2, 2, '2025-09-10'),
    (3, 3, '2025-10-20'),
    (4, 4, '2025-11-15'),
    (5, 5, '2025-12-01'),
    (2, 6, '2025-12-10'),
    (3, 7, '2025-12-15');
    
INSERT INTO etapas (idleadasignado, nombreetapa, fechainicio, fechafin, estado) VALUES 
    (1, 'Captación', '2025-09-01', '2025-09-05', 'finalizado'),
    (1, 'Conversión', '2025-09-06', '2025-09-10', 'finalizado'),
    (1, 'Venta', '2025-09-11', '2025-09-15', 'en_proceso'),
    (2, 'Captación', '2025-09-10', '2025-09-15', 'finalizado'),
    (3, 'Conversión', '2025-10-21', '2025-10-25', 'en_proceso'),
    (4, 'Captación', '2025-11-16', '2025-11-20', 'finalizado');


INSERT INTO seguimientos (modalidadcontacto, fecha, hora, comentarios, idetapa) VALUES 
    ('Llamada telefónica', '2025-09-02', '10:30:00', 'Cliente interesado en servicio residencial', 1),
    ('Email', '2025-09-05', '14:15:00', 'Enviada información detallada del paquete', 1),
    ('Visita presencial', '2025-09-08', '16:00:00', 'Presentación completa del servicio', 2),
    ('Mensaje Whatsapp', '2025-10-22', '09:45:00', 'Cliente solicitó cotización especial', 5),
    ('Llamada telefónica', '2025-11-18', '11:20:00', 'Confirmar disponibilidad en su zona', 6);
    
    SELECT 
    u.nombreusuario AS Usuario_Responsable,
    p.nombres AS Nombre,
    p.apellidos AS Apellidos,
    l.idlead AS ID_Lead,
    la.fechasignacion AS Fecha_Asignacion
	FROM leadasignados la
	JOIN usuarios u ON la.idusuarioresponsable = u.idusuario
	JOIN leads l ON la.idlead = l.idlead
	JOIN persona p ON l.idpersona = p.idpersona
	ORDER BY u.nombreusuario, la.fechasignacion;
