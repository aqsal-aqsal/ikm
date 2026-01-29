<?php

class SurveyController extends Controller {
    public function index() {
        $data = [
            'title' => 'Survei Kepuasan Masyarakat',
            'questions' => [],
            'units' => [],
            'success' => false,
            'error' => ''
        ];

        $surveyModel = $this->model('Survey');
        $unitModel = $this->model('Unit');

        // Handle Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($surveyModel->createSurvey($_POST)) {
                $data['success'] = true;
            } else {
                $data['error'] = 'Gagal mengirim survei. Silakan coba lagi.';
            }
        }

        $data['questions'] = $surveyModel->getQuestions();
        $data['units'] = $unitModel->getAllUnits();
        
        // Pre-select unit if provided in URL or from Session
        $data['selected_unit_id'] = isset($_GET['unit_id']) ? $_GET['unit_id'] : (isset($_SESSION['user']['unit_id']) ? $_SESSION['user']['unit_id'] : null);
        
        $data['selected_unit_name'] = null;
        if ($data['selected_unit_id']) {
            foreach ($data['units'] as $u) {
                if ($u['id'] == $data['selected_unit_id']) {
                    $data['selected_unit_name'] = $u['nama'];
                    break;
                }
            }
        }

        // Since this is a public page, we might not use the standard layout (header/sidebar/footer) 
        // but rather a standalone layout like the original survey.php.
        // I will create a specific view for this.
        $this->view('survey/index', $data);
    }
}