/**
 * API CLIENT - Funciones para consumir el backend
 * El compañero del frontend puede usar estas funciones
 */

const API_BASE = 'api';

// ========================================
// CLASE PRINCIPAL
// ========================================
class ApiClient {

    // ---- ALUMNOS ----
    
    static async getAlumnos() {
        const res = await fetch(`${API_BASE}/alumnos.php`);
        return await res.json();
    }

    static async getAlumno(id) {
        const res = await fetch(`${API_BASE}/alumnos.php?id=${id}`);
        return await res.json();
    }

    static async createAlumno(datos) {
        const res = await fetch(`${API_BASE}/alumnos.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        return await res.json();
    }

    static async updateAlumno(id, datos) {
        const res = await fetch(`${API_BASE}/alumnos.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        return await res.json();
    }

    static async deleteAlumno(id) {
        const res = await fetch(`${API_BASE}/alumnos.php?id=${id}`, {
            method: 'DELETE'
        });
        return await res.json();
    }

    // ---- GRUPOS ----

    static async getGrupos() {
        const res = await fetch(`${API_BASE}/grupos.php`);
        return await res.json();
    }

    static async getGrupo(id) {
        const res = await fetch(`${API_BASE}/grupos.php?id=${id}`);
        return await res.json();
    }

    static async createGrupo(datos) {
        const res = await fetch(`${API_BASE}/grupos.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        return await res.json();
    }

    static async updateGrupo(id, datos) {
        const res = await fetch(`${API_BASE}/grupos.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        return await res.json();
    }

    static async deleteGrupo(id) {
        const res = await fetch(`${API_BASE}/grupos.php?id=${id}`, {
            method: 'DELETE'
        });
        return await res.json();
    }

    static async generarClaveGrupo(carrera, turno, grado) {
        const res = await fetch(`${API_BASE}/grupos.php?generar_clave=1&carrera=${carrera}&turno=${turno}&grado=${grado}`);
        return await res.json();
    }

    // ---- CATÁLOGOS ----

    static async getCarreras() {
        const res = await fetch(`${API_BASE}/catalogos.php?tipo=carreras`);
        return await res.json();
    }

    static async getTurnos() {
        const res = await fetch(`${API_BASE}/catalogos.php?tipo=turnos`);
        return await res.json();
    }

    static async getGrados() {
        const res = await fetch(`${API_BASE}/catalogos.php?tipo=grados`);
        return await res.json();
    }
}

// ========================================
// FUNCIONES HELPER PARA CARGAR SELECTS
// ========================================

async function cargarGruposEnSelect(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="">Cargando...</option>';
    const result = await ApiClient.getGrupos();
    
    if (result.success) {
        select.innerHTML = '<option value="">Seleccione un grupo</option>';
        result.data.forEach(g => {
            select.innerHTML += `<option value="${g.id_grupo}">${g.clave} - ${g.carrera}</option>`;
        });
    }
}

async function cargarCarrerasEnSelect(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="">Cargando...</option>';
    const result = await ApiClient.getCarreras();
    
    if (result.success) {
        select.innerHTML = '<option value="">Seleccione una carrera</option>';
        result.data.forEach(c => {
            select.innerHTML += `<option value="${c.id_carrera}">${c.nombre}</option>`;
        });
    }
}

async function cargarTurnosEnSelect(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="">Cargando...</option>';
    const result = await ApiClient.getTurnos();
    
    if (result.success) {
        select.innerHTML = '<option value="">Seleccione un turno</option>';
        result.data.forEach(t => {
            select.innerHTML += `<option value="${t.id_turno}">${t.nombre}</option>`;
        });
    }
}

function cargarGradosEnSelect(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="">Seleccione un grado</option>';
    for (let i = 1; i <= 9; i++) {
        select.innerHTML += `<option value="${i}">${i}° Semestre</option>`;
    }
}

// ========================================
// FUNCIÓN PARA ACTUALIZAR CLAVE AUTOMÁTICA
// ========================================

async function actualizarClaveGrupo(carreraId, turnoId, gradoId, claveId) {
    const carrera = document.getElementById(carreraId).value;
    const turno = document.getElementById(turnoId).value;
    const grado = document.getElementById(gradoId).value;
    const claveInput = document.getElementById(claveId);
    
    if (carrera && turno && grado) {
        const result = await ApiClient.generarClaveGrupo(carrera, turno, grado);
        if (result.success) {
            claveInput.value = result.data.clave;
        }
    } else {
        claveInput.value = '';
    }
}
