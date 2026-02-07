<?php
$pageTitle = 'Listado de Alumnos';
$breadcrumb = [
    ['text' => 'Alumnos']
];
include 'includes/header.php';
?>

<style>
    .catalogo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .catalogo-title {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--border);
        text-transform: uppercase;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .btn-nuevo {
        background: var(--secondary);
        color: white;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        border: 2px solid var(--secondary);
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-block;
        cursor: pointer;
    }

    .btn-nuevo:hover {
        background: white;
        color: var(--secondary);
        transform: translateY(-2px);
    }

    .filtro-estado {
        display: flex;
        gap: 0.5rem;
        background: white;
        border: 2px solid var(--border);
        padding: 0.25rem;
    }

    .filtro-btn {
        padding: 0.5rem 1rem;
        background: transparent;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        font-family: 'DM Mono', monospace;
    }

    .filtro-btn.active {
        background: var(--accent);
        color: white;
    }

    .filtro-btn:hover:not(.active) {
        background: var(--bg);
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

    .alumno-nombre {
        font-weight: 600;
        color: var(--text);
    }

    .badge {
        font-family: 'DM Mono', monospace;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 2px;
        font-weight: 500;
    }

    .badge-grupo {
        background: #e7f3ff;
        color: #007bff;
        border: 1px solid #007bff;
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
        text-decoration: none;
        display: inline-block;
    }

    .btn-editar {
        border-color: var(--tertiary);
        color: var(--tertiary);
    }

    .btn-editar:hover {
        background: var(--tertiary);
        color: white;
    }

    .btn-desactivar {
        border-color: var(--accent);
        color: var(--accent);
    }

    .btn-desactivar:hover {
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

    .stats-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .stat-card {
        background: white;
        border: 2px solid var(--border);
        padding: 1rem 1.5rem;
        flex: 1;
        min-width: 180px;
    }

    .stat-label {
        font-family: 'DM Mono', monospace;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.7;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--accent);
    }

    @media (max-width: 768px) {
        .catalogo-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .filtro-estado {
            width: 100%;
        }

        .filtro-btn {
            flex: 1;
        }

        .tabla-catalogo {
            font-size: 0.875rem;
        }

        .tabla-catalogo th,
        .tabla-catalogo td {
            padding: 0.75rem 0.5rem;
        }

        .acciones {
            flex-direction: column;
        }

        .stat-card {
            min-width: 100%;
        }
    }
</style>

<div class="catalogo-header">
    <h1 class="catalogo-title">Alumnos</h1>
    <div class="header-actions">
        <div class="filtro-estado">
            <button class="filtro-btn active" data-filtro="activos" onclick="cambiarFiltro('activos')">
                ACTIVOS
            </button>
            <button class="filtro-btn" data-filtro="inactivos" onclick="cambiarFiltro('inactivos')">
                INACTIVOS
            </button>
            <button class="filtro-btn" data-filtro="todos" onclick="cambiarFiltro('todos')">
                TODOS
            </button>
        </div>
        <a href="formulario_alumno.php" class="btn-nuevo">+ Nuevo Alumno</a>
    </div>
</div>

<div class="stats-bar" id="statsBar" style="display: none;">
    <div class="stat-card">
        <div class="stat-label">Total Activos</div>
        <div class="stat-value" id="totalActivos">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Inactivos</div>
        <div class="stat-value" id="totalInactivos" style="color: var(--accent);">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Grupos Únicos</div>
        <div class="stat-value" id="totalGrupos" style="color: var(--secondary);">0</div>
    </div>
</div>

<div id="loading">Cargando alumnos...</div>
<div id="error" style="display:none; padding: 1rem; background: #fff8f8; border: 2px solid var(--accent); color: var(--accent); margin-bottom: 1rem;"></div>

<table class="tabla-catalogo" id="tablaAlumnos" style="display:none;">
    <thead>
        <tr>
            <th>Nombre Completo</th>
            <th>Grupo</th>
            <th>Carrera</th>
            <th>Turno</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="alumnosBody">
    </tbody>
</table>

<script>
let todosLosAlumnos = [];
let filtroActual = 'activos';

// Cargar alumnos desde la API
async function cargarAlumnos() {
    try {
        const response = await fetch('/api/alumnos.php?incluir_inactivos=1');
        const data = await response.json();
        
        if (data.success) {
            todosLosAlumnos = data.data;
            actualizarEstadisticas(data.data);
            aplicarFiltro();
        } else {
            mostrarError(data.message);
        }
    } catch (error) {
        mostrarError('Error al cargar alumnos: ' + error.message);
    }
}

function cambiarFiltro(filtro) {
    filtroActual = filtro;
    
    // Actualizar botones activos
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-filtro="${filtro}"]`).classList.add('active');
    
    aplicarFiltro();
}

function aplicarFiltro() {
    let alumnosFiltrados = [];
    
    switch(filtroActual) {
        case 'activos':
            alumnosFiltrados = todosLosAlumnos.filter(a => a.activo == 1);
            break;
        case 'inactivos':
            alumnosFiltrados = todosLosAlumnos.filter(a => a.activo == 0);
            break;
        case 'todos':
            alumnosFiltrados = todosLosAlumnos;
            break;
    }
    
    mostrarAlumnos(alumnosFiltrados);
}

function mostrarAlumnos(alumnos) {
    const tbody = document.getElementById('alumnosBody');
    const tabla = document.getElementById('tablaAlumnos');
    const loading = document.getElementById('loading');
    
    tbody.innerHTML = '';
    
    if (alumnos.length === 0) {
        loading.innerHTML = `<div class="empty-state">No hay alumnos ${filtroActual}<br><small style="opacity: 0.7; margin-top: 0.5rem; display: block;">Prueba cambiando el filtro o agrega un nuevo alumno</small></div>`;
        loading.style.display = 'block';
        tabla.style.display = 'none';
        return;
    }
    
    alumnos.forEach(alumno => {
        const tr = document.createElement('tr');
        if (alumno.activo == 0) {
            tr.classList.add('inactivo');
        }
        
        tr.innerHTML = `
            <td><span class="alumno-nombre">${alumno.nombre_completo}</span></td>
            <td><span class="badge badge-grupo">${alumno.grupo}</span></td>
            <td>${alumno.carrera}</td>
            <td>${alumno.turno}</td>
            <td>
                <span class="badge ${alumno.activo == 1 ? 'badge-activo' : 'badge-inactivo'}">
                    ${alumno.activo == 1 ? 'ACTIVO' : 'INACTIVO'}
                </span>
            </td>
            <td class="acciones">
                <a href="formulario_alumno.php?id=${alumno.id_alumno}" class="btn-accion btn-editar">Editar</a>
                <button onclick="toggleAlumno(${alumno.id_alumno}, ${alumno.activo}, '${alumno.nombre_completo.replace(/'/g, "\\'")}')" 
                        class="btn-accion ${alumno.activo == 1 ? 'btn-desactivar' : 'btn-activar'}">
                    ${alumno.activo == 1 ? 'Desactivar' : 'Activar'}
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    loading.style.display = 'none';
    tabla.style.display = 'table';
}

