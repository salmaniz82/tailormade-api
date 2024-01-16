<?php

namespace App\Modules;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

class usersModule
{

    public $DB;

    public function __construct()
    {

        $this->DB = new \Framework\Database();
        $this->DB->table = 'users';
    }


    public function fetchUserDashboard()
    {

        $query = "SELECT u.id, u.role_id, r.role as 'role', u.name as 'username', u.email, u.isActive from users u
        LEFT JOIN roles r on r.id = u.role_id";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function emailExists($email)
    {
        $query = "SELECT id from users where email = ?";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }


    public function getUserById($id)
    {

        $query = "SELECT id, name, email, isActive from users WHERE id = ?";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('d', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return false;
        }

        return $result->fetch_assoc();
    }



    public function remove($id): bool
    {

        $query = "DELETE FROM users where id = ? AND id <> 1 LIMIT 1";

        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('d', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateUser($payload, $id): bool
    {

        if ($this->DB->update($payload, $id)) {
            return true;
        } else {
            return false;
        }
    }
}
