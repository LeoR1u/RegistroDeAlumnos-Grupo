<?php
/**
 * API REST - ALUMNOS
 * GET    /api/alumnos.php         → Listar todos
 * GET    /api/alumnos.php?id=1    → Obtener uno
 * POST   /api/alumnos.php         → Crear
 * PUT    /api/alumnos.php?id=1    → Actualizar
 * DELETE /api/alumnos.php?id=1    → Eliminar
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

setApiHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        $id ? getAlumno($db, $id) : getAlumnos($db);
        break;
    case 'POST':
        createAlumno($db);
        break;
    case 'PUT':
        if (!$id) jsonError('ID requerido');
        updateAlumno($db, $id);
        break;
    case 'DELETE':
        if (!$id) jsonError('ID requerido');
        deleteAlumno($db, $id);
        break;
    default:
        jsonError('Método no permitido', 405);
}

// Obtener todos los alumnos
function getAlumnos($db) {
    try {
        $stmt = $db->query("
            SELECT id_alumno, nombre, apellido_paterno, apellido_materno, 
                   nombre_completo, id_grupo, grupo, carrera, turno
            FROM vista_alumnos
            WHERE activo = 1
            ORDER BY apellido_paterno, apellido_materno, nombre
        ");
        jsonSuccess($stmt->fetchAll());
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Obtener un alumno
function getAlumno($db, $id) {
    try {
        $stmt = $db->prepare("
            SELECT id_alumno, nombre, apellido_paterno, apellido_materno,
                   nombre_completo, id_grupo, grupo, carrera, turno
            FROM vista_alumnos
            WHERE id_alumno = ? AND activo = 1
        ");
        $stmt->execute([$id]);
        $alumno = $stmt->fetch();
        
        if (!$alumno) jsonError('Alumno no encontrado', 404);
        jsonSuccess($alumno);
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Crear alumno
function createAlumno($db) {
    $data = getRequestData();
    validateRequired($data, ['nombre', 'apellido_paterno', 'id_grupo']);
    
    $nombre = sanitize($data['nombre']);
    $apellidoP = sanitize($data['apellido_paterno']);
    $apellidoM = isset($data['apellido_materno']) ? sanitize($data['apellido_materno']) : null;
    $idGrupo = (int)$data['id_grupo'];
    
    try {
        // Verificar que el grupo existe
        $stmt = $db->prepare("SELECT id_grupo FROM grupos WHERE id_grupo = ? AND activo = 1");
        $stmt->execute([$idGrupo]);
        if (!$stmt->fetch()) jsonError('El grupo no existe');
        
        // Insertar
        $stmt = $db->prepare("
            INSERT INTO alumnos (nombre, apellido_paterno, apellido_materno, id_grupo)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$nombre, $apellidoP, $apellidoM, $idGrupo]);
        
        $newId = $db->lastInsertId();
        
        // Devolver alumno creado
        $stmt = $db->prepare("SELECT * FROM vista_alumnos WHERE id_alumno = ?");
        $stmt->execute([$newId]);
        
        jsonSuccess($stmt->fetch(), 'Alumno registrado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Actualizar alumno
function updateAlumno($db, $id) {
    $data = getRequestData();
    validateRequired($data, ['nombre', 'apellido_paterno', 'id_grupo']);
    
    $nombre = sanitize($data['nombre']);
    $apellidoP = sanitize($data['apellido_paterno']);
    $apellidoM = isset($data['apellido_materno']) ? sanitize($data['apellido_materno']) : null;
    $idGrupo = (int)$data['id_grupo'];
    
    try {
        // Verificar que existe
        $stmt = $db->prepare("SELECT id_alumno FROM alumnos WHERE id_alumno = ? AND activo = 1");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Alumno no encontrado', 404);
        
        // Verificar grupo
        $stmt = $db->prepare("SELECT id_grupo FROM grupos WHERE id_grupo = ? AND activo = 1");
        $stmt->execute([$idGrupo]);
        if (!$stmt->fetch()) jsonError('El grupo no existe');
        
        // Actualizar
        $stmt = $db->prepare("
            UPDATE alumnos 
            SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, id_grupo = ?
            WHERE id_alumno = ?
        ");
        $stmt->execute([$nombre, $apellidoP, $apellidoM, $idGrupo, $id]);
        
        // Devolver actualizado
        $stmt = $db->prepare("SELECT * FROM vista_alumnos WHERE id_alumno = ?");
        $stmt->execute([$id]);
        
        jsonSuccess($stmt->fetch(), 'Alumno actualizado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Eliminar alumno (soft delete)
function deleteAlumno($db, $id) {
    try {
        $stmt = $db->prepare("SELECT id_alumno FROM alumnos WHERE id_alumno = ? AND activo = 1");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Alumno no encontrado', 404);
        
        $stmt = $db->prepare("UPDATE alumnos SET activo = 0 WHERE id_alumno = ?");
        $stmt->execute([$id]);
        
        jsonSuccess(null, 'Alumno eliminado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}
?>
