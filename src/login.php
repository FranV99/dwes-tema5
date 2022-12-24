<?php
/**********************************************************************************************************************
 * Este programa, a través del formulario que tienes que hacer debajo, en el área de la vista, realiza el inicio de
 * sesión del usuario verificando que ese usuario con esa contraseña existe en la base de datos.
 * 
 * Para mantener iniciada la sesión dentrás que usar la $_SESSION de PHP.
 * 
 * En el formulario se deben indicar los errores ("Usuario y/o contraseña no válido") cuando corresponda.
 * 
 * Dicho formulario enviará los datos por POST.
 * 
 * Cuando el usuario se haya logeado correctamente y hayas iniciado la sesión, redirige al usuario a la
 * página principal.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
 
session_start();

require 'utils/consultas.php';

$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

function validar($nombre,$clave): array{
    //Array con los errores
    $errores = [
        'nombre' => null,
        'clave' => null
    ];

    //Si no hay nombre
    if (mb_strlen($nombre) == 0) {
        $errores['nombre'] = 'nonombre';
    }
    //Si no hay contraseña 1
    if (mb_strlen($clave) == 0) {
        $errores['clave'] = 'noclave';
    }

    //Si la contraseña es menor a 8 caracteres
    if (mb_strlen($clave) < 8 && mb_strlen($clave) > 0) {
        $errores['clave'] = 'nominclave';
    }

    if (loginUsuario($nombre,$clave) == false) {
        $errores['nombre'] = 'erroval';
    }

    //Devuelve el array
    return $errores;
}

$errores = [
    'nombre' => null,
    'clave' => null
];

$esOk = false;
//Si hay post y hay nombre y post
if ($_POST && isset($_POST['nombre']) && isset($_POST['clave'])) {
    
    //saneamos los campos
    $nombre = htmlentities(trim($_POST['nombre']));
    $clave = htmlentities(trim($_POST['clave']));

    //Validamos
    $errores = validar($nombre,$clave);
    //Si no hay errores
    if ($errores['nombre'] == null && $errores['clave'] == null) {
        //Verificamos que este todo correcto
        $esOk = loginUsuario($nombre,$clave);
        //Si esta todo bien
        if ($esOk == true) {
            $_SESSION['usuario'] = $nombre;
            echo "No hay errores";
            echo "<p> Te has logueado correctamente.</p>";
            echo "<li><a href='index.php'>Pulsa aqui para ir al inicio</a></li>";
            exit();
        } else {
            echo "Los valores introdocidos no son correctos";
        }
    } 
}

$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$clave = isset($_POST['clave']) ? $_POST['clave'] : '';

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: añadir el menú.
 * - TODO: formulario con nombre de usuario y contraseña.
 */
?>
<?php
//Si el usuario ya está logueado, lo mandamos al index
if (isset($_SESSION['usuario'])) {
    echo "<p>Ya estás logueado!</p>";
    echo "<li><a href='index.php'>Pulsa aqui para ir al inicio</a></li>";
}
if ($usuario == null) {
    echo <<<END
        <ul>
            <li><a href="index.php"><b>Home</b></a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Iniciar sesión</a></li>
        </ul>
    END;
} else {
    return <<<END
        <ul>
            <li><strong>Home</strong></li>
            <li><a href="add.php">Añadir imagen</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="logout.php">Cerrar sesión ($usuario)</a></li>
        </ul>
    END;
}

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
    
<?php if(!$_POST || ($_POST && ($errores['nombre'] != null || $errores['clave'] != null))) { ?>
<h1>Inicia sesión</h1>

<form action="login.php" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label><br>
        <input type="text" name="nombre" id="nombre" value="<?= $nombre ?>">
        <?php if($errores['nombre'] == 'nonombre'){ echo "<p>El nombre no puede estar en blanco.</p>";} ?>
    </p>
    <p>
        <label for="clave">Contraseña</label><br>
        <input type="password" name="clave" id="clave" value="<?= $clave ?>">
        <?php if($errores['clave'] == 'noclave'){ echo "<p>La contraseña no puede estar en blanco.</p>";} ?>
    </p>
    <?php if($errores['nombre'] == 'errorval'){ echo "<p>Error al validar.</p>";} ?>
    <p>
        <input type="submit" value="Inicia sesión">
    </p>
</form>
</body>
</html>

<?php
}