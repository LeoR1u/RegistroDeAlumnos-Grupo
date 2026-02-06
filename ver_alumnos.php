<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Listado de Alumnos';
include 'includes/header.php';
?>

<div class="container">
    <div class="header-section">
        <h2>Listado de Alumnos</h2>
        <a href="formulario_alumno.php" class="btn-nuevo">+ Nuevo Alumno</a>
    </div>
    
    <div id="loading">Cargando alumnos...</div>
    <div id="error" style="display:none; color: red;"></div>
    
    <table id="tablaAlumnos" style="display:none;">
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Grupo</th>
                <th>Carrera</th>
                <th>Turno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="alumnosBody">
        </tbody>
    </table>
</div>

<script>
// Cargar alumnos desde la API
async function cargarAlumnos() {
    try {
        const response = await fetch('/api/alumnos.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarAlumnos(data.data);
        } else {
            mostrarError(data.message);
        }
    } catch (error) {
        mostrarError('Error al cargar alumnos: ' + error.message);
    }
}

function mostrarAlumnos(alumnos) {
    const tbody = document.getElementById('alumnosBody');
    const tabla = document.getElementById('tablaAlumnos');
    const loading = document.getElementById('loading');
    
    tbody.innerHTML = '';
    
    if (alumnos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 20px;">No hay alumnos registrados</td></tr>';
    } else {
        alumnos.forEach(alumno => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${alumno.nombre_completo}</td>
                <td>${alumno.grupo}</td>
                <td>${alumno.carrera}</td>
                <td>${alumno.turno}</td>
                <td class="acciones">
                    <a href="formulario_alumno.php?id=${alumno.id_alumno}" class="btn-editar">Editar</a>
                    <button onclick="eliminarAlumno(${alumno.id_alumno}, '${alumno.nombre_completo}')" class="btn-eliminar">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    loading.style.display = 'none';
    tabla.style.display = 'table';
}

async function eliminarAlumno(id, nombre) {
    if (!confirm(`¿Está seguro de eliminar al alumno ${nombre}?`)) {
        return;
    }
    
    try {
        const response = await fetch(`/api/alumnos.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            cargarAlumnos(); // Recargar la lista
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
cargarAlumnos();
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

#tablaAlumnos {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#tablaAlumnos th,
#tablaAlumnos td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

#tablaAlumnos th {
    background-color: #f8f9fa;
    font-weight: bold;
}

#tablaAlumnos tr:hover {
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

