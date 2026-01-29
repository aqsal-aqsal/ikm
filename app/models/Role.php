<?php
class Role {
    private $file;
    public function __construct() {
        $this->file = dirname(__DIR__) . '/data/role_permissions.json';
    }
    public function getRoles() {
        $perms = $this->getPermissions();
        return array_keys($perms);
    }
    public function getPermissions() {
        $defaults = [
            'SUPERADMIN' => [
                'manage_users' => true,
                'manage_units' => true,
                'delete_surveys' => true,
                'export_csv' => true,
                'view_all_units_dashboard' => true
            ],
            'ADMIN_PROVINSI' => [
                'manage_users' => false,
                'manage_units' => false,
                'delete_surveys' => false,
                'export_csv' => true,
                'view_all_units_dashboard' => false
            ],
            'ADMIN_UPPD' => [
                'manage_users' => false,
                'manage_units' => false,
                'delete_surveys' => false,
                'export_csv' => true,
                'view_all_units_dashboard' => false
            ],
            'OPERATOR' => [
                'manage_users' => false,
                'manage_units' => false,
                'delete_surveys' => false,
                'export_csv' => false,
                'view_all_units_dashboard' => false
            ]
        ];
        if (file_exists($this->file)) {
            $json = file_get_contents($this->file);
            $data = json_decode($json, true);
            if (is_array($data)) return $data;
        }
        return $defaults;
    }
    public function savePermissions($permissions) {
        $clean = [];
        foreach ($permissions as $role => $perm) {
            $clean[$role] = [
                'manage_users' => !empty($perm['manage_users']),
                'manage_units' => !empty($perm['manage_units']),
                'delete_surveys' => !empty($perm['delete_surveys']),
                'export_csv' => !empty($perm['export_csv']),
                'view_all_units_dashboard' => !empty($perm['view_all_units_dashboard'])
            ];
        }
        if (!is_dir(dirname($this->file))) {
            mkdir(dirname($this->file), 0777, true);
        }
        return (bool)file_put_contents($this->file, json_encode($clean));
    }
    public function addRole($name, $permissions) {
        $data = $this->getPermissions();
        if (isset($data[$name])) return false;
        $data[$name] = [
            'manage_users' => !empty($permissions['manage_users']),
            'manage_units' => !empty($permissions['manage_units']),
            'delete_surveys' => !empty($permissions['delete_surveys']),
            'export_csv' => !empty($permissions['export_csv']),
            'view_all_units_dashboard' => !empty($permissions['view_all_units_dashboard'])
        ];
        return $this->savePermissions($data);
    }
    public function updateRole($original, $name, $permissions) {
        $data = $this->getPermissions();
        if (!isset($data[$original])) return false;
        if ($original !== $name && isset($data[$name])) return false;
        $data[$name] = [
            'manage_users' => !empty($permissions['manage_users']),
            'manage_units' => !empty($permissions['manage_units']),
            'delete_surveys' => !empty($permissions['delete_surveys']),
            'export_csv' => !empty($permissions['export_csv']),
            'view_all_units_dashboard' => !empty($permissions['view_all_units_dashboard'])
        ];
        if ($original !== $name) {
            unset($data[$original]);
        }
        return $this->savePermissions($data);
    }
    public function deleteRole($name) {
        if ($name === 'SUPERADMIN') return false;
        $data = $this->getPermissions();
        if (!isset($data[$name])) return false;
        unset($data[$name]);
        return $this->savePermissions($data);
    }
}
