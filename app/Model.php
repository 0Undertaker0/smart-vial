<?php
require_once __DIR__ . '/config.php';

class Model
{
    public $db;

    public function __construct()
    {
        $this->db = getDb();
    }

    public function query($sql, $types = null, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }
        if ($types && $params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();
        return $res;
    }
}
