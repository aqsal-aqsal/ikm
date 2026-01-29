<?php
class AuthController extends Controller {
    public function index() {
        $this->login();
    }

    public function login() {
        if (isset($_SESSION['user'])) {
            redirect('dashboard');
        }

        $data = [
            'title' => 'Login',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $userModel = $this->model('User');
            $user = $userModel->getUserByUsername($username);

            // Bootstrap Superadmin Check
            if (!$user) {
                if ($userModel->userCount() == 0 && $username === 'superadmin') {
                    $defaultHash = password_hash('admin123', PASSWORD_BCRYPT);
                    $userModel->createSuperAdmin($username, $defaultHash);
                    $user = $userModel->getUserByUsername($username);
                }
            }

            if ($user) {
                $valid = false;
                if (password_verify($password, $user['password'])) {
                    $valid = true;
                } elseif ($user['password'] === $password) { // Legacy support
                    $valid = true;
                }

                if ($valid) {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'role' => $user['role'],
                        'unit_id' => $user['unit_id']
                    ];
                    redirect('dashboard');
                } else {
                    $data['error'] = 'Username atau password salah.';
                }
            } else {
                $data['error'] = 'Username atau password salah.';
            }
        }

        $this->view('auth/login', $data);
    }

    public function logout() {
        session_destroy();
        redirect('auth/login');
    }
}
