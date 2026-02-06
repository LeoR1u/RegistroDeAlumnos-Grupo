<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Detectar si es edición o creación
$isEdit = isset($_GET['id']) && !empty($_GET['id']);
$id = $isEdit ? (int)$_GET['id'] : 0;

$pageTitle = $isEdit ? 'Editar Alumno' : 'Registrar Alumno';
include 'includes/header.php';
?>

<div class="container">
    <h2><?= $isEdit ? 'Editar Alumno' : 'Registrar Nuevo Alumno' ?></h2>
    
    <div id="loading" style="<?= $isEdit ? '' : 'display:none;' ?>">
        Cargando datos...
    </div>
    
    <div id="mensaje" style="display:none; padding: 10px; margin: 10px 0;"></div>
    
    <form id="formAlumno" style="<?= $isEdit ? 'display:none;' : '' ?>">
        <?php if ($isEdit): ?>
        <input type="hidden" id="id_alumno" value="<?= $id ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Nombre: <span class="required">*</span></label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        
        <div class="form-group">
            <label>Apellido Paterno: <span class="required">*</span></label>
            <input type="text" id="apellido_paterno" name="apellido_paterno" required>
        </div>
        
        <div class="form-group">
            <label>Apellido Materno:</label>
            <input type="text" id="apellido_materno" name="apellido_materno">
        </div>
        
        <div class="form-group">
            <label>Grupo: <span class="required">*</span></label>
            <select id="id_grupo" name="id_grupo" required>
                <option value="">Seleccione un grupo...</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <?= $isEdit ? 'Actualizar Alumno' : 'Registrar Alumno' ?>
            </button>
            <a href="ver_alumnos.php" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
const idAlumno = <?= $id ?>;

// Cargar grupos al iniciar
async function cargarGrupos() {
    try {
        const response = await fetch('/api/grupos.php');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('id_grupo');
            
            data.data.forEach(grupo => {
                const option = document.createElement('option');
                option.value = grupo.id_grupo;
                option.textContent = `${grupo.clave} - ${grupo.carrera} ${grupo.turno}`;
                select.appendChild(option);
            });
            
            // Si es edición, cargar datos del alumno
            if (isEdit) {
                await cargarAlumno();
            }
        }
    } catch (error) {
        console.error('Error al cargar grupos:', error);
        mostrarMensaje('Error al cargar grupos: ' + error.message, 'error');
    }
}

// Cargar datos del alumno (solo en modo edición)
async function cargarAlumno() {
    try {
        const response = await fetch(`/api/alumnos.php?id=${idAlumno}`);
        const data = await response.json();
        
        if (data.success) {
            const alumno = data.data;
            
            // Llenar el formulario con los datos
            document.getElementById('nombre').value = alumno.nombre;
            document.getElementById('apellido_paterno').value = alumno.apellido_paterno;
            document.getElementById('apellido_materno').value = alumno.apellido_materno || '';
            document.getElementById('id_grupo').value = alumno.id_grupo;
            
            // Mostrar formulario
            document.getElementById('loading').style.display = 'none';
            document.getElementById('formAlumno').style.display = 'block';
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        alert('Error al cargar alumno: ' + error.message);
        window.location.href = 'ver_alumnos.php';
    }
}

// Manejar envío del formulario
document.getElementById('formAlumno').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        nombre: document.getElementById('nombre').value.trim(),
        apellido_paterno: document.getElementById('apellido_paterno').value.trim(),
        apellido_materno: document.getElementById('apellido_materno').value.trim(),
        id_grupo: document.getElementById('id_grupo').value
    };
    
    // Validación básica
    if (!formData.nombre || !formData.apellido_paterno || !formData.id_grupo) {
        mostrarMensaje('Por favor complete los campos obligatorios', 'error');
        return;
    }
    
    try {
        let url = '/api/alumnos.php';
        let method = 'POST';
        
        // Si es edición, cambiar URL y método
        if (isEdit) {
            url = `/api/alumnos.php?id=${idAlumno}`;
            method = 'PUT';
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            const mensaje = isEdit ? 'Alumno actualizado correctamente' : 'Alumno registrado correctamente';
            mostrarMensaje(mensaje, 'success');
            
            // Limpiar formulario si es nuevo registro
            if (!isEdit) {
                document.getElementById('formAlumno').reset();
            }
            
            // Redirigir después de 1.5 segundos
            setTimeout(() => {
                window.location.href = 'ver_alumnos.php';
            }, 1500);
        } else {
            mostrarMensaje('Error: ' + data.message, 'error');
        }
    } catch (error) {
        mostrarMensaje('Error al guardar: ' + error.message, 'error');
    }
});

function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById('mensaje');
    mensaje.textContent = texto;
    mensaje.style.display = 'block';
    mensaje.style.backgroundColor = tipo === 'success' ? '#d4edda' : '#f8d7da';
    mensaje.style.color = tipo === 'success' ? '#155724' : '#721c24';
    mensaje.style.border = `1px solid ${tipo === 'success' ? '#c3e6cb' : '#f5c6cb'}`;
    mensaje.style.borderRadius = '4px';
}

// Inicializar
cargarGrupos();
</script>

<style>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

h2 {
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

.required {
    color: #dc3545;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.form-actions {
    margin-top: 25px;
    display: flex;
    gap: 10px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    display: inline-block;
    padding: 12px 24px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
}

.btn-secondary:hover {
    background-color: #545b62;
}

#loading {
    text-align: center;
    padding: 40px;
    color: #666;
    font-size: 16px;
}
</style>

