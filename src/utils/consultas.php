<?php

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
}

