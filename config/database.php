<?php
class Database
{
    public static function pdo_gbi()
    {
        $dbHostGbi = env('GBI_DB_HOST');
        $dbUserGbi = env('GBI_DB_USER');
        $dbPasswordGbi = env('GBI_DB_PASSWORD');
        $dbNameGbi = env('GBI_DB_NAME');

        $mbd = new PDO("sqlsrv:Server=$dbHostGbi;Database=$dbNameGbi", $dbUserGbi, $dbPasswordGbi);
        $mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $mbd;
    }

    public static function pdo_erp()
    {
        $dbHostSamy = env('SAMY_DB_HOST');
        $dbUserSamy = env('SAMY_DB_USER');
        $dbPasswordSamy = env('SAMY_DB_PASSWORD');
        $dbNameSamy = env('SAMY_DB_NAME');

        $mbd = new PDO("sqlsrv:Server=$dbHostSamy;Database=$dbNameSamy", $dbUserSamy, $dbPasswordSamy);
        $mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $mbd;
    }
}
