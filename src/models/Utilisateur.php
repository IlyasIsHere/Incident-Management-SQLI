<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/src/config/dbconfig.php';

class Utilisateur {
    protected $id;
    protected $email;
    protected $nom;
    protected $prenom;
    protected $password;
    protected $role;

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function getUserRow() {
        global $db;
        $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$this->getEmail()]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() != 0) {
            return $row;
        }
        else {
            return false;
        }
    }

    public static function getUserRowById($id) {
        global $db;
        $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE id = ?');

        if (! $stmt->execute([$id])) {
            return false;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkLogin() {

        $row = $this->getUserRow();
        if ($row && password_verify($this->getPassword(), $row['password'])) {
            return true;
        }
        else {
            return false;
        }
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }


}

