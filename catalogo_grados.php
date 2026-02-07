<?php
$pageTitle = 'Catálogo de Grados';
$breadcrumb = [
    ['text' => 'Catálogos', 'url' => 'catalogos.php'],
    ['text' => 'Grados']
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
        background: var(--tertiary);
        color: white;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        border: 2px solid var(--tertiary);
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-block;
        cursor: pointer;
    }

    .btn-nuevo:hover {
        background: white;
        color: var(--tertiary);
        transform: translateY(-2px);
    }

    .info-box {
        background: #fffbf5;
        border: 2px solid var(--tertiary);
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

    .grado-numero {
        font-size: 2rem;
        font-weight: 800;
        color: var(--tertiary);
    }
</style>

<div class="catalogo-header">
    <h1 class="catalogo-title">Grados</h1>
    <button class="btn-nuevo" onclick="mostrarFormulario()">+ Nuevo Grado</button>
</div>

<div id="formularioContainer" style="display: none; margin-bottom: 2rem;">
    <div style="background: white; border: 2px solid var(--border); padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-weight: 600;" id="formTitle">Nuevo Grado</h3>
        <form id="formGrado">
            <input type="hidden" id="id_grado" value="">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Número de Grado:</label>
                <input type="number" id="numero" required min="1" max="15"
                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); font-family: 'Karla', sans-serif;">
                <small style="display: block; margin-top: 0.5rem; font-family: 'DM Mono', monospace; font-size: 0.75rem; opacity: 0.7;">
                    Ingrese un número del 1 al 15
                </small>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nombre:</label>
                <input type="text" id="nombre" required 
                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); font-family: 'Karla', sans-serif;">
                <small style="display: block; margin-top: 0.5rem; font-family: 'DM Mono', monospace; font-size: 0.75rem; opacity: 0.7;">
                    Ejemplo: "Primer Semestre", "1° Grado", etc.
                </small>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn-accion btn-editar">Guardar</button>
                <button type="button" class="btn-accion btn-toggle" onclick="ocultarFormulario()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="loading">Cargando grados...</div>

<table class="tabla-catalogo" id="tablaGrados" style="display: none;">
    <thead>
        <tr>
            <th>Grado</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="gradosBody">
    </tbody>
</table>

<script>
let editandoId = null;

async function cargarGrados() {
    try {
        const response = await fetch('/api/grados.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarGrados(data.data);
        }
    } catch (error) {
        document.getElementById('loading').textContent = 'Error al cargar grados: ' + error.message;
    }
}

function mostrarGrados(grados) {
    const tbody = document.getElementById('gradosBody');
    const tabla = document.getElementById('tablaGrados');
    const loading = document.getElementById('loading');
    
    tbody.innerHTML = '';
    
    if (grados.length === 0) {
        loading.innerHTML = '<div class="empty-state">No hay grados registrados</div>';
        return;
    }
    
    grados.forEach(grado => {
        const tr = document.createElement('tr');
        if (grado.activo == 0) {
            tr.classList.add('inactivo');
        }
        
        tr.innerHTML = `
            <td>
                <span class="grado-numero">${grado.numero}°</span>
            </td>
            <td><strong>${grado.nombre}</strong></td>
            <td>
                <span class="badge ${grado.activo == 1 ? 'badge-activo' : 'badge-inactivo'}">
                    ${grado.activo == 1 ? 'ACTIVO' : 'INACTIVO'}
                </span>
            </td>
            <td class="acciones">
                <button class="btn-accion btn-editar" onclick="editarGrado(${grado.id_grado}, ${grado.numero}, '${grado.nombre}')">
                    Editar
                </button>
                <button class="btn-accion ${grado.activo == 1 ? 'btn-toggle' : 'btn-activar'}" 
                        onclick="toggleGrado(${grado.id_grado}, ${grado.activo})">
                    ${grado.activo == 1 ? 'Desactivar' : 'Activar'}
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
    document.getElementById('formTitle').textContent = 'Nuevo Grado';
    document.getElementById('formGrado').reset();
    document.getElementById('id_grado').value = '';
    editandoId = null;
}

function ocultarFormulario() {
    document.getElementById('formularioContainer').style.display = 'none';
    document.getElementById('formGrado').reset();
    editandoId = null;
}

function editarGrado(id, numero, nombre) {
    editandoId = id;
    document.getElementById('formularioContainer').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Editar Grado';
    document.getElementById('id_grado').value = id;
    document.getElementById('numero').value = numero;
    document.getElementById('nombre').value = nombre;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function toggleGrado(id, estadoActual) {
    const accion = estadoActual == 1 ? 'desactivar' : 'activar';
    if (!confirm(`¿Está seguro de ${accion} este grado?`)) {
        return;
    }
    
    try {
        const response = await fetch(`/api/grados.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ activo: estadoActual == 1 ? 0 : 1 })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            cargarGrados();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al actualizar: ' + error.message);
    }
}

document.getElementById('formGrado').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const numero = parseInt(document.getElementById('numero').value);
    const nombre = document.getElementById('nombre').value.trim();
    
    if (numero < 1 || numero > 15) {
        alert('El número de grado debe estar entre 1 y 15');
        return;
    }
    
    const formData = {
        numero: numero,
        nombre: nombre
    };
    
    try {
        let url = '/api/grados.php';
        let method = 'POST';
        
        if (editandoId) {
            url = `/api/grados.php?id=${editandoId}`;
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
            cargarGrados();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al guardar: ' + error.message);
    }
});

cargarGrados();
</script>
