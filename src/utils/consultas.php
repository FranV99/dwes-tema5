<?php
//Si el usuario ya está logueado, lo mandamos al index
if (isset($_SESSION['usuario'])) {
    echo "<p>Ya estás logueado!</p>";
    echo "<li><a href='login.php'>Pulsa aqui para iniciar sesión</a></li>";
}

function existeUsuario($nombre){
    //Conectamos a mariadb
    $mysqli = new mysqli("db","dwes","dwes","dwes",3306);
    if ($mysqli->errno) {
        echo "No hay conexion con la base de datos.";
        return [];
    }

    //Preparamos la consulta
    $consulta = $mysqli->prepare("select id, nombre from usuario where nombre = ? ");
    if (!$consulta) {
        echo "Error: " . $mysqli->error;
        $mysqli->close();
        return true;
    }

    //Vinculamos
    $vinculacion = $consulta->bind_param("s", $nombre);
    if (!$vinculacion) {
        echo "Error al vincular: " . $mysqli->error;
        $consulta->close();
        $mysqli->close();
        return [];
    }

    //Ejecutamos
    $ejecucion = $consulta->execute();
    if (!$ejecucion) {
        echo "Error al ejecutar:" . $mysqli->error;
        $consulta->close();
        $mysqli->close();
        return[];
    }

    //Obtenemos los resultados
    $resultado = $consulta->get_result();
    if (!$resultado) {
        echo "Error al obtener los resultados.";
        $consulta->close();
        return[];
    }
    //Si devuelve una columna, el usuario existe
    $existe = $resultado->num_rows > 0;  

    return $existe;
    //cerramos todo

}

function insertarUsuario($nombre,$clave){
    //Conectamos a mariadb
    $mysqli = new mysqli("db","dwes","dwes","dwes",3306);
    if ($mysqli->errno) {
        echo "No hay conexion con la base de datos.";
        return [];
    }

    $clave_encriptada = password_hash($clave,PASSWORD_BCRYPT);
    $resultado = $mysqli->query(
       "insert into usuario (nombre, clave )
       values ('$nombre', '$clave_encriptada')"
    );

    return $resultado; 
    //cerramos todo
    $resultado->close();
    $mysqli->close();
}

function loginUsuario($nombre,$clave): bool
{
    //Conectamos a mariadb
    $mysqli = new mysqli("db","dwes","dwes","dwes",3306);
    if ($mysqli->errno) {
        echo "No hay conexion con la base de datos.";
        return false;
    }

    //Preparamos la consulta
    $consulta= $mysqli->prepare(
       "select nombre, clave from usuario where nombre = ?"
    );
    if (!$consulta) {
        echo "Error: " . $mysqli->error;
        $mysqli->close();
        return true;
    }

    //Vinculamos
    $vinculo = $consulta->bind_param("s", $nombre);
    if (!$vinculo) {
        echo "Error al vincular: " . $mysqli->error;
        $consulta->close();
        $mysqli->close();
        return false;
    }
    //Ejecutamos
    $ejecucion = $consulta->execute();
    if (!$ejecucion) {
        echo "Error al ejecutar:" . $mysqli->error;
        $consulta->close();
        $mysqli->close();
        return false;
    }

    //Obtenemos los resultados
    $resultado = $consulta->get_result();
    if (!$resultado) {
        echo "Error al obtener los resultados.";
        $consulta->close();
        return false;
    }
    $fila = $resultado->fetch_assoc();
    if ($fila == null) {
        return false;
    }

    if ($nombre == $fila['nombre'] && password_verify($clave, $fila['clave'])) {
        return true;
    } else {
        echo "Los campos no son correctos";
        return false;
    }
}

