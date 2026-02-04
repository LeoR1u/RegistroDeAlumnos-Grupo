<?php
/**
 * API REST - CATÁLOGOS
 * GET /api/catalogos.php?tipo=carreras  → Listar carreras
 * GET /api/catalogos.php?tipo=turnos    → Listar turnos
 * GET /api/catalogos.php?tipo=grados    → Listar grados (1-9)
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

setApiHeaders();

$db = getDB();
$tipo = isset($_GET['tipo']) ? sanitize($_GET['tipo']) : null;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Método no permitido', 405);
}

if (!$tipo) {
    jsonError('Especifique tipo: carreras, turnos o grados');
}

switch ($tipo) {
    case 'carreras':
        $stmt = $db->query("SELECT id_carrera, nombre, abreviatura FROM carreras WHERE activo = 1 ORDER BY nombre");
        jsonSuccess($stmt->fetchAll());
        break;
        
    case 'turnos':
        $stmt = $db->query("SELECT id_turno, nombre, abreviatura FROM turnos WHERE activo = 1 ORDER BY id_turno");
        jsonSuccess($stmt->fetchAll());
        break;
        
    case 'grados':
        $grados = [];
        for ($i = 1; $i <= 9; $i++) {
            $grados[] = ['valor' => $i, 'nombre' => $i . '° Semestre'];
        }
        jsonSuccess($grados);
        break;
        
    default:
        jsonError('Tipo no válido. Use: carreras, turnos o grados');
}
?>
