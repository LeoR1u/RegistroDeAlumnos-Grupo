<?php
/**
 * API REST - CARRERAS
 * GET    /api/carreras.php         → Listar todas
 * GET    /api/carreras.php?id=1    → Obtener una
 * POST   /api/carreras.php         → Crear
 * PUT    /api/carreras.php?id=1    → Actualizar
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

setApiHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        $id ? getCarrera($db, $id) : getCarreras($db);
        break;
    case 'POST':
        createCarrera($db);
        break;
    case 'PUT':
        if (!$id) jsonError('ID requerido');
        updateCarrera($db, $id);
        break;
    default:
        jsonError('Método no permitido', 405);
}

function getCarreras($db) {
    try {
        $stmt = $db->query("SELECT * FROM carreras ORDER BY nombre");
        jsonSuccess($stmt->fetchAll());
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function getCarrera($db, $id) {
    try {
        $stmt = $db->prepare("SELECT * FROM carreras WHERE id_carrera = ?");
        $stmt->execute([$id]);
        $carrera = $stmt->fetch();
        
        if (!$carrera) jsonError('Carrera no encontrada', 404);
        jsonSuccess($carrera);
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function createCarrera($db) {
    $data = getRequestData();
    validateRequired($data, ['nombre', 'abreviatura']);
    
    $nombre = sanitize($data['nombre']);
    $abreviatura = sanitize($data['abreviatura']);
    
    try {
        $stmt = $db->prepare("INSERT INTO carreras (nombre, abreviatura) VALUES (?, ?)");
        $stmt->execute([$nombre, $abreviatura]);
        
        $newId = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM carreras WHERE id_carrera = ?");
        $stmt->execute([$newId]);
        
        jsonSuccess($stmt->fetch(), 'Carrera registrada correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function updateCarrera($db, $id) {
    $data = getRequestData();
    
    try {
        $stmt = $db->prepare("SELECT * FROM carreras WHERE id_carrera = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Carrera no encontrada', 404);
        
        // Si solo se envía 'activo', actualizar solo ese campo
        if (isset($data['activo']) && count($data) == 1) {
            $activo = (int)$data['activo'];
            $stmt = $db->prepare("UPDATE carreras SET activo = ? WHERE id_carrera = ?");
            $stmt->execute([$activo, $id]);
            
            $mensaje = $activo == 1 ? 'Carrera activada correctamente' : 'Carrera desactivada correctamente';
            jsonSuccess(null, $mensaje);
        } else {
            // Actualización completa
            validateRequired($data, ['nombre', 'abreviatura']);
            
            $nombre = sanitize($data['nombre']);
            $abreviatura = sanitize($data['abreviatura']);
            
            $stmt = $db->prepare("UPDATE carreras SET nombre = ?, abreviatura = ? WHERE id_carrera = ?");
            $stmt->execute([$nombre, $abreviatura, $id]);
            
            $stmt = $db->prepare("SELECT * FROM carreras WHERE id_carrera = ?");
            $stmt->execute([$id]);
            
            jsonSuccess($stmt->fetch(), 'Carrera actualizada correctamente');
        }
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}
?>