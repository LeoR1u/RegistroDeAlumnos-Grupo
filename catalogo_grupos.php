<?php
$pageTitle = 'Catálogo de Grupos';
$breadcrumb = [
    ['text' => 'Catálogos', 'url' => 'catalogos.php'],
    ['text' => 'Grupos']
];
include 'includes/header.php';
?>

<style>
    .catalogo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .catalogo-title {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--border);
        text-transform: uppercase;
    }

    .btn-nuevo {
        background: var(--accent);
        color: white;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        border: 2px solid var(--accent);
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-block;
        cursor: pointer;
    }

    .btn-nuevo:hover {
        background: white;
        color: var(--accent);
        transform: translateY(-2px);
    }

    .info-box {
        background: #e7f3ff;
        border: 2px solid #007bff;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        font-family: 'DM Mono', monospace;
        font-size: 0.875rem;
    }

    .tabla-catalogo {
        width: 100%;
        background: white;
        border: 2px solid var(--border);
        border-collapse: collapse;
    }

    .tabla-catalogo thead {
        background: var(--bg);
        border-bottom: 2px solid var(--border);
    }

    .tabla-catalogo th {
        font-family: 'DM Mono', monospace;
        font-size: 0.875rem;
        font-weight: 500;
        text-align: left;
        padding: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .tabla-catalogo td {
        padding: 1rem;
        border-bottom: 1px solid #e5e3df;
    }

    .tabla-catalogo tbody tr {
        transition: all 0.2s ease;
    }

    .tabla-catalogo tbody tr:hover {
        background: #fffbf5;
    }

    .tabla-catalogo tbody tr.inactivo {
        background: #fff8f8;
        opacity: 0.6;
    }

    .tabla-catalogo tbody tr.inactivo:hover {
        background: #ffe5e5;
    }

    .badge {
        font-family: 'DM Mono', monospace;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 2px;
        font-weight: 500;
    }

    .badge-activo {
        background: #f8fff8;
        color: var(--secondary);
        border: 1px solid var(--secondary);
    }

    .badge-inactivo {
        background: #fff8f8;
        color: var(--accent);
        border: 1px solid var(--accent);
    }

    .clave-grupo {
        font-family: 'DM Mono', monospace;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--accent);
        letter-spacing: 0.05em;
    }

    .acciones {
        display: flex;
        gap: 0.5rem;
    }

    .btn-accion {
        padding: 0.5rem 1rem;
        border: 2px solid;
        background: white;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .btn-editar {
        border-color: var(--tertiary);
        color: var(--tertiary);
    }

    .btn-editar:hover {
        background: var(--tertiary);
        color: white;
    }

    .btn-toggle {
        border-color: var(--accent);
        color: var(--accent);
    }

    .btn-toggle:hover {
        background: var(--accent);
        color: white;
    }

    .btn-activar {
        border-color: var(--secondary);
        color: var(--secondary);
    }

    .btn-activar:hover {
        background: var(--secondary);
        color: white;
    }

    #loading {
        text-align: center;
        padding: 3rem;
        font-family: 'DM Mono', monospace;
        color: var(--text);
        opacity: 0.6;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-group select,
    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid var(--border);
        font-family: 'Karla', sans-serif;
        font-size: 1rem;
    }

    .form-group small {
        display: block;
        margin-top: 0.5rem;
        font-family: 'DM Mono', monospace;
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .clave-preview {
        background: #fffbf5;
        border: 2px solid var(--tertiary);
        padding: 1rem;
        margin-top: 1rem;
        text-align: center;
    }

    .clave-preview-texto {
        font-family: 'DM Mono', monospace;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--accent);
        letter-spacing: 0.1em;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text);
        opacity: 0.6;
    }
</style>

<div class="catalogo-header">
    <h1 class="catalogo-title">Grupos</h1>
    <button class="btn-nuevo" onclick="mostrarFormulario()">+ Nuevo Grupo</button>
</div>

<div id="formularioContainer" style="display: none; margin-bottom: 2rem;">
    <div style="background: white; border: 2px solid var(--border); padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-weight: 600;" id="formTitle">Nuevo Grupo</h3>
        <form id="formGrupo">
            <input type="hidden" id="id_grupo" value="">
            
            <div class="form-group">
                <label>Carrera: <span style="color: var(--accent);">*</span></label>
                <select id="id_carrera" required>
                    <option value="">Seleccione una carrera...</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Grado: <span style="color: var(--accent);">*</span></label>
                <select id="grado" required>
                    <option value="">Seleccione un grado...</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Turno: <span style="color: var(--accent);">*</span></label>
                <select id="id_turno" required>
                    <option value="">Seleccione un turno...</option>
                </select>
            </div>
            
            <div id="clavePreview" style="display: none;" class="clave-preview">
                <small style="opacity: 0.7;">Vista previa de la clave:</small>
                <div class="clave-preview-texto" id="claveGenerada">---</div>
            </div>
            
            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="submit" class="btn-accion btn-editar">Guardar</button>
                <button type="button" class="btn-accion btn-toggle" onclick="ocultarFormulario()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="loading">Cargando grupos...</div>

<table class="tabla-catalogo" id="tablaGrupos" style="display: none;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Clave</th>
            <th>Carrera</th>
            <th>Grado</th>
            <th>Turno</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="gruposBody">
    </tbody>
</table>

<script>
let editandoId = null;
let carreras = [];
let turnos = [];
let grados = [];

