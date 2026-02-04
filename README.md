# RegistroDeAlumnos-Grupo

Sistema de registro de alumnos y grupos.

## 👥 Equipo
- **LeoR1u** - Frontend (HTML, CSS, JavaScript)
- **Jonathan** - Backend (PHP, MySQL, API REST)

---

## 🗄️ Base de Datos

### Importar en phpMyAdmin:
1. Abre http://localhost/phpmyadmin
2. Click en **Importar**
3. Selecciona el archivo `database.sql`
4. Click en **Continuar**

---

## 🔌 API Endpoints

### Alumnos
| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/api/alumnos.php` | Listar todos |
| GET | `/api/alumnos.php?id=1` | Obtener uno |
| POST | `/api/alumnos.php` | Crear |
| PUT | `/api/alumnos.php?id=1` | Actualizar |
| DELETE | `/api/alumnos.php?id=1` | Eliminar |

**Body para POST/PUT:**
```json
{
    "nombre": "Juan",
    "apellido_paterno": "Pérez",
    "apellido_materno": "García",
    "id_grupo": 1
}
```

### Grupos
| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/api/grupos.php` | Listar todos |
| GET | `/api/grupos.php?id=1` | Obtener uno |
| POST | `/api/grupos.php` | Crear |
| PUT | `/api/grupos.php?id=1` | Actualizar |
| DELETE | `/api/grupos.php?id=1` | Eliminar |

**Body para POST/PUT:**
```json
{
    "id_carrera": 1,
    "id_turno": 2,
    "grado": 5
}
```

### Catálogos
| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/api/catalogos.php?tipo=carreras` | Lista de carreras |
| GET | `/api/catalogos.php?tipo=turnos` | Lista de turnos |
| GET | `/api/catalogos.php?tipo=grados` | Lista de grados |

---

## 💻 Uso en Frontend

Incluir el archivo JavaScript:
```html
<script src="js/api-client.js"></script>
```

### Ejemplos:

```javascript
// Cargar grupos en un select
cargarGruposEnSelect('selectGrupo');

// Crear alumno
const result = await ApiClient.createAlumno({
    nombre: 'Juan',
    apellido_paterno: 'Pérez',
    apellido_materno: 'García',
    id_grupo: 1
});

if (result.success) {
    alert('Alumno registrado');
}

// Obtener todos los alumnos
const alumnos = await ApiClient.getAlumnos();
console.log(alumnos.data);

// Eliminar alumno
await ApiClient.deleteAlumno(1);
```

---

## 📁 Estructura

```
RegistroDeAlumnos-Grupo/
├── api/
│   ├── alumnos.php      # CRUD alumnos
│   ├── grupos.php       # CRUD grupos
│   └── catalogos.php    # Carreras, turnos, grados
├── config/
│   └── database.php     # Conexión BD
├── includes/
│   └── functions.php    # Funciones auxiliares
├── js/
│   └── api-client.js    # Cliente JS para frontend
├── database.sql         # Script de BD
└── README.md
```

---

## ⚙️ Configuración

Editar `config/database.php` si es necesario:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'registro_alumnos');
```
