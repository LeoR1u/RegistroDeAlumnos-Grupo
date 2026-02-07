<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Detectar si es edición o creación
$isEdit = isset($_GET['id']) && !empty($_GET['id']);
$id = $isEdit ? (int)$_GET['id'] : 0;

$pageTitle = $isEdit ? 'Editar Grupo' : 'Crear Grupo';
include 'includes/header.php';
?>

<div class="container">
    <h2><?= $isEdit ? 'Editar Grupo' : 'Crear Nuevo Grupo' ?></h2>
    
    <div id="loading" style="<?= $isEdit ? '' : 'display:none;' ?>">
        Cargando datos...
    </div>
    
    <div id="mensaje" style="display:none; padding: 10px; margin: 10px 0;"></div>
    
    <form id="formGrupo" style="<?= $isEdit ? 'display:none;' : '' ?>">
        <?php if ($isEdit): ?>
        <input type="hidden" id="id_grupo" value="<?= $id ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Carrera: <span class="required">*</span></label>
            <select id="id_carrera" name="id_carrera" required>
                <option value="">Seleccione una carrera...</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Grado: <span class="required">*</span></label>
            <select id="grado" name="grado" required>
                <option value="">Seleccione un grado...</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Turno: <span class="required">*</span></label>
            <select id="id_turno" name="id_turno" required>
                <option value="">Seleccione un turno...</option>
            </select>
        </div>
        
        <div class="form-group info-box" id="clavePreview" style="display:none;">
            <label>Clave del grupo:</label>
            <div class="clave-generada" id="claveGenerada">---</div>
            <small>La clave se genera automáticamente</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <?= $isEdit ? 'Actualizar Grupo' : 'Crear Grupo' ?>
            </button>
            <a href="ver_grupos.php" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
const idGrupo = <?= $id ?>;

// Cargar catálogos (carreras, turnos, grados)
async function cargarCatalogos() {
    try {
        // Cargar carreras
        const resCarreras = await fetch('/api/catalogos.php?tipo=carreras');
        const dataCarreras = await resCarreras.json();
        
        if (dataCarreras.success) {
            const selectCarrera = document.getElementById('id_carrera');
            dataCarreras.data.forEach(carrera => {
                const option = document.createElement('option');
                option.value = carrera.id_carrera;
                option.textContent = `${carrera.nombre} (${carrera.abreviatura})`;
                option.setAttribute('data-abrev', carrera.abreviatura);
                selectCarrera.appendChild(option);
            });
        }
        
        // Cargar grados
        const resGrados = await fetch('/api/catalogos.php?tipo=grados');
        const dataGrados = await resGrados.json();
        
        if (dataGrados.success) {
            const selectGrado = document.getElementById('grado');
            dataGrados.data.forEach(grado => {
                const option = document.createElement('option');
                option.value = grado.valor;
                option.textContent = grado.nombre;
                selectGrado.appendChild(option);
            });
        }
        
        // Cargar turnos
        const resTurnos = await fetch('/api/catalogos.php?tipo=turnos');
        const dataTurnos = await resTurnos.json();
        
        if (dataTurnos.success) {
            const selectTurno = document.getElementById('id_turno');
            dataTurnos.data.forEach(turno => {
                const option = document.createElement('option');
                option.value = turno.id_turno;
                option.textContent = `${turno.nombre} (${turno.abreviatura})`;
                option.setAttribute('data-abrev', turno.abreviatura);
                selectTurno.appendChild(option);
            });
        }
        
        // Si es edición, cargar datos del grupo
        if (isEdit) {
            await cargarGrupo();
        } else {
            document.getElementById('formGrupo').style.display = 'block';
        }
        
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
        mostrarMensaje('Error al cargar datos: ' + error.message, 'error');
    }
}

// Cargar datos del grupo (solo en modo edición)
async function cargarGrupo() {
    try {
        const response = await fetch(`/api/grupos.php?id=${idGrupo}`);
        const data = await response.json();
        
        if (data.success) {
            const grupo = data.data;
            
            // Llenar el formulario con los datos
            document.getElementById('id_carrera').value = grupo.id_carrera;
            document.getElementById('grado').value = grupo.grado;
            document.getElementById('id_turno').value = grupo.id_turno;
            
            // Mostrar clave actual
            document.getElementById('claveGenerada').textContent = grupo.clave;
            document.getElementById('clavePreview').style.display = 'block';
            
            // Mostrar formulario
            document.getElementById('loading').style.display = 'none';
            document.getElementById('formGrupo').style.display = 'block';
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        alert('Error al cargar grupo: ' + error.message);
        window.location.href = 'ver_grupos.php';
    }
}

// Preview de clave al cambiar campos
document.getElementById('id_carrera').addEventListener('change', generarClavePreview);
document.getElementById('grado').addEventListener('change', generarClavePreview);
document.getElementById('id_turno').addEventListener('change', generarClavePreview);

async function generarClavePreview() {
    const carreraId = document.getElementById('id_carrera').value;
    const grado = document.getElementById('grado').value;
    const turnoId = document.getElementById('id_turno').value;
    
    if (!carreraId || !grado || !turnoId) {
        document.getElementById('clavePreview').style.display = 'none';
        return;
    }
    
    try {
        const response = await fetch(
            `/api/grupos.php?generar_clave=1&carrera=${carreraId}&turno=${turnoId}&grado=${grado}`
        );
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('claveGenerada').textContent = data.data.clave;
            document.getElementById('clavePreview').style.display = 'block';
        }
    } catch (error) {
        console.error('Error al generar preview:', error);
    }
}

// Manejar envío del formulario
document.getElementById('formGrupo').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        id_carrera: parseInt(document.getElementById('id_carrera').value),
        grado: parseInt(document.getElementById('grado').value),
        id_turno: parseInt(document.getElementById('id_turno').value)
    };
    
    // Validación
    if (!formData.id_carrera || !formData.grado || !formData.id_turno) {
        mostrarMensaje('Por favor complete todos los campos', 'error');
        return;
    }
    
    if (formData.grado < 1 || formData.grado > 9) {
        mostrarMensaje('El grado debe estar entre 1 y 9', 'error');
        return;
    }
    
    try {
        let url = '/api/grupos.php';
        let method = 'POST';
        
        // Si es edición, cambiar URL y método
        if (isEdit) {
            url = `/api/grupos.php?id=${idGrupo}`;
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
            const mensaje = isEdit ? 'Grupo actualizado correctamente' : 'Grupo creado correctamente';
            mostrarMensaje(mensaje + ' - Clave: ' + data.data.clave, 'success');
            
            // Limpiar formulario si es nuevo registro
            if (!isEdit) {
                document.getElementById('formGrupo').reset();
                document.getElementById('clavePreview').style.display = 'none';
            }
            
            // Redirigir después de 2 segundos
            setTimeout(() => {
                window.location.href = 'ver_grupos.php';
            }, 2000);
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
cargarCatalogos();
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

.form-group small {
    display: block;
    margin-top: 5px;
    color: #6c757d;
    font-size: 12px;
}

.info-box {
    background-color: #e7f3ff;
    padding: 15px;
    border-radius: 4px;
    border: 1px solid #b3d7ff;
}

.clave-generada {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
    padding: 10px;
    background-color: white;
    border-radius: 4px;
    text-align: center;
    margin: 10px 0;
    letter-spacing: 2px;
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
