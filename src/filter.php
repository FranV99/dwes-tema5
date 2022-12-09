<?php
/*********************************************************************************************************************
 * Este script muestra un formulario a través del cual se pueden buscar imágenes por el nombre y mostrarlas. Utiliza
 * el operador LIKE de SQL para buscar en el nombre de la imagen lo que llegue por $_GET['nombre'].
 * 
 * Evidentemente, tienes que controlar si viene o no por GET el valor a buscar. Si no viene nada, muestra el formulario
 * de búsqueda. Si viene en el GET el valor a buscar (en $_GET['nombre']) entonces hay que preparar y ejecutar una 
 * sentencia SQL.
 * 
 * El valor a buscar se tiene que mantener en el formulario.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */

function filtra(string $texto): array
{
    //Conectamos a la base de datos
    $mysqli = new mysqli("db","dwes","dwes","dwes",3306);

    if ($mysqli->errno) {
        echo "Error: No hay conexión con la base de datos.";
        //En vez de terminar, mostramos un array vacío
        return[];
    }
    //Preparamos la consulta que se hará al buscar
    $sentencia = $mysqli->prepare(
        "select id, nombre, ruta from imagen where nombre like ?"
    );
    //Si la sentencia no funciona
    if (!$sentencia) {
        //Imprimimos el error
        echo "Error: " . $mysqli->error;
        //Cerramos la base de datos
        $mysqli->close();
        //Devolvemos un array vacio
        return [];
    }
    //Indicamos las variables(bind)
    $valor = '%' . $texto . '%';
    $vinculo = $sentencia->bind_param("s",$valor);
    //Si el vinculo falla
    if (!$vinculo) {
        echo "Error al vincular: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return[];
    }
    //Ejecutamos
    $ejecucion = $sentencia->execute();
    //Si no se ejecuta
    if (!$ejecucion) {
        echo "Error al ejecutar: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return[];
    }
    //Recuperamos las filas obtenidas como resultado
    $resultado = $sentencia->get_result();
    //Si no hay resultado
    if (!$resultado) {
        echo "Error en el resultado: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return[];
    }
    //Definimos el array donde se guardará la consulta introducida por el usuario
    $resultadoBusqueda = [];
    //Cuando el resultado sea diferente de null, guada cada fila dentro del array de la busqueda
    while (($fila = $resultado->fetch_assoc()) != null) {
        $resultadoBusqueda[] = $fila; 
    }
    return $resultadoBusqueda;
}
//Programa principal
//Creamos un array que contenga los posts
$posts = [];
//Si hay get y hay nombre, sanea el nombre, si no, deja un espacio en blanco
$textoBuscar = $_GET && isset($_GET['nombre']) ? htmlentities(trim($_GET['nombre'])) : '';
//Si el nombre introducido no está vacío
if (mb_strlen($textoBuscar) > 0) {
    //Llamamos a la funcion tocha de antes y le pasamos el texto introducido en el get
    $posts = filtra($textoBuscar);
}

?>

<?php
/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: completa el código de la vista añadiendo el menú de navegación.
 * - TODO: en el formulario falta añadir el nombre que se puso cuando se envió el formulario.
 * - TODO: debajo del formulario tienen que aparecer las imágenes que se han encontrado en la base de datos.
 */
?>
<h1>Galería de imágenes</h1>

<h2>Busca imágenes por filtro</h2>

<form method="get">
    <p>
        <label for="nombre">Busca por nombre</label>
        <!--Se muestra el texto introducido en el input-->
        <input type="text" name="nombre" id="nombre" value="<?= $textoBuscar ?>">
    </p>
    <p>
        <input type="submit" value="Buscar">
    </p>
</form>

<?php
//Imprimimos los post dentro de posts
foreach($posts as $post){
    echo <<<END
        <p>{$post['titulo']}</p>
    END;
}
?>
