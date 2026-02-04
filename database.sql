-- ============================================
-- BASE DE DATOS: Registro de Alumnos y Grupos
-- Importar en phpMyAdmin o MySQL
-- ============================================

CREATE DATABASE IF NOT EXISTS registro_alumnos 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_spanish_ci;

USE registro_alumnos;

-- ============================================
-- TABLA: carreras
-- ============================================
CREATE TABLE IF NOT EXISTS carreras (
    id_carrera INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    abreviatura VARCHAR(10) NOT NULL UNIQUE,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA: turnos
-- ============================================
CREATE TABLE IF NOT EXISTS turnos (
    id_turno INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    abreviatura CHAR(1) NOT NULL UNIQUE,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- ============================================
-- TABLA: grupos
-- ============================================
CREATE TABLE IF NOT EXISTS grupos (
    id_grupo INT AUTO_INCREMENT PRIMARY KEY,
    id_carrera INT NOT NULL,
    id_turno INT NOT NULL,
    grado TINYINT NOT NULL,
    clave VARCHAR(20) NOT NULL UNIQUE,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON DELETE RESTRICT,
    FOREIGN KEY (id_turno) REFERENCES turnos(id_turno) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA: alumnos
-- ============================================
CREATE TABLE IF NOT EXISTS alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50),
    id_grupo INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- ÍNDICES
-- ============================================
CREATE INDEX idx_alumnos_grupo ON alumnos(id_grupo);
CREATE INDEX idx_alumnos_nombre ON alumnos(apellido_paterno, apellido_materno, nombre);
CREATE INDEX idx_grupos_carrera ON grupos(id_carrera);

-- ============================================
-- DATOS INICIALES: Carreras
-- ============================================
INSERT INTO carreras (nombre, abreviatura) VALUES
('Ingeniería en Sistemas Computacionales', 'ISC'),
('Ingeniería Industrial', 'II'),
('Ingeniería en Gestión Empresarial', 'IGE'),
('Ingeniería Electromecánica', 'IEM'),
('Ingeniería Civil', 'IC'),
('Ingeniería Química', 'IQ'),
('Licenciatura en Administración', 'LA'),
('Contador Público', 'CP');

-- ============================================
-- DATOS INICIALES: Turnos
-- ============================================
INSERT INTO turnos (nombre, abreviatura) VALUES
('Matutino', 'M'),
('Vespertino', 'V'),
('Mixto', 'X');

-- ============================================
-- DATOS INICIALES: Grupos de ejemplo
-- ============================================
INSERT INTO grupos (id_carrera, id_turno, grado, clave) VALUES
(1, 1, 5, 'ISC501-M'),
(1, 2, 5, 'ISC501-V'),
(1, 1, 3, 'ISC301-M'),
(2, 1, 3, 'II301-M'),
(2, 2, 3, 'II301-V');

-- ============================================
-- DATOS INICIALES: Alumnos de ejemplo
-- ============================================
INSERT INTO alumnos (nombre, apellido_paterno, apellido_materno, id_grupo) VALUES
('Juan', 'Pérez', 'García', 1),
('María', 'López', 'Hernández', 1),
('Carlos', 'Rodríguez', 'Martínez', 2),
('Ana', 'González', 'Sánchez', 3);

-- ============================================
-- VISTA: alumnos con información completa
-- ============================================
CREATE OR REPLACE VIEW vista_alumnos AS
SELECT 
    a.id_alumno,
    a.nombre,
    a.apellido_paterno,
    a.apellido_materno,
    CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', COALESCE(a.apellido_materno, '')) AS nombre_completo,
    a.id_grupo,
    g.clave AS grupo,
    g.grado,
    c.nombre AS carrera,
    c.abreviatura AS carrera_abrev,
    t.nombre AS turno,
    a.activo,
    a.created_at
FROM alumnos a
INNER JOIN grupos g ON a.id_grupo = g.id_grupo
INNER JOIN carreras c ON g.id_carrera = c.id_carrera
INNER JOIN turnos t ON g.id_turno = t.id_turno;

-- ============================================
-- VISTA: grupos con información completa
-- ============================================
CREATE OR REPLACE VIEW vista_grupos AS
SELECT 
    g.id_grupo,
    g.clave,
    g.grado,
    g.id_carrera,
    c.nombre AS carrera,
    c.abreviatura AS carrera_abrev,
    g.id_turno,
    t.nombre AS turno,
    t.abreviatura AS turno_abrev,
    g.activo,
    (SELECT COUNT(*) FROM alumnos WHERE id_grupo = g.id_grupo AND activo = 1) AS total_alumnos
FROM grupos g
INNER JOIN carreras c ON g.id_carrera = c.id_carrera
INNER JOIN turnos t ON g.id_turno = t.id_turno;
