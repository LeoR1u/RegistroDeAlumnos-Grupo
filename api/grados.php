<?php
/**
 * API REST - GRADOS
 * GET    /api/grados.php         → Listar todos
 * GET    /api/grados.php?id=1    → Obtener uno
 * POST   /api/grados.php         → Crear
 * PUT    /api/grados.php?id=1    → Actualizar
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

setApiHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        $id ? getGrado($db, $id) : getGrados($db);
        break;
    case 'POST':
        createGrado($db);
        break;
    case 'PUT':
        if (!$id) jsonError('ID requerido');
        updateGrado($db, $id);
        break;
    default:
        jsonError('Método no permitido', 405);
}

function getGrados($db) {
    try {
        $stmt = $db->query("SELECT * FROM grados ORDER BY numero");
        jsonSuccess($stmt->fetchAll());
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function getGrado($db, $id) {
    try {
        $stmt = $db->prepare("SELECT * FROM grados WHERE id_grado = ?");
        $stmt->execute([$id]);
        $grado = $stmt->fetch();
        
        if (!$grado) jsonError('Grado no encontrado', 404);
        jsonSuccess($grado);
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function createGrado($db) {
    $data = getRequestData();
    validateRequired($data, ['numero', 'nombre']);
    
    $numero = (int)$data['numero'];
    $nombre = sanitize($data['nombre']);
    
    if ($numero < 1 || $numero > 15) {
        jsonError('El número de grado debe estar entre 1 y 15');
    }
    
    try {
        // Verificar si ya existe ese número
        $stmt = $db->prepare("SELECT id_grado FROM grados WHERE numero = ?");
        $stmt->execute([$numero]);
        if ($stmt->fetch()) {
            jsonError('Ya existe un grado con ese número');
        }
        
        $stmt = $db->prepare("INSERT INTO grados (numero, nombre) VALUES (?, ?)");
        $stmt->execute([$numero, $nombre]);
        
        $newId = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM grados WHERE id_grado = ?");
        $stmt->execute([$newId]);
        
        jsonSuccess($stmt->fetch(), 'Grado registrado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

function updateGrado($db, $id) {
    $data = getRequestData();
    
    try {
        $stmt = $db->prepare("SELECT * FROM grados WHERE id_grado = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Grado no encontrado', 404);
        
        // Si solo se envía 'activo', actualizar solo ese campo
        if (isset($data['activo']) && count($data) == 1) {
            $activo = (int)$data['activo'];
            $stmt = $db->prepare("UPDATE grados SET activo = ? WHERE id_grado = ?");
            $stmt->execute([$activo, $id]);
            
            $mensaje = $activo == 1 ? 'Grado activado correctamente' : 'Grado desactivado correctamente';
            jsonSuccess(null, $mensaje);
        } else {
            // Actualización completa
            validateRequired($data, ['numero', 'nombre']);
            
            $numero = (int)$data['numero'];
            $nombre = sanitize($data['nombre']);
            
            if ($numero < 1 || $numero > 15) {
                jsonError('El número de grado debe estar entre 1 y 15');
            }
            
            // Verificar que no exista otro grado con ese número
            $stmt = $db->prepare("SELECT id_grado FROM grados WHERE numero = ? AND id_grado != ?");
            $stmt->execute([$numero, $id]);
            if ($stmt->fetch()) {
                jsonError('Ya existe un grado con ese número');
            }
            
            $stmt = $db->prepare("UPDATE grados SET numero = ?, nombre = ? WHERE id_grado = ?");
            $stmt->execute([$numero, $nombre, $id]);
            
            $stmt = $db->prepare("SELECT * FROM grados WHERE id_grado = ?");
            $stmt->execute([$id]);
            
            jsonSuccess($stmt->fetch(), 'Grado actualizado correctamente');
        }
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}
?>