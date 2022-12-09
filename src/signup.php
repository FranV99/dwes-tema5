<?php
/*********************************************************************************************************************
 * Este script realiza el registro del usuario vía el POST del formulario que hay debajo, en la vista.
 * 
 * Cuando llegue POST hay que validarlo y si todo fue bien insertar en la base de datos el usuario.
 * 
 * Requisitos del POST:
 * - El nombre de usuario no tiene que estar vacío y NO PUEDE EXISTIR UN USUARIO CON ESE NOMBRE EN LA BASE DE DATOS.
 * - La contraseña tiene que ser, al menos, de 8 caracteres.
 * - Las contraseñas tiene que coincidir.
 * 
 * La contraseña la tienes que guardar en la base de datos cifrada mediante el algoritmo BCRYPT.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
//Primero que nada iniciamos la sesión
session_start();

require 'utils/consultas.php';

//Si el usuario ya está logueado, lo mandamos al index
if (isset($_SESSION['usuario'])) {
    echo "<p>Ya estás logueado!</p>";
    echo "<li><a href='login.php'>Pulsa aqui para iniciar sesión</a></li>";
}

//Funcion para validar el nombre y las contraseñas
function validar($nombre, $clave,$repite_clave): array
{
    //Array con los errores
    $errores = [
        'nombre' => null,
        'clave' => null,
        'repite_clave' => null
    ];

    //Si no hay nombre
    if (mb_strlen($nombre) == 0) {
        $errores['nombre'] = 'nonombre';
        //No va esta mierda
    } else if (existeUsuario($nombre)) {
        $errores['nombre'] = 'existeusuario';
    }

    //Si no hay contraseña 1
    if (mb_strlen($clave) == 0) {
        $errores['clave'] = 'noclave';
    }

    //Si la contraseña es menor a 8 caracteres
    if (mb_strlen($clave) < 8 && mb_strlen($clave) > 0) {
        $errores['clave'] = 'nominclave';
    }

    //Si no hay contraseña 2
    if (mb_strlen($repite_clave) == 0) {
        $errores['repite_clave'] = 'norepetirclave';
    }

    //Si la contraseña es menor a 8 caracteres
    if (mb_strlen($repite_clave) < 8 && mb_strlen($repite_clave) > 0) {
        $errores['repite_clave'] = 'nominrepetirclave';
    }

    //Si no coinciden las contraseñas
    if ($clave != $repite_clave) {
        $errores['repite_clave'] = 'nocoinciclave';
    }

    //Devuelve el array
    return $errores;
}
//Iniciamos los errores a null
$errores = [
    'nombre' => null,
    'clave' => null,
    'repite_clave' => null
];
//Si hay post y hay contenido dentro de los campos
if ($_POST && isset($_POST['nombre']) && isset($_POST['clave']) && isset($_POST['repite_clave'])) {
    //Saneamos todo
    $nombre = htmlentities(trim($_POST['nombre']));
    $clave = htmlentities(trim($_POST['clave']));
    $repite_clave = htmlentities(trim($_POST['repite_clave']));

    //Se comprueba si hay errores
    $errores = validar($nombre,$clave,$repite_clave);
    
    //Si no hay errores
    if ($errores['nombre'] == null && $errores['clave'] == null && $errores['repite_clave'] == null) {
        //Se insertan los valores en la base de datos
        insertarUsuario($nombre,$clave);
        echo "No hay errores";
    }
}


$nombreTemp = $_POST && isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '';
$claveTemp = $_POST && isset($_POST['clave']) ? htmlspecialchars(trim($_POST['clave'])) : '';
$repetir_claveTemp = $_POST && isset($_POST['repite_clave']) ? htmlspecialchars(trim($_POST['repite_clave'])) : '';



//Si da error

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: los errores que se produzcan tienen que aparecer debajo de los campos.
 * - TODO: cuando hay errores en el formulario se debe mantener el valor del nombre de usuario en el campo
 *         correspondiente.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
<?php if(!$_POST || ($_POST && ($errores['nombre'] != null || 
                                $errores['clave'] != null || 
                                $errores['repite_clave'] != null))) { ?>
<h1>Regístrate</h1>
<form action="#" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre" value="<?= $nombreTemp ?>">
        <?php if($errores['nombre'] == 'nonombre'){ echo "<p>No se ha introducido un nombre.</p>"; } ?>
        <?php if($errores['nombre'] == 'existeusuario'){ echo "<p>El nombre de usuario ya esta registrado.</p>"; } ?>
    </p>
    <p>
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave" value="<?= $claveTemp ?>">
        <?php if($errores['clave'] == 'noclave'){ echo "<p>No se ha introducido una clave.</p>"; } ?>
        <?php if($errores['clave'] == 'nominclave'){ echo "<p>La contraseña tiene que tener al menos 8 caracteres.</p>"; } ?>
    </p>
    <p>
        <label for="repite_clave">Repite la contraseña</label>
        <input type="password" name="repite_clave" id="repite_clave" value="<?= $repetir_claveTemp ?>">
        <?php if($errores['repite_clave'] == 'norepetirclave'){ echo "<p>No se ha introducido una segunda clave.</p>"; } ?>
        <?php if($errores['repite_clave'] == 'nominrepetirclave'){ echo "<p>La contraseña tiene que tener al menos 8 caracteres.</p>"; } ?>
        <?php if($errores['repite_clave'] == 'nocoinciclave'){ echo "<p>Las claves no coinciden.</p>"; } ?>
        
    </p>
    <p>
        <input type="submit" value="Regístrate">
    </p>
</form>
<?php } else {
            echo "<p> Se ha subido todo correctamente.</p>";
            echo "<li><a href='login.php'>Pulsa aqui para iniciar sesión</a></li>";
      }
?>
</body>
</html>