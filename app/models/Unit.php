<?php
class Unit {
    private $table = 'units';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllUnits($limit = null, $offset = 0, $search = '') {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        
        if ($search) {
            $query .= " AND (nama LIKE :search OR jenis LIKE :search)";
        }
        
        $query .= " ORDER BY nama ASC";
        
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $this->db->query($query);
        
        if ($search) $this->db->bind('search', "%$search%");
        if ($limit) {
            $this->db->bind('limit', $limit, PDO::PARAM_INT);
            $this->db->bind('offset', $offset, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet();
    }
    
    public function countUnits($search = '') {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE 1=1";
        
        if ($search) {
            $query .= " AND (nama LIKE :search OR jenis LIKE :search)";
        }
        
        $this->db->query($query);
        if ($search) $this->db->bind('search', "%$search%");
        
        $row = $this->db->single();
        return $row['count'];
    }
    
    public function searchUnits($keyword) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE nama LIKE :keyword OR jenis LIKE :keyword");
        $this->db->bind('keyword', "%$keyword%");
        return $this->db->resultSet();
    }

    public function addUnit($data) {
        $query = "INSERT INTO units (nama, jenis) VALUES (:nama, :jenis)";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('jenis', $data['jenis']);
        return $this->db->execute();
    }
    
    public function updateUnit($data) {
        $query = "UPDATE units SET nama = :nama, jenis = :jenis WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('jenis', $data['jenis']);
        $this->db->bind('id', $data['id']);
        return $this->db->execute();
    }

    public function deleteUnit($id) {
        // Check dependency
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE unit_id = :id");
        $this->db->bind('id', $id);
        if ($this->db->single()['count'] > 0) return false;
        
        $this->db->query("SELECT COUNT(*) as count FROM survey WHERE unit_id = :id");
        $this->db->bind('id', $id);
        if ($this->db->single()['count'] > 0) return false;

        $query = "DELETE FROM units WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }
}
