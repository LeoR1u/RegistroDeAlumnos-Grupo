<?php
/**
 * API REST - GRUPOS
 * GET    /api/grupos.php                                    → Listar todos
 * GET    /api/grupos.php?id=1                               → Obtener uno
 * POST   /api/grupos.php                                    → Crear
 * PUT    /api/grupos.php?id=1                               → Actualizar
 * DELETE /api/grupos.php?id=1                               → Eliminar
 * GET    /api/grupos.php?generar_clave=1&carrera=1&turno=1&grado=5 → Generar clave
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

setApiHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Generar clave
if ($method === 'GET' && isset($_GET['generar_clave'])) {
    generarClave($db);
}

switch ($method) {
    case 'GET':
        $id ? getGrupo($db, $id) : getGrupos($db);
        break;
    case 'POST':
        createGrupo($db);
        break;
    case 'PUT':
        if (!$id) jsonError('ID requerido');
        updateGrupo($db, $id);
        break;
    case 'DELETE':
        if (!$id) jsonError('ID requerido');
        deleteGrupo($db, $id);
        break;
    default:
        jsonError('Método no permitido', 405);
}

// Obtener todos los grupos
function getGrupos($db) {
    try {
        $stmt = $db->query("
            SELECT * FROM vista_grupos
            WHERE activo = 1
            ORDER BY carrera_abrev, grado, turno_abrev
        ");
        jsonSuccess($stmt->fetchAll());
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Obtener un grupo
function getGrupo($db, $id) {
    try {
        $stmt = $db->prepare("SELECT * FROM vista_grupos WHERE id_grupo = ? AND activo = 1");
        $stmt->execute([$id]);
        $grupo = $stmt->fetch();
        
        if (!$grupo) jsonError('Grupo no encontrado', 404);
        jsonSuccess($grupo);
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Generar clave de grupo automáticamente
function generarClave($db) {
    $idCarrera = isset($_GET['carrera']) ? (int)$_GET['carrera'] : null;
    $idTurno = isset($_GET['turno']) ? (int)$_GET['turno'] : null;
    $grado = isset($_GET['grado']) ? (int)$_GET['grado'] : null;
    
    if (!$idCarrera || !$idTurno || !$grado) {
        jsonError('Se requiere carrera, turno y grado');
    }
    
    try {
        $stmt = $db->prepare("SELECT abreviatura FROM carreras WHERE id_carrera = ?");
        $stmt->execute([$idCarrera]);
        $carrera = $stmt->fetch();
        
        $stmt = $db->prepare("SELECT abreviatura FROM turnos WHERE id_turno = ?");
        $stmt->execute([$idTurno]);
        $turno = $stmt->fetch();
        
        if (!$carrera || !$turno) jsonError('Carrera o turno no válido');
        
        // Generar clave única
        $claveBase = $carrera['abreviatura'] . $grado . '0';
        $contador = 1;
        
        do {
            $clave = $claveBase . $contador . '-' . $turno['abreviatura'];
            $stmt = $db->prepare("SELECT id_grupo FROM grupos WHERE clave = ?");
            $stmt->execute([$clave]);
            $existe = $stmt->fetch();
            $contador++;
        } while ($existe);
        
        jsonSuccess(['clave' => $clave]);
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Crear grupo
function createGrupo($db) {
    $data = getRequestData();
    validateRequired($data, ['id_carrera', 'id_turno', 'grado']);
    
    $idCarrera = (int)$data['id_carrera'];
    $idTurno = (int)$data['id_turno'];
    $grado = (int)$data['grado'];
    
    if ($grado < 1 || $grado > 9) jsonError('El grado debe estar entre 1 y 9');
    
    try {
        // Obtener abreviaturas
        $stmt = $db->prepare("SELECT abreviatura FROM carreras WHERE id_carrera = ?");
        $stmt->execute([$idCarrera]);
        $carrera = $stmt->fetch();
        
        $stmt = $db->prepare("SELECT abreviatura FROM turnos WHERE id_turno = ?");
        $stmt->execute([$idTurno]);
        $turno = $stmt->fetch();
        
        if (!$carrera || !$turno) jsonError('Carrera o turno no válido');
        
        // Generar clave única
        $claveBase = $carrera['abreviatura'] . $grado . '0';
        $contador = 1;
        
        do {
            $clave = $claveBase . $contador . '-' . $turno['abreviatura'];
            $stmt = $db->prepare("SELECT id_grupo FROM grupos WHERE clave = ?");
            $stmt->execute([$clave]);
            $existe = $stmt->fetch();
            $contador++;
        } while ($existe);
        
        // Insertar
        $stmt = $db->prepare("
            INSERT INTO grupos (id_carrera, id_turno, grado, clave)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$idCarrera, $idTurno, $grado, $clave]);
        
        $newId = $db->lastInsertId();
        
        $stmt = $db->prepare("SELECT * FROM vista_grupos WHERE id_grupo = ?");
        $stmt->execute([$newId]);
        
        jsonSuccess($stmt->fetch(), 'Grupo registrado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Actualizar grupo
function updateGrupo($db, $id) {
    $data = getRequestData();
    validateRequired($data, ['id_carrera', 'id_turno', 'grado']);
    
    $idCarrera = (int)$data['id_carrera'];
    $idTurno = (int)$data['id_turno'];
    $grado = (int)$data['grado'];
    
    if ($grado < 1 || $grado > 9) jsonError('El grado debe estar entre 1 y 9');
    
    try {
        // Verificar que existe
        $stmt = $db->prepare("SELECT * FROM grupos WHERE id_grupo = ? AND activo = 1");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Grupo no encontrado', 404);
        
        // Regenerar clave
        $stmt = $db->prepare("SELECT abreviatura FROM carreras WHERE id_carrera = ?");
        $stmt->execute([$idCarrera]);
        $carrera = $stmt->fetch();
        
        $stmt = $db->prepare("SELECT abreviatura FROM turnos WHERE id_turno = ?");
        $stmt->execute([$idTurno]);
        $turno = $stmt->fetch();
        
        $claveBase = $carrera['abreviatura'] . $grado . '0';
        $contador = 1;
        
        do {
            $clave = $claveBase . $contador . '-' . $turno['abreviatura'];
            $stmt = $db->prepare("SELECT id_grupo FROM grupos WHERE clave = ? AND id_grupo != ?");
            $stmt->execute([$clave, $id]);
            $existe = $stmt->fetch();
            $contador++;
        } while ($existe);
        
        // Actualizar
        $stmt = $db->prepare("
            UPDATE grupos SET id_carrera = ?, id_turno = ?, grado = ?, clave = ?
            WHERE id_grupo = ?
        ");
        $stmt->execute([$idCarrera, $idTurno, $grado, $clave, $id]);
        
        $stmt = $db->prepare("SELECT * FROM vista_grupos WHERE id_grupo = ?");
        $stmt->execute([$id]);
        
        jsonSuccess($stmt->fetch(), 'Grupo actualizado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}

// Eliminar grupo
function deleteGrupo($db, $id) {
    try {
        $stmt = $db->prepare("SELECT id_grupo FROM grupos WHERE id_grupo = ? AND activo = 1");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonError('Grupo no encontrado', 404);
        
        // Verificar que no tenga alumnos
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM alumnos WHERE id_grupo = ? AND activo = 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            jsonError('No se puede eliminar: el grupo tiene alumnos asignados');
        }
        
        $stmt = $db->prepare("UPDATE grupos SET activo = 0 WHERE id_grupo = ?");
        $stmt->execute([$id]);
        
        jsonSuccess(null, 'Grupo eliminado correctamente');
    } catch (PDOException $e) {
        jsonError('Error: ' . $e->getMessage(), 500);
    }
}
?>
