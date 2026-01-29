<?php
class UnitController extends Controller {
    public function index() {
        requireRole(['SUPERADMIN']);
        
        $data = [
            'title' => 'Master Layanan',
            'units' => [],
            'error' => '',
            'success' => ''
        ];
        
        $unitModel = $this->model('Unit');
        
        // Handle Action
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'create') {
                if($unitModel->addUnit($_POST)) {
                    $data['success'] = 'Unit berhasil ditambahkan.';
                }
            } elseif ($action === 'update') {
                if($unitModel->updateUnit($_POST)) {
                    $data['success'] = 'Unit berhasil diperbarui.';
                }
            } elseif ($action === 'delete') {
                if(!$unitModel->deleteUnit($_POST['id'])) {
                    $data['error'] = 'Unit tidak bisa dihapus karena sudah memiliki data survei atau pengguna.';
                } else {
                    $data['success'] = 'Unit berhasil dihapus.';
                }
            }
        }
        
        // Fetch Data
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';

        $data['units'] = $unitModel->getAllUnits($limit, $offset, $search);
        $totalRecords = $unitModel->countUnits($search);
        $totalPages = ceil($totalRecords / $limit);
        
        $data['page'] = $page;
        $data['totalPages'] = $totalPages;
        $data['search'] = $search;

        $this->view('layout/header', $data);
        $this->view('layout/sidebar', $data);
        $this->view('unit/index', $data);
        $this->view('layout/footer');
    }
}
