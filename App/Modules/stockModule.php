<?php

namespace App\Modules;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use \Framework\Database;

class stockModule
{

    public $DB;


    public function __construct()
    {
        $this->DB = new Database();
        $this->DB->table = 'stocks';
    }


    public function delete($id)
    {

        $query = "DELETE FROM stocks where id = ? LIMIT 1";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('d', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }



    public function getStocks()
    {

        $query = "SELECT id, title, url, alias, metaFields from stocks";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function pluckAliasviaSource($source)
    {

        $query = "SELECT alias from stocks WHERE url = '{$source}' LIMIT 1";
        $stmt = $this->DB->connection->prepare($query);
        if (!$stmt->execute()) {
            return false;
        } else {
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data[0]['alias'];
        }
    }


    public function update($payload, $id)
    {
        if ($this->DB->update($payload, $id)) {
            return true;
        }
        return false;
    }


    public function addStocks($dataPayload)
    {
        if ($lastId = $this->DB->insert($dataPayload)) {
            return $lastId;
        }
        return false;
    }


    public function getStockByID($id)
    {

        $query = "SELECT id, title, url, alias, metaFields from stocks where id = ? LIMIT 1";
        $stmt = $this->DB->connection->prepare($query);
        $stmt->bind_param('d', $id);
        if (!$stmt->execute()) {
            return false;
        }

        $result = $stmt->get_result();
        $output = $result->fetch_all(MYSQLI_ASSOC);

        if (sizeof($output) > 0) {
            return $output[0];
        }

        return false;
    }


    public function save($payload)
    {

        if ($id = $this->DB->insert($payload)) {
            return $id;
        }

        return false;
    }
}