// Cargar catálogos
async function cargarCatalogos() {
    try {
        const [resCarreras, resTurnos, resGrados] = await Promise.all([
            fetch('/api/carreras.php'),
            fetch('/api/turnos.php'),
            fetch('/api/grados.php')
        ]);
        
        const dataCarreras = await resCarreras.json();
        const dataTurnos = await resTurnos.json();
        const dataGrados = await resGrados.json();
        
        if (dataCarreras.success) {
            carreras = dataCarreras.data.filter(c => c.activo == 1);
            const selectCarrera = document.getElementById('id_carrera');
            carreras.forEach(carrera => {
                const option = document.createElement('option');
                option.value = carrera.id_carrera;
                option.textContent = `${carrera.nombre} (${carrera.abreviatura})`;
                option.setAttribute('data-abrev', carrera.abreviatura);
                selectCarrera.appendChild(option);
            });
        }
        
        if (dataTurnos.success) {
            turnos = dataTurnos.data.filter(t => t.activo == 1);
            const selectTurno = document.getElementById('id_turno');
            turnos.forEach(turno => {
                const option = document.createElement('option');
                option.value = turno.id_turno;
                option.textContent = `${turno.nombre} (${turno.abreviatura})`;
                option.setAttribute('data-abrev', turno.abreviatura);
                selectTurno.appendChild(option);
            });
        }
        
        if (dataGrados.success) {
            grados = dataGrados.data.filter(g => g.activo == 1);
            const selectGrado = document.getElementById('grado');
            grados.forEach(grado => {
                const option = document.createElement('option');
                option.value = grado.numero;
                option.textContent = `${grado.numero}° - ${grado.nombre}`;
                selectGrado.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
}

// Cargar grupos
async function cargarGrupos() {
    try {
        const response = await fetch('/api/grupos.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarGrupos(data.data);
        }
    } catch (error) {
        document.getElementById('loading').textContent = 'Error al cargar grupos: ' + error.message;
    }
}

function mostrarGrupos(grupos) {
    const tbody = document.getElementById('gruposBody');
    const tabla = document.getElementById('tablaGrupos');
    const loading = document.getElementById('loading');
    
    tbody.innerHTML = '';
    
    if (grupos.length === 0) {
        loading.innerHTML = '<div class="empty-state">No hay grupos registrados</div>';
        return;
    }
    
    grupos.forEach(grupo => {
        const tr = document.createElement('tr');
        if (grupo.activo == 0) {
            tr.classList.add('inactivo');
        }
        
        tr.innerHTML = `
            <td>${grupo.id_grupo}</td>
            <td><span class="clave-grupo">${grupo.clave}</span></td>
            <td>${grupo.carrera} <small>(${grupo.carrera_abrev})</small></td>
            <td>${grupo.grado}°</td>
            <td>${grupo.turno} <small>(${grupo.turno_abrev})</small></td>
            <td>
                <span class="badge ${grupo.activo == 1 ? 'badge-activo' : 'badge-inactivo'}">
                    ${grupo.activo == 1 ? 'ACTIVO' : 'INACTIVO'}
                </span>
            </td>
            <td class="acciones">
                <button class="btn-accion btn-editar" onclick="editarGrupo(${grupo.id_grupo}, ${grupo.id_carrera}, ${grupo.grado}, ${grupo.id_turno})">
                    Editar
                </button>
                <button class="btn-accion ${grupo.activo == 1 ? 'btn-toggle' : 'btn-activar'}" 
                        onclick="toggleGrupo(${grupo.id_grupo}, ${grupo.activo}, '${grupo.clave}')">
                    ${grupo.activo == 1 ? 'Desactivar' : 'Activar'}
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    loading.style.display = 'none';
    tabla.style.display = 'table';
}

function mostrarFormulario() {
    document.getElementById('formularioContainer').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Nuevo Grupo';
    document.getElementById('formGrupo').reset();
    document.getElementById('id_grupo').value = '';
    document.getElementById('clavePreview').style.display = 'none';
    editandoId = null;
}

function ocultarFormulario() {
    document.getElementById('formularioContainer').style.display = 'none';
    document.getElementById('formGrupo').reset();
    document.getElementById('clavePreview').style.display = 'none';
    editandoId = null;
}

function editarGrupo(id, idCarrera, grado, idTurno) {
    editandoId = id;
    document.getElementById('formularioContainer').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Editar Grupo';
    document.getElementById('id_grupo').value = id;
    document.getElementById('id_carrera').value = idCarrera;
    document.getElementById('grado').value = grado;
    document.getElementById('id_turno').value = idTurno;
    
    // Generar preview de la clave
    generarClavePreview();
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function toggleGrupo(id, estadoActual, clave) {
    const accion = estadoActual == 1 ? 'desactivar' : 'activar';
    if (!confirm(`¿Está seguro de ${accion} el grupo ${clave}?`)) {
        return;
    }
    
    try {
        const response = await fetch(`/api/grupos.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            cargarGrupos();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al actualizar: ' + error.message);
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

document.getElementById('formGrupo').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        id_carrera: parseInt(document.getElementById('id_carrera').value),
        grado: parseInt(document.getElementById('grado').value),
        id_turno: parseInt(document.getElementById('id_turno').value)
    };
    
    // Validación
    if (!formData.id_carrera || !formData.grado || !formData.id_turno) {
        alert('Por favor complete todos los campos');
        return;
    }
    
    try {
        let url = '/api/grupos.php';
        let method = 'POST';
        
        if (editandoId) {
            url = `/api/grupos.php?id=${editandoId}`;
            method = 'PUT';
        }
        
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            const mensaje = editandoId ? 'Grupo actualizado correctamente' : 'Grupo creado correctamente';
            alert(mensaje + ' - Clave: ' + data.data.clave);
            ocultarFormulario();
            cargarGrupos();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al guardar: ' + error.message);
    }
});

// Inicializar
cargarCatalogos();
cargarGrupos();
</script>
