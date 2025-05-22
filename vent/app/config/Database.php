<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host = 'localhost:3306';
    private $dbname = 'alexis-nguemby-mbina_vent';
    private $username = 'insert dbname';
    private $password = 'insert passwordname';

    // Changer "protected" en "public"
    public function connect() {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
             // Débogage
            return $pdo;
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }
}



