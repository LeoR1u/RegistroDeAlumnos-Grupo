<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carrera_id = $_POST['carrera'];
    $grado = $_POST['grado'];
    $turno = $_POST['turno'];

    $carrera = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT siglas FROM carreras WHERE id = $carrera_id")
    );

    $siglas = $carrera['siglas'];

    $query = "
        SELECT MAX(secuencia) AS max_seq
        FROM grupos
        WHERE carrera_id = $carrera_id
          AND grado = $grado
          AND turno = '$turno'
    ";

    $result = mysqli_fetch_assoc(mysqli_query($conn, $query));
    $secuencia = ($result['max_seq'] ?? 0) + 1;

    $nombre_grupo = $siglas . $grado . str_pad($secuencia, 2, '0', STR_PAD_LEFT) . '-' . $turno;

    mysqli_query($conn, "
        INSERT INTO grupos (nombre_grupo, carrera_id, grado, turno, secuencia)
        VALUES ('$nombre_grupo', $carrera_id, $grado, '$turno', $secuencia)
    ");
}
?>

<h2>Crear Grupo</h2>

<form method="POST">
    <select name="carrera" required>
        <?php
        $carreras = mysqli_query($conn, "SELECT * FROM carreras");
        while ($c = mysqli_fetch_assoc($carreras)) {
            echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
        }
        ?>
    </select>

    <input type="number" name="grado" min="1" max="12" required>
    <select name="turno">
        <option value="M">Matutino</option>
        <option value="V">Vespertino</option>
        <option value="N">Nocturno</option>
    </select>

    <button type="submit">Crear Grupo</button>
</form>
