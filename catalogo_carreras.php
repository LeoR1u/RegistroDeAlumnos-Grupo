<?php
$pageTitle = 'Catálogo de Carreras';
$breadcrumb = [
    ['text' => 'Catálogos', 'url' => 'catalogos.php'],
    ['text' => 'Carreras']
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
    }

    .btn-nuevo:hover {
        background: white;
        color: var(--accent);
        transform: translateY(-2px);
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

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text);
        opacity: 0.6;
    }
</style>

<div class="catalogo-header">
    <h1 class="catalogo-title">Carreras</h1>
    <button class="btn-nuevo" onclick="mostrarFormulario()">+ Nueva Carrera</button>
</div>

<div id="formularioContainer" style="display: none; margin-bottom: 2rem;">
    <div style="background: white; border: 2px solid var(--border); padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-weight: 600;" id="formTitle">Nueva Carrera</h3>
        <form id="formCarrera">
            <input type="hidden" id="id_carrera" value="">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nombre:</label>
                <input type="text" id="nombre" required 
                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); font-family: 'Karla', sans-serif;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Abreviatura:</label>
                <input type="text" id="abreviatura" required maxlength="10"
                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); font-family: 'Karla', sans-serif;">
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn-accion btn-editar">Guardar</button>
                <button type="button" class="btn-accion btn-toggle" onclick="ocultarFormulario()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="loading">Cargando carreras...</div>

<table class="tabla-catalogo" id="tablaCarreras" style="display: none;">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Abreviatura</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="carrerasBody">
    </tbody>
</table>

<script>
let editandoId = null;

async function cargarCarreras() {
    try {
        const response = await fetch('/api/carreras.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarCarreras(data.data);
        }
    } catch (error) {
        document.getElementById('loading').textContent = 'Error al cargar carreras: ' + error.message;
    }
}

function mostrarCarreras(carreras) {
    const tbody = document.getElementById('carrerasBody');
    const tabla = document.getElementById('tablaCarreras');
    const loading = document.getElementById('loading');
    
    tbody.innerHTML = '';
    
    if (carreras.length === 0) {
        loading.innerHTML = '<div class="empty-state">No hay carreras registradas</div>';
        return;
    }
    
    carreras.forEach(carrera => {
        const tr = document.createElement('tr');
        if (carrera.activo == 0) {
            tr.classList.add('inactivo');
        }
        
        tr.innerHTML = `
            <td><strong>${carrera.nombre}</strong></td>
            <td>${carrera.abreviatura}</td>
            <td>
                <span class="badge ${carrera.activo == 1 ? 'badge-activo' : 'badge-inactivo'}">
                    ${carrera.activo == 1 ? 'ACTIVO' : 'INACTIVO'}
                </span>
            </td>
            <td class="acciones">
                <button class="btn-accion btn-editar" onclick="editarCarrera(${carrera.id_carrera}, '${carrera.nombre}', '${carrera.abreviatura}')">
                    Editar
                </button>
                <button class="btn-accion ${carrera.activo == 1 ? 'btn-toggle' : 'btn-activar'}" 
                        onclick="toggleCarrera(${carrera.id_carrera}, ${carrera.activo})">
                    ${carrera.activo == 1 ? 'Desactivar' : 'Activar'}
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
    document.getElementById('formTitle').textContent = 'Nueva Carrera';
    document.getElementById('formCarrera').reset();
    document.getElementById('id_carrera').value = '';
    editandoId = null;
}

function ocultarFormulario() {
    document.getElementById('formularioContainer').style.display = 'none';
    document.getElementById('formCarrera').reset();
    editandoId = null;
}

function editarCarrera(id, nombre, abreviatura) {
    editandoId = id;
    document.getElementById('formularioContainer').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Editar Carrera';
    document.getElementById('id_carrera').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('abreviatura').value = abreviatura;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function toggleCarrera(id, estadoActual) {
    const accion = estadoActual == 1 ? 'desactivar' : 'activar';
    if (!confirm(`¿Está seguro de ${accion} esta carrera?`)) {
        return;
    }
    
    try {
        const response = await fetch(`/api/carreras.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ activo: estadoActual == 1 ? 0 : 1 })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            cargarCarreras();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al actualizar: ' + error.message);
    }
}

document.getElementById('formCarrera').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        nombre: document.getElementById('nombre').value.trim(),
        abreviatura: document.getElementById('abreviatura').value.trim().toUpperCase()
    };
    
    try {
        let url = '/api/carreras.php';
        let method = 'POST';
        
        if (editandoId) {
            url = `/api/carreras.php?id=${editandoId}`;
            method = 'PUT';
        }
        
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            ocultarFormulario();
            cargarCarreras();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al guardar: ' + error.message);
    }
});

cargarCarreras();
</script>
