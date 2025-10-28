<?php
class Log
{
    protected $rutaDir;
    protected $rutaLog;

    public function __construct()
    {
        // carpeta logs relativa al directorio actual (model)
        $this->rutaDir = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'logs';
        // si no existe, intentamos crearla
        if (!is_dir($this->rutaDir)) {
            @mkdir($this->rutaDir, 0777, true);
        }
        // archivo diario por defecto
        $this->rutaLog = $this->rutaDir . DIRECTORY_SEPARATOR . "log_" . date('Y-m-d') . ".log";
    }

    /**
     * Registrar error.
     * $parameters es opcional y siempre será convertido a array.
     */
    public function registrar_error($msg, $parameters = [])
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $time = date('Y-m-d H:i:s');
        $paramsText = @json_encode($parameters, JSON_UNESCAPED_UNICODE);
        if ($paramsText === false) {
            // fallback si json falla
            $paramsText = var_export($parameters, true);
        }

        $log = "[$time] [ERROR] $msg" . PHP_EOL;
        $log .= "[$time] [PARAMETROS] $paramsText" . PHP_EOL . PHP_EOL;

        $this->safe_write($log);
    }

    /**
     * Mensaje general (info)
     */
    public function registrar_msg($msg)
    {
        $time = date('Y-m-d H:i:s');
        $log = "[$time] [MSG] $msg" . PHP_EOL;
        $this->safe_write($log);
    }

    /**
     * Registrar info con parámetros opcionales
     */
    public function registrar_info($msg, $parameters = [])
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $time = date('Y-m-d H:i:s');
        $paramsText = @json_encode($parameters, JSON_UNESCAPED_UNICODE);
        if ($paramsText === false) {
            $paramsText = var_export($parameters, true);
        }

        $log = "[$time] [INFO] $msg" . PHP_EOL;
        $log .= "[$time] [PARAMETROS] $paramsText" . PHP_EOL . PHP_EOL;
        $this->safe_write($log);
    }

    /**
     * Escribir de forma segura en el archivo de log, con fallback a temp dir
     */
    protected function safe_write($text)
    {
        // intentar escribir en el log diario
        $ok = @error_log($text, 3, $this->rutaLog);

        if ($ok === false) {
            // fallback: intentar crear la carpeta otra vez y reintentar
            if (!is_dir($this->rutaDir)) {
                @mkdir($this->rutaDir, 0777, true);
            }
            $ok = @error_log($text, 3, $this->rutaLog);
        }

        if ($ok === false) {
            // último recurso: escribir en sys_get_temp_dir()
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'debug_siesa_fallback.log';
            @error_log($text, 3, $tmp);
        }
    }

    /**
     * Método utilitario público si quieres escribir un archivo de debug específico
     * ejemplo: $log->writeDebug('debug_siesa.log', 'texto...');
     */
    public function writeDebug($filename, $text)
    {
        $path = $this->rutaDir . DIRECTORY_SEPARATOR . basename($filename);
        // asegurarse de la carpeta
        if (!is_dir($this->rutaDir)) {
            @mkdir($this->rutaDir, 0777, true);
        }
        @file_put_contents($path, $text, FILE_APPEND | LOCK_EX);
    }
}