function actualizarEstadisticas(alumnos) {
    const activos = alumnos.filter(a => a.activo == 1).length;
    const inactivos = alumnos.filter(a => a.activo == 0).length;
    const gruposUnicos = new Set(alumnos.map(a => a.id_grupo));
    
    document.getElementById('totalActivos').textContent = activos;
    document.getElementById('totalInactivos').textContent = inactivos;
    document.getElementById('totalGrupos').textContent = gruposUnicos.size;
    document.getElementById('statsBar').style.display = 'flex';
}

async function toggleAlumno(id, estadoActual, nombre) {
    const accion = estadoActual == 1 ? 'desactivar' : 'activar';
    const confirmacion = estadoActual == 1 
        ? `¿Está seguro de desactivar al alumno ${nombre}?\n\nEl alumno no será eliminado, solo quedará inactivo.`
        : `¿Está seguro de activar al alumno ${nombre}?`;
    
    if (!confirm(confirmacion)) {
        return;
    }
    
    try {
        const response = await fetch(`/api/alumnos.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ activo: estadoActual == 1 ? 0 : 1 })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Mostrar mensaje temporal
            const loading = document.getElementById('loading');
            loading.style.display = 'block';
            loading.style.color = 'var(--secondary)';
            loading.textContent = data.message;
            
            setTimeout(() => {
                cargarAlumnos();
            }, 1000);
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al actualizar: ' + error.message);
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
