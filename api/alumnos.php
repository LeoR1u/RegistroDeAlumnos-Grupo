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
$incluirInactivos = isset($_GET['incluir_inactivos']) && $_GET['incluir_inactivos'] == 1;

switch ($method) {
// Modificar el switch case GET
    case 'GET':
        $id ? getAlumno($db, $id) : getAlumnos($db, $incluirInactivos);
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

// Al inicio, después de obtener el método

// Modificar getAlumnos
function getAlumnos($db, $incluirInactivos = false) {
    try {
        $where = $incluirInactivos ? "" : "WHERE activo = 1";
        $stmt = $db->query("
            SELECT id_alumno, nombre, apellido_paterno, apellido_materno, 
                   nombre_completo, id_grupo, grupo, carrera, turno, activo
            FROM vista_alumnos
            $where
            ORDER BY apellido_paterno, apellido_materno, nombre
        ");
        jsonSuccess($stmt->fetchAll());
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Modificar updateAlumno para soportar solo cambio de activo
function updateAlumno($db, $id) {
    $data = getRequestData();
    
    try {
        // Verificar que existe
        $stmt = $db->prepare("SELECT id_alumno FROM alumnos WHERE id_alumno = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Alumno no encontrado', 404);
        
        // Si solo se envía 'activo', actualizar solo ese campo
        if (isset($data['activo']) && count($data) == 1) {
            $activo = (int)$data['activo'];
            $stmt = $db->prepare("UPDATE alumnos SET activo = ? WHERE id_alumno = ?");
            $stmt->execute([$activo, $id]);
            
            $mensaje = $activo == 1 ? 'Alumno activado correctamente' : 'Alumno desactivado correctamente';
            jsonSuccess(null, $mensaje);
        } else {
            // Actualización completa
            validateRequired($data, ['nombre', 'apellido_paterno', 'id_grupo']);
            
            $nombre = sanitize($data['nombre']);
            $apellidoP = sanitize($data['apellido_paterno']);
            $apellidoM = isset($data['apellido_materno']) ? sanitize($data['apellido_materno']) : null;
            $idGrupo = (int)$data['id_grupo'];
            
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
        }
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// deleteAlumno ahora solo desactiva
function deleteAlumno($db, $id) {
    try {
        $stmt = $db->prepare("SELECT id_alumno FROM alumnos WHERE id_alumno = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Alumno no encontrado', 404);
        
        $stmt = $db->prepare("UPDATE alumnos SET activo = 0 WHERE id_alumno = ?");
        $stmt->execute([$id]);
        
        jsonSuccess(null, 'Alumno desactivado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}
?>
