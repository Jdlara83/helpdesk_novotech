<?php

class Database
{
    private static string $host = "localhost";
    private static string $db_name = "helpdesk_novotech";
    private static string $username = "root";
    private static string $password = "Ninguna123.";
    private static ?PDO $conn = null;

    public static function getConnection(): PDO
    {
        if (self::$conn === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";

                self::$conn = new PDO($dsn, self::$username, self::$password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                die("Error de conexión a la base de datos: " . $exception->getMessage());
            }
        }

        return self::$conn;
    }
}