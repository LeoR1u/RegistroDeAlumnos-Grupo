<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Detectar si es edición o creación
$isEdit = isset($_GET['id']) && !empty($_GET['id']);
$id = $isEdit ? (int)$_GET['id'] : 0;

$pageTitle = $isEdit ? 'Editar Alumno' : 'Registrar Alumno';
$breadcrumb = [
    ['text' => 'Alumnos', 'url' => 'ver_alumnos.php'],
    ['text' => $isEdit ? 'Editar' : 'Nuevo']
];
include 'includes/header.php';
?>

<style>
    .form-container {
        max-width: 700px;
        margin: 0 auto;
    }

    .form-title {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--border);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .form-subtitle {
        font-family: 'DM Mono', monospace;
        font-size: 0.875rem;
        color: var(--secondary);
        margin-bottom: 2rem;
        letter-spacing: 0.05em;
    }

    .form-card {
        background: white;
        border: 2px solid var(--border);
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text);
        font-size: 0.95rem;
    }

    .required {
        color: var(--accent);
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.875rem;
        border: 2px solid var(--border);
        font-family: 'Karla', sans-serif;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--accent);
        background: #fffbf5;
    }

    .form-group small {
        display: block;
        margin-top: 0.5rem;
        font-family: 'DM Mono', monospace;
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .form-actions {
        margin-top: 2rem;
        display: flex;
        gap: 0.75rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e3df;
    }

    .btn-accion {
        padding: 0.875rem 1.75rem;
        border: 2px solid;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-primary {
        background: var(--secondary);
        border-color: var(--secondary);
        color: white;
    }

    .btn-primary:hover {
        background: white;
        color: var(--secondary);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: white;
        border-color: var(--border);
        color: var(--text);
    }

    .btn-secondary:hover {
        background: var(--bg);
    }

    #loading {
        text-align: center;
        padding: 3rem;
        font-family: 'DM Mono', monospace;
        color: var(--text);
        opacity: 0.6;
    }

    .mensaje {
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        border: 2px solid;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .mensaje::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
    }

    .mensaje-success {
        background: #f8fff8;
        border-color: var(--secondary);
        color: var(--secondary);
    }

    .mensaje-success::before {
        background: var(--secondary);
    }

    .mensaje-error {
        background: #fff8f8;
        border-color: var(--accent);
        color: var(--accent);
    }

    .mensaje-error::before {
        background: var(--accent);
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-accion {
            width: 100%;
        }
    }
</style>

<div class="form-container">
    <h1 class="form-title"><?= $isEdit ? 'Editar Alumno' : 'Nuevo Alumno' ?></h1>
    <div class="form-subtitle">GESTIÓN DE ALUMNOS</div>
    
    <div id="loading" style="<?= $isEdit ? '' : 'display:none;' ?>">
        Cargando datos del alumno...
    </div>
    
    <div id="mensaje" style="display:none;"></div>
    
    <div class="form-card" style="<?= $isEdit ? 'display:none;' : '' ?>">
        <form id="formAlumno">
            <?php if ($isEdit): ?>
            <input type="hidden" id="id_alumno" value="<?= $id ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Nombre(s): <span class="required">*</span></label>
                <input type="text" id="nombre" name="nombre" required 
                       placeholder="Ingrese el nombre del alumno">
            </div>
            
            <div class="form-group">
                <label>Apellido Paterno: <span class="required">*</span></label>
                <input type="text" id="apellido_paterno" name="apellido_paterno" required
                       placeholder="Ingrese el apellido paterno">
            </div>
            
            <div class="form-group">
                <label>Apellido Materno:</label>
                <input type="text" id="apellido_materno" name="apellido_materno"
                       placeholder="Ingrese el apellido materno (opcional)">
            </div>
            
            <div class="form-group">
                <label>Grupo: <span class="required">*</span></label>
                <select id="id_grupo" name="id_grupo" required>
                    <option value="">Seleccione un grupo...</option>
                </select>
                <small>Seleccione el grupo al que pertenecerá el alumno</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-accion btn-primary">
                    <?= $isEdit ? '✓ Actualizar Alumno' : '+ Registrar Alumno' ?>
                </button>
                <a href="ver_alumnos.php" class="btn-accion btn-secondary">← Cancelar</a>
            </div>
        </form>
    </div>
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
            
            // Filtrar solo grupos activos
            const gruposActivos = data.data.filter(g => g.activo == 1);
            
            gruposActivos.forEach(grupo => {
                const option = document.createElement('option');
                option.value = grupo.id_grupo;
                option.textContent = `${grupo.clave} - ${grupo.carrera} (${grupo.turno})`;
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
            document.querySelector('.form-card').style.display = 'block';
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
    mensaje.className = 'mensaje mensaje-' + tipo;
    mensaje.style.display = 'block';
    
    // Scroll al mensaje
    mensaje.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Inicializar
cargarGrupos();
</script>