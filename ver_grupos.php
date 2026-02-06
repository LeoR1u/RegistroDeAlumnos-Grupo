<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Gestión de Grupos';
include 'includes/header.php';
?>

<div class="container">
    <div class="header-section">
        <h2>Grupos Registrados</h2>
        <a href="formulario_grupo.php" class="btn-nuevo">+ Nuevo Grupo</a>
    </div>
    
    <div id="loading">Cargando grupos...</div>
    <div id="error" style="display:none; color: red;"></div>
    
    <table id="tablaGrupos" style="display:none;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Clave</th>
                <th>Carrera</th>
                <th>Grado</th>
                <th>Turno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="gruposBody">
        </tbody>
    </table>
</div>

<script>
// Cargar grupos desde la API
async function cargarGrupos() {
    try {
        const response = await fetch('/api/grupos.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarGrupos(data.data);
        } else {
            mostrarError(data.message);
        }
    } catch (error) {
        mostrarError('Error al cargar grupos: ' + error.message);
    }
}

function mostrarGrupos(grupos) {
    const tbody = document.getElementById('gruposBody');
    const tabla = document.getElementById('tablaGrupos');
    const loading = document.getElementById('loading');
    
    tbody.innerHTML = '';
    
    if (grupos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 20px;">No hay grupos registrados</td></tr>';
    } else {
        grupos.forEach(grupo => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${grupo.id_grupo}</td>
                <td><strong>${grupo.clave}</strong></td>
                <td>${grupo.carrera} (${grupo.carrera_abrev})</td>
                <td>${grupo.grado}</td>
                <td>${grupo.turno} (${grupo.turno_abrev})</td>
                <td class="acciones">
                    <a href="formulario_grupo.php?id=${grupo.id_grupo}" class="btn-editar">Editar</a>
                    <button onclick="eliminarGrupo(${grupo.id_grupo}, '${grupo.clave}')" class="btn-eliminar">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    loading.style.display = 'none';
    tabla.style.display = 'table';
}

async function eliminarGrupo(id, clave) {
    if (!confirm(`¿Está seguro de eliminar el grupo ${clave}?\n\nNOTA: Solo se puede eliminar si no tiene alumnos asignados.`)) {
        return;
    }
    
    try {
        const response = await fetch(`/api/grupos.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            cargarGrupos(); // Recargar la lista
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al eliminar: ' + error.message);
    }
}

function mostrarError(mensaje) {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('error').textContent = mensaje;
    document.getElementById('error').style.display = 'block';
}

// Cargar al iniciar
cargarGrupos();
</script>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.btn-nuevo {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
}

.btn-nuevo:hover {
    background-color: #218838;
}

#tablaGrupos {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#tablaGrupos th,
#tablaGrupos td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

#tablaGrupos th {
    background-color: #f8f9fa;
    font-weight: bold;
}

#tablaGrupos tr:hover {
    background-color: #f5f5f5;
}

.acciones {
    white-space: nowrap;
}

.btn-editar,
.btn-eliminar {
    padding: 6px 12px;
    margin-right: 5px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
}

.btn-editar {
    background-color: #007bff;
    color: white;
}

.btn-editar:hover {
    background-color: #0056b3;
}

.btn-eliminar {
    background-color: #dc3545;
    color: white;
}

.btn-eliminar:hover {
    background-color: #c82333;
}

#loading {
    text-align: center;
    padding: 40px;
    color: #666;
}
</style>

<?php include 'includes/footer.php'; ?>