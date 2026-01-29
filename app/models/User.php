<?php
class User {
    private $table = 'users';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getUserByUsername($username) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE username = :username');
        $this->db->bind('username', $username);
        return $this->db->single();
    }
    
    public function userCount() {
        $this->db->query('SELECT COUNT(*) as count FROM ' . $this->table);
        $row = $this->db->single();
        return $row['count'];
    }
    
    public function createSuperAdmin($username, $hash) {
        $this->db->query("INSERT INTO " . $this->table . " (username, password, role, unit_id) VALUES (:username, :password, 'SUPERADMIN', NULL)");
        $this->db->bind('username', $username);
        $this->db->bind('password', $hash);
        return $this->db->execute();
    }
    
    public function getAllUsers($limit = null, $offset = 0, $search = '') {
        $query = "
            SELECT u.*, un.nama as unit_nama 
            FROM users u 
            LEFT JOIN units un ON u.unit_id = un.id 
            WHERE 1=1
        ";
        
        if ($search) {
            $query .= " AND (u.username LIKE :search OR un.nama LIKE :search)";
        }
        
        $query .= " ORDER BY u.username ASC";
        
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
    
    public function countUsers($search = '') {
        $query = "
            SELECT COUNT(*) as count 
            FROM users u 
            LEFT JOIN units un ON u.unit_id = un.id 
            WHERE 1=1
        ";
        
        if ($search) {
            $query .= " AND (u.username LIKE :search OR un.nama LIKE :search)";
        }
        
        $this->db->query($query);
        if ($search) $this->db->bind('search', "%$search%");
        
        $row = $this->db->single();
        return $row['count'];
    }

    public function createUser($data) {
        $query = "INSERT INTO users (username, password, role, unit_id) VALUES (:username, :password, :role, :unit_id)";
        $this->db->query($query);
        $this->db->bind('username', $data['username']);
        $this->db->bind('password', $data['password']);
        $this->db->bind('role', $data['role']);
        $this->db->bind('unit_id', $data['unit_id']);
        return $this->db->execute();
    }

    public function updateUser($data) {
        $query = "UPDATE users SET username = :username, role = :role, unit_id = :unit_id WHERE id = :id";
        if (!empty($data['password'])) {
            $query = "UPDATE users SET username = :username, password = :password, role = :role, unit_id = :unit_id WHERE id = :id";
        }
        $this->db->query($query);
        $this->db->bind('username', $data['username']);
        $this->db->bind('role', $data['role']);
        $this->db->bind('unit_id', $data['unit_id']);
        $this->db->bind('id', $data['id']);
        if (!empty($data['password'])) {
            $this->db->bind('password', $data['password']);
        }
        return $this->db->execute();
    }

    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }
    
    public function countUsersByRole($role) {
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = :role");
        $this->db->bind('role', $role);
        $row = $this->db->single();
        return (int)$row['count'];
    }
}
