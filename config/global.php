<?php

/**
 * Obtener variables de entorno del sistema
 * @param $key
 * @return mixed
 */
function env($key = '')
{
    # Ruta .env
    $enviromentFile = __DIR__ . DIRECTORY_SEPARATOR . "../.env";
    # Acceder al .env
    $env = parse_ini_file($enviromentFile);
    return $env[$key];
}
