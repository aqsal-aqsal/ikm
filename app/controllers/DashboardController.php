<?php
class DashboardController extends Controller {
    public function index() {
        requireAuth();

        $data = [
            'title' => 'Dashboard',
            'user' => $_SESSION['user']
        ];

        $surveyModel = $this->model('Survey');
        $unitModel = $this->model('Unit'); // If needed for total units

        // Total Units count
        $data['total_units'] = count($unitModel->getAllUnits());

        $unit_id = ($_SESSION['user']['role'] === 'SUPERADMIN') ? null : $_SESSION['user']['unit_id'];

        $data['total_responden'] = $surveyModel->getTotalRespondents($unit_id);
        $data['avg_index'] = $surveyModel->getAverageIndex($unit_id);
        
        // Pagination for Unit Performance
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;
        
        $data['unit_stats'] = $surveyModel->getUnitPerformance($unit_id, $limit, $offset);
        $totalRecords = $surveyModel->countUnitPerformance($unit_id);
        $totalPages = ceil($totalRecords / $limit);
        
        $data['page'] = $page;
        $data['totalPages'] = $totalPages;

        // Trend Data Processing
        $trendRaw = $surveyModel->getTrendData($unit_id);
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        
        $chartData = [];
        $counts = [];
        $averages = [];
        $labels = [];

        foreach ($months as $num => $name) {
            $count = 0;
            $avg = 0;
            
            foreach ($trendRaw as $row) {
                if ($row['month'] == $num) {
                    $count = $row['total_responden'];
                    $avg = number_format($row['avg_index'] * 25, 2); // Convert 1-4 to 25-100
                    break;
                }
            }
            
            $counts[] = $count;
            $averages[] = $avg;
            $labels[] = $name;
        }

        $data['chart'] = [
            'labels' => $labels,
            'counts' => $counts,
            'averages' => $averages
        ];

        $this->view('layout/header', $data);
        $this->view('layout/sidebar', $data);
        $this->view('dashboard/index', $data);
        $this->view('layout/footer');
    }

    public function export() {
        requireAuth();
        // Export logic
        $surveyModel = $this->model('Survey');
        
        if ($_SESSION['user']['role'] === 'SUPERADMIN') {
            $stats = $surveyModel->getUnitPerformance();
        } else {
            $stats = $surveyModel->getUnitPerformance($_SESSION['user']['unit_id']);
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="dashboard_ikm_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Unit Layanan', 'Total Responden', 'Rata-rata IKM', 'Target Achievement']);
        
        foreach ($stats as $row) {
            $avgIkm = $row['avg_nilai'] * 25; 
            $target = min(100, round($avgIkm));
            fputcsv($output, [
                $row['unit_nama'], 
                $row['count'], 
                number_format($avgIkm, 2), 
                $target . '%'
            ]);
        }
        fclose($output);
        exit;
    }
}
