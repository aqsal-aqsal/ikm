<?php

class RoleController extends Controller {
    public function index() {
        requireRole(['SUPERADMIN']);

        $roleModel = $this->model('Role');
        $userModel = $this->model('User');

        $data = [
            'title' => 'Pengaturan Role & Akses',
            'roles' => $roleModel->getRoles(),
            'role_permissions' => $roleModel->getPermissions(),
            'success' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'create') {
                $name = trim($_POST['role_name']);
                if ($name === '' || isset($data['role_permissions'][$name])) {
                    $data['error'] = 'Role sudah ada atau nama tidak valid.';
                } else {
                    if ($roleModel->addRole($name, $_POST['permissions'] ?? [])) {
                        $data['success'] = 'Role berhasil ditambahkan.';
                    } else {
                        $data['error'] = 'Gagal menambahkan role.';
                    }
                }
            } elseif ($action === 'update') {
                $original = $_POST['original_role'];
                $name = trim($_POST['role_name']);
                if ($original === 'SUPERADMIN') {
                    $data['error'] = 'Role SUPERADMIN tidak dapat diubah.';
                } else {
                    if ($roleModel->updateRole($original, $name, $_POST['permissions'] ?? [])) {
                        $data['success'] = 'Role berhasil diperbarui.';
                    } else {
                        $data['error'] = 'Gagal memperbarui role.';
                    }
                }
            } elseif ($action === 'delete') {
                $name = $_POST['role_name'];
                if ($name === 'SUPERADMIN') {
                    $data['error'] = 'Role SUPERADMIN tidak dapat dihapus.';
                } else {
                    if ($userModel->countUsersByRole($name) > 0) {
                        $data['error'] = 'Role tidak bisa dihapus karena sedang digunakan oleh pengguna.';
                    } else {
                        if ($roleModel->deleteRole($name)) {
                            $data['success'] = 'Role berhasil dihapus.';
                        } else {
                            $data['error'] = 'Gagal menghapus role.';
                        }
                    }
                }
            }
            $data['roles'] = $roleModel->getRoles();
            $data['role_permissions'] = $roleModel->getPermissions();
        }

        $this->view('layout/header', $data);
        $this->view('layout/sidebar', $data);
        $this->view('role/index', $data);
        $this->view('layout/footer');
    }
}
