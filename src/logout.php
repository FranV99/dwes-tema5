<?php
/**********************************************************************************************************************
 * Este script tan solo tiene que destruir la sesión y volver a la página principal.
 * 
 * UN USUARIO NO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
//Si el usuario ya está logueado, lo mandamos al index
if (isset($_SESSION['usuario'])) {
    echo "<p>Ya estás logueado!</p>";
    echo "<li><a href='login.php'>Pulsa aqui para iniciar sesión</a></li>";
}
session_start();

session_destroy();

header("Location: index.php");
