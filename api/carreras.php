<?php
/**
 * API REST - TURNOS
 * GET    /api/turnos.php         → Listar todos
 * GET    /api/turnos.php?id=1    → Obtener uno
 * POST   /api/turnos.php         → Crear
 * PUT    /api/turnos.php?id=1    → Actualizar
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

setApiHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        $id ? getTurno($db, $id) : getTurnos($db);
        break;
    case 'POST':
        createTurno($db);
        break;
    case 'PUT':
        if (!$id) jsonError('ID requerido');
        updateTurno($db, $id);
        break;
    default:
        jsonError('Método no permitido', 405);
}

function getTurnos($db) {
    try {
        $stmt = $db->query("SELECT * FROM turnos ORDER BY id_turno");
        jsonSuccess($stmt->fetchAll());
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function getTurno($db, $id) {
    try {
        $stmt = $db->prepare("SELECT * FROM turnos WHERE id_turno = ?");
        $stmt->execute([$id]);
        $turno = $stmt->fetch();
        
        if (!$turno) jsonError('Turno no encontrado', 404);
        jsonSuccess($turno);
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function createTurno($db) {
    $data = getRequestData();
    validateRequired($data, ['nombre', 'abreviatura']);
    
    $nombre = sanitize($data['nombre']);
    $abreviatura = sanitize($data['abreviatura']);
    
    try {
        $stmt = $db->prepare("INSERT INTO turnos (nombre, abreviatura) VALUES (?, ?)");
        $stmt->execute([$nombre, $abreviatura]);
        
        $newId = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM turnos WHERE id_turno = ?");
        $stmt->execute([$newId]);
        
        jsonSuccess($stmt->fetch(), 'Turno registrado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function updateTurno($db, $id) {
    $data = getRequestData();
    
    try {
        $stmt = $db->prepare("SELECT * FROM turnos WHERE id_turno = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Turno no encontrado', 404);
        
        if (isset($data['activo']) && count($data) == 1) {
            $activo = (int)$data['activo'];
            $stmt = $db->prepare("UPDATE turnos SET activo = ? WHERE id_turno = ?");
            $stmt->execute([$activo, $id]);
            
            $mensaje = $activo == 1 ? 'Turno activado correctamente' : 'Turno desactivado correctamente';
            jsonSuccess(null, $mensaje);
        } else {
            validateRequired($data, ['nombre', 'abreviatura']);
            
            $nombre = sanitize($data['nombre']);
            $abreviatura = sanitize($data['abreviatura']);
            
            $stmt = $db->prepare("UPDATE turnos SET nombre = ?, abreviatura = ? WHERE id_turno = ?");
            $stmt->execute([$nombre, $abreviatura, $id]);
            
            $stmt = $db->prepare("SELECT * FROM turnos WHERE id_turno = ?");
            $stmt->execute([$id]);
            
            jsonSuccess($stmt->fetch(), 'Turno actualizado correctamente');
        }
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}
?>