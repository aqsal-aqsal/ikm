<?php

class LaporanController extends Controller {
    public function index() {
        requireRole(['SUPERADMIN', 'ADMIN_PROVINSI', 'ADMIN_UPPD']);

        $surveyModel = $this->model('Survey');
        $unitModel = $this->model('Unit');
        $unit_id = null;
        $units = [];
        $success = '';
        $error = '';

        // Handle Action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
            if ($_SESSION['user']['role'] === 'SUPERADMIN') {
                if ($surveyModel->deleteSurvey($_POST['id'])) {
                    $success = 'Data survey berhasil dihapus.';
                } else {
                    $error = 'Gagal menghapus data survey.';
                }
            } else {
                $error = 'Anda tidak memiliki akses untuk menghapus data.';
            }
        }
        
        // Filter for Non-Superadmin
        if ($_SESSION['user']['role'] !== 'SUPERADMIN') {
            $unit_id = $_SESSION['user']['unit_id'];
        } else {
            $units = $unitModel->getAllUnits();
            if (isset($_GET['unit_filter']) && !empty($_GET['unit_filter'])) {
                $unit_id = $_GET['unit_filter'];
            }
        }

        // Handle Export
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $data = $surveyModel->getAllSurveysForExport($unit_id);
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="laporan_survey_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Tanggal', 'Nomor Polisi', 'Unit Layanan', 'Nilai Rata-rata', 'Saran']);
            
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['tanggal'],
                    $row['responden_nopol'],
                    $row['unit_nama'],
                    number_format((float)($row['nilai_rata'] ?? 0), 2),
                    $row['saran']
                ]);
            }
            fclose($output);
            exit;
        }

        // Pagination & Search
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';

        $surveys = $surveyModel->getSurveys($limit, $offset, $search, $unit_id);
        $totalRecords = $surveyModel->countSurveys($search, $unit_id);
        $totalPages = ceil($totalRecords / $limit);

        $data = [
            'title' => 'Laporan Survey',
            'surveys' => $surveys,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'units' => $units,
            'unit_filter' => $unit_id, // Pass current filter to view
            'success' => $success,
            'error' => $error
        ];

        $this->view('layout/header', $data);
        $this->view('layout/sidebar', $data);
        $this->view('laporan/index', $data);
        $this->view('layout/footer');
    }
}