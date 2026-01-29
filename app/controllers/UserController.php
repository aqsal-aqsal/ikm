<?php

class UserController extends Controller {
    public function index() {
        requireRole(['SUPERADMIN']);

        $data = [
            'title' => 'Kelola Pengguna',
            'users' => [],
            'units' => [],
            'roles' => [],
            'error' => '',
            'success' => ''
        ];

        $userModel = $this->model('User');
        $unitModel = $this->model('Unit');
        $roleModel = $this->model('Role');

        // Handle Form Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'create') {
                // Check if username exists
                if ($userModel->getUserByUsername($_POST['username'])) {
                    $data['error'] = 'Username sudah digunakan.';
                } else {
                    // Hash password
                    $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    
                    if ($userModel->createUser($_POST)) {
                        $data['success'] = 'Pengguna berhasil ditambahkan.';
                    } else {
                        $data['error'] = 'Gagal menambahkan pengguna.';
                    }
                }
            } elseif ($action === 'update') {
                // If password is provided, hash it
                if (!empty($_POST['password'])) {
                    $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                }
                
                if ($userModel->updateUser($_POST)) {
                    $data['success'] = 'Pengguna berhasil diperbarui.';
                } else {
                    $data['error'] = 'Gagal memperbarui pengguna.';
                }
            } elseif ($action === 'delete') {
                $id = $_POST['id'];
                // Prevent deleting self
                if ($id == $_SESSION['user']['id']) {
                    $data['error'] = 'Anda tidak dapat menghapus akun Anda sendiri.';
                } else {
                    if ($userModel->deleteUser($id)) {
                        $data['success'] = 'Pengguna berhasil dihapus.';
                    } else {
                        $data['error'] = 'Gagal menghapus pengguna.';
                    }
                }
            } elseif ($action === 'save_role_permissions') {
                if ($roleModel->savePermissions($_POST['permissions'] ?? [])) {
                    $data['success'] = 'Pengaturan role berhasil disimpan.';
                } else {
                    $data['error'] = 'Gagal menyimpan pengaturan role.';
                }
            }
        }

        // Fetch Data
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';

        $data['users'] = $userModel->getAllUsers($limit, $offset, $search);
        $totalRecords = $userModel->countUsers($search);
        $totalPages = ceil($totalRecords / $limit);
        
        $data['page'] = $page;
        $data['totalPages'] = $totalPages;
        $data['search'] = $search;

        $data['units'] = $unitModel->getAllUnits();
        $data['roles'] = $roleModel->getRoles();
        $data['role_permissions'] = $roleModel->getPermissions();

        // Search Filter (if implemented in model, currently doing client side or simple filter)
        // The model getAllUsers currently returns all. 
        // If search param exists, we might want to filter. 
        // But User model getAllUsers doesn't support search param yet.
        // Let's keep it simple for now or update model if needed. 
        // The previous view had search, but let's check if User model has search.
        // User model currently does NOT have search parameter in getAllUsers.
        // I will rely on the view to display all or update model if requested.
        // Actually, let's update the model to support search to be consistent with previous code.
        
        // Wait, I saw getAllUsers in User.php earlier:
        // public function getAllUsers() { ... query ... }
        // It didn't take params.
        
        $this->view('layout/header', $data);
        $this->view('layout/sidebar', $data);
        $this->view('user/index', $data);
        $this->view('layout/footer');
    }
}
