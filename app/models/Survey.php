<?php
class Survey {
    private $table = 'survey';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }
    
    public function getTotalRespondents($unit_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        if ($unit_id) {
            $query .= " WHERE unit_id = :unit_id";
        }
        $this->db->query($query);
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        $row = $this->db->single();
        return $row['count'];
    }

    public function getAverageIndex($unit_id = null) {
        $query = "SELECT AVG(indeks) as avg_index FROM " . $this->table;
        if ($unit_id) {
            $query .= " WHERE unit_id = :unit_id";
        }
        $this->db->query($query);
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        $row = $this->db->single();
        return $row['avg_index'];
    }
    
    public function getUnitPerformance($unit_id = null, $limit = null, $offset = 0) {
        $query = "
            SELECT u.nama as unit_nama, 
                   COUNT(s.id) as count, 
                   COALESCE(AVG(s.indeks), 0) as avg_nilai 
            FROM units u 
            LEFT JOIN survey s ON u.id = s.unit_id 
            WHERE 1=1
        ";
        
        if ($unit_id) {
            $query .= " AND u.id = :unit_id";
        }
        
        $query .= " GROUP BY u.id, u.nama ORDER BY avg_nilai DESC";

        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $this->db->query($query);
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        if ($limit) {
            $this->db->bind('limit', $limit, PDO::PARAM_INT);
            $this->db->bind('offset', $offset, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet();
    }
    
    public function countUnitPerformance($unit_id = null) {
        // Since we are grouping by unit, count(*) would return number of groups.
        // If unit_id is set, it's 1 (or 0 if not exists).
        // If unit_id is null, it's total units.
        // However, the original query uses LEFT JOIN from units. So it counts units.
        
        $query = "SELECT COUNT(*) as count FROM units u WHERE 1=1";
        
        if ($unit_id) {
            $query .= " AND u.id = :unit_id";
        }
        
        $this->db->query($query);
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        
        $row = $this->db->single();
        return $row['count'];
    }
    
    public function getTrendData($unit_id = null) {
        $query = "
            SELECT 
                MONTH(tanggal) as month,
                COUNT(*) as total_responden,
                AVG(indeks) as avg_index
            FROM " . $this->table . "
            WHERE YEAR(tanggal) = YEAR(CURDATE())
        ";

        if ($unit_id) {
            $query .= " AND unit_id = :unit_id";
        }

        $query .= " GROUP BY MONTH(tanggal) ORDER BY MONTH(tanggal) ASC";

        $this->db->query($query);
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        
        return $this->db->resultSet();
    }
    
    public function getSurveys($limit, $offset, $search = '', $unit_id = null) {
        $query = "
            SELECT 
                s.*, 
                u.nama as unit_nama,
                r.nomor_polisi as responden_nopol
            FROM survey s 
            LEFT JOIN units u ON s.unit_id = u.id 
            LEFT JOIN responden r ON s.responden_id = r.id
            WHERE 1=1
        ";

        if ($unit_id) {
            $query .= " AND s.unit_id = :unit_id";
        }
        
        if ($search) {
            $query .= " AND (r.nomor_polisi LIKE :search OR u.nama LIKE :search)";
        }

        $query .= " ORDER BY s.tanggal DESC LIMIT :limit OFFSET :offset";

        $this->db->query($query);
        $this->db->bind('limit', $limit, PDO::PARAM_INT);
        $this->db->bind('offset', $offset, PDO::PARAM_INT);
        
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        if ($search) $this->db->bind('search', "%$search%");

        return $this->db->resultSet();
    }

    public function countSurveys($search = '', $unit_id = null) {
        $query = "
            SELECT COUNT(*) as count 
            FROM survey s 
            LEFT JOIN units u ON s.unit_id = u.id 
            LEFT JOIN responden r ON s.responden_id = r.id
            WHERE 1=1
        ";

        if ($unit_id) {
            $query .= " AND s.unit_id = :unit_id";
        }
        
        if ($search) {
            $query .= " AND (r.nomor_polisi LIKE :search OR u.nama LIKE :search)";
        }

        $this->db->query($query);
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        if ($search) $this->db->bind('search', "%$search%");
        
        $row = $this->db->single();
        return $row['count'];
    }

    public function getAllSurveysForExport($unit_id = null) {
        $query = "
            SELECT 
                s.tanggal,
                r.nomor_polisi as responden_nopol,
                u.nama as unit_nama,
                s.indeks as nilai_rata,
                s.saran
            FROM survey s
            JOIN responden r ON s.responden_id = r.id
            JOIN units u ON s.unit_id = u.id
        ";

        if ($unit_id) {
            $query .= " WHERE u.id = :unit_id";
        }
        
        $query .= " ORDER BY s.tanggal DESC";

        $this->db->query($query);
        if ($unit_id) $this->db->bind('unit_id', $unit_id);
        
        return $this->db->resultSet();
    }

    public function createSurvey($data) {
        try {
            $this->db->beginTransaction();

            $this->db->query("INSERT INTO responden (nomor_polisi, unit_id, tanggal_survey) VALUES (:nomor_polisi, :unit_id, CURDATE())");
            $this->db->bind('nomor_polisi', $data['nomor_polisi'] ?? '');
            $this->db->bind('unit_id', $data['unit_id']);
            $this->db->execute();
            $responden_id = $this->db->lastInsertId();

            $total_nilai = 0;
            $count = 0;
            foreach ($data['jawaban'] as $val) {
                $total_nilai += (int)$val;
                $count++;
            }
            
            $avg = $count > 0 ? ($total_nilai / $count) : 0;
            $indeks = $avg;
            
            if ($indeks >= 3.5324) $kategori = 'A';
            elseif ($indeks >= 3.0644) $kategori = 'B';
            elseif ($indeks >= 2.60) $kategori = 'C';
            else $kategori = 'D';

            $this->db->query("INSERT INTO survey (responden_id, unit_id, tanggal, saran, total_nilai, indeks, kategori) VALUES (:responden_id, :unit_id, CURDATE(), :saran, :total_nilai, :indeks, :kategori)");
            $this->db->bind('responden_id', $responden_id);
            $this->db->bind('unit_id', $data['unit_id']);
            $this->db->bind('saran', $data['saran'] ?? null);
            $this->db->bind('total_nilai', $total_nilai);
            $this->db->bind('indeks', $indeks);
            $this->db->bind('kategori', $kategori);
            $this->db->execute();
            $survey_id = $this->db->lastInsertId();

            $this->db->query("INSERT INTO survey_jawaban (survey_id, unsur_ikm_id, nilai) VALUES (:survey_id, :unsur_ikm_id, :nilai)");
            foreach ($data['jawaban'] as $unsur_id => $nilai) {
                $this->db->bind('survey_id', $survey_id);
                $this->db->bind('unsur_ikm_id', $unsur_id);
                $this->db->bind('nilai', (int)$nilai);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteSurvey($id) {
        try {
            $this->db->beginTransaction();

            // Get respondent_id first
            $this->db->query("SELECT responden_id FROM survey WHERE id = :id");
            $this->db->bind('id', $id);
            $row = $this->db->single();
            $responden_id = $row['responden_id'] ?? null;

            // Delete answers
            $this->db->query("DELETE FROM survey_jawaban WHERE survey_id = :id");
            $this->db->bind('id', $id);
            $this->db->execute();

            // Delete survey
            $this->db->query("DELETE FROM survey WHERE id = :id");
            $this->db->bind('id', $id);
            $this->db->execute();

            // Delete responden if exists
            if ($responden_id) {
                $this->db->query("DELETE FROM responden WHERE id = :id");
                $this->db->bind('id', $responden_id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getQuestions() {
        $this->db->query("SELECT * FROM unsur_ikm ORDER BY id ASC");
        return $this->db->resultSet();
    }
}
