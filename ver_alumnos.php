<?php
include 'config.php';

$query = "
SELECT a.nombre, a.apellido_paterno, a.apellido_materno, g.nombre_grupo
FROM alumnos a
JOIN grupos g ON a.grupo_id = g.id
";

$result = mysqli_query($conn, $query);
?>

<h2>Listado de Alumnos</h2>

<table border="1">
<tr>
    <th>Alumno</th>
    <th>Grupo</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['nombre'].' '.$row['apellido_paterno'].' '.$row['apellido_materno'] ?></td>
    <td><?= $row['nombre_grupo'] ?></td>
</tr>
<?php } ?>
</table>
