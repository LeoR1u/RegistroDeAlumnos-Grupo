<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $ap = $_POST['ap'];
    $am = $_POST['am'];
    $grupo = $_POST['grupo'];

    mysqli_query($conn, "
        INSERT INTO alumnos (nombre, apellido_paterno, apellido_materno, grupo_id)
        VALUES ('$nombre', '$ap', '$am', $grupo)
    ");
}
?>

<h2>Registrar Alumno</h2>

<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="ap" placeholder="Apellido Paterno" required>
    <input type="text" name="am" placeholder="Apellido Materno" required>

    <select name="grupo">
        <?php
        $grupos = mysqli_query($conn, "SELECT * FROM grupos");
        while ($g = mysqli_fetch_assoc($grupos)) {
            echo "<option value='{$g['id']}'>{$g['nombre_grupo']}</option>";
        }
        ?>
    </select>

    <button type="submit">Registrar</button>
</form>
