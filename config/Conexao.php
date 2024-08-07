<?php



class Conexao
{
    private static $instancia;

    private function __construct()
    {
        if (in_array($_SERVER['REMOTE_ADDR'], ["127.0.0.1", "::1"])) {
            // por enquanto nao vou definir senha para o banco de dados para facilitar o compartilhamento do codigo
            $hostname = 'localhost';
            $database = 'adotyx';
            $username = 'root';
            $password = '';
        }

        $dsn = "mysql:host=$hostname;dbname=$database";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
            self::$instancia = new PDO($dsn, $username, $password, $options);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public static function getConexao()
    {
        if (!isset(self::$instancia)) {
            new Conexao();
        }
        return self::$instancia;
    }
}
