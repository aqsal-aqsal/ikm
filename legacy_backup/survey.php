<?php
require_once 'includes/config.php';

$success = false;
$error = '';

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Insert Responden
        $nama = $_POST['nama'] ?? '';
        $umur = $_POST['umur'] ?? 0;
        $jk = $_POST['jk'] ?? 'L';
        $pendidikan = $_POST['pendidikan'] ?? '';
        $pekerjaan = $_POST['pekerjaan'] ?? '';
        $unit_id = $_POST['unit_id'] ?? null;
        
        // Validation
        if (!$nama || !$umur || !$unit_id) {
            throw new Exception("Mohon lengkapi data diri Anda.");
        }

        $stmt = $pdo->prepare("INSERT INTO responden (nama, umur, jk, pendidikan, pekerjaan, unit_id, tanggal_survey) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
        $stmt->execute([$nama, $umur, $jk, $pendidikan, $pekerjaan, $unit_id]);
        $responden_id = $pdo->lastInsertId();

        // 2. Insert Survey
        $saran = $_POST['saran'] ?? '';
        $jawaban = $_POST['jawaban'] ?? []; // Array of [unsur_id => nilai]
        
        if (empty($jawaban)) {
            throw new Exception("Mohon isi semua pertanyaan survei.");
        }

        // Calculate Score
        $total_nilai = 0;
        $count = 0;
        foreach ($jawaban as $val) {
            $total_nilai += (int)$val;
            $count++;
        }
        
        // Indeks logic (avg * 25 to make it 25-100 scale)
        // If answers are 1-4.
        $avg = $count > 0 ? ($total_nilai / $count) : 0;
        // $indeks = $avg * 25; // If we want 100 scale
        $indeks = $avg; // Storing raw 1-4 scale is often better, but let's check dashboard.
        // Dashboard code: $ikmScore = $avgNilai ? ($avgNilai * 25) : 0;
        // This implies DB stores 1-4 scale. So I will store raw average.
        
        // Determine Category (Permenpan RB 14/2017 style or simplified)
        // 1.00 - 2.5996 : D (Tidak Baik)
        // 2.60 - 3.064 : C (Kurang Baik)
        // 3.0644 - 3.532 : B (Baik)
        // 3.5324 - 4.00 : A (Sangat Baik)
        // Using simplified logic for now
        if ($indeks >= 3.53) $kategori = 'A';
        elseif ($indeks >= 3.06) $kategori = 'B';
        elseif ($indeks >= 2.60) $kategori = 'C';
        else $kategori = 'D';

        $stmt = $pdo->prepare("INSERT INTO survey (responden_id, unit_id, tanggal, saran, total_nilai, indeks, kategori) VALUES (?, ?, CURDATE(), ?, ?, ?, ?)");
        $stmt->execute([$responden_id, $unit_id, $saran, $total_nilai, $indeks, $kategori]);
        $survey_id = $pdo->lastInsertId();

        // 3. Insert Answers
        $stmt = $pdo->prepare("INSERT INTO survey_jawaban (survey_id, unsur_ikm_id, nilai) VALUES (?, ?, ?)");
        foreach ($jawaban as $unsur_id => $nilai) {
            $stmt->execute([$survey_id, $unsur_id, $nilai]);
        }

        $pdo->commit();
        $success = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Fetch Units
$units = $pdo->query("SELECT * FROM units ORDER BY nama ASC")->fetchAll();

// Fetch Questions (Unsur IKM)
$questions = $pdo->query("SELECT * FROM unsur_ikm ORDER BY id ASC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survei Kepuasan Masyarakat - IKM Bapenda Kalsel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf3', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d'
                        }
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        };
    </script>
    <style>
        .option-card:hover { transform: translateY(-4px); }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="max-w-3xl mx-auto min-h-[600px] flex flex-col justify-center py-10 px-4">
        
        <?php if ($success): ?>
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-10 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Terima Kasih!</h2>
            <p class="text-slate-500 mb-8">Survei Anda telah berhasil dikirim. Masukan Anda sangat berharga bagi peningkatan layanan kami.</p>
            <a href="survey.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition">
                Isi Survei Lagi
            </a>
        </div>
        <?php else: ?>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Survei Kepuasan Masyarakat</h1>
            <p class="text-slate-500 mt-2">Bantu kami meningkatkan pelayanan dengan penilaian Anda.</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Wizard -->
        <form method="POST" id="survey-form" class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden relative min-h-[400px]">
            <!-- Progress Bar -->
            <div class="absolute top-0 left-0 w-full h-1.5 bg-slate-100">
                <div id="progress-bar" class="h-full bg-emerald-500 transition-all duration-500 ease-out w-0"></div>
            </div>

            <!-- Step 1: Data Responden -->
            <div id="step-data" class="p-8 md:p-10 space-y-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-slate-900">Data Responden</h2>
                    <p class="text-sm text-slate-500">Silakan lengkapi identitas Anda sebelum memulai.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                        <input name="nama" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" required value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Umur</label>
                        <input name="umur" type="number" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" required value="<?php echo htmlspecialchars($_POST['umur'] ?? ''); ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Jenis Kelamin</label>
                        <select name="jk" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pendidikan Terakhir</label>
                        <select name="pendidikan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" required>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pekerjaan</label>
                    <input name="pekerjaan" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" required value="<?php echo htmlspecialchars($_POST['pekerjaan'] ?? ''); ?>">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Unit Layanan</label>
                    <select name="unit_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" required>
                        <option value="">Pilih Unit...</option>
                        <?php foreach ($units as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pt-4">
                    <button type="button" onclick="nextStep()" class="w-full bg-emerald-600 text-white font-semibold py-3.5 px-6 rounded-xl hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 transition duration-200 shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
                        Mulai Survei
                    </button>
                </div>
            </div>

            <!-- Step 2: Questions Loop -->
            <div id="step-questions" class="hidden p-8 md:p-10 flex flex-col items-center justify-center min-h-[400px]">
                <div class="text-center w-full max-w-2xl mb-8">
                    <span id="q-counter" class="text-xs font-bold tracking-widest text-emerald-600 uppercase mb-2 block">Pertanyaan 1 dari <?php echo count($questions); ?></span>
                    <h3 id="q-text" class="text-xl md:text-2xl font-bold text-slate-900 leading-snug">
                        <!-- Question Text populated by JS -->
                    </h3>
                </div>

                <!-- Hidden inputs for answers -->
                <?php foreach ($questions as $q): ?>
                    <input type="hidden" name="jawaban[<?php echo $q['id']; ?>]" id="input-q-<?php echo $q['id']; ?>" value="">
                <?php endforeach; ?>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
                    <?php 
                    $options = [
                        ['val' => 1, 'label' => 'Sangat Buruk', 'icon' => '😡', 'color' => 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100'],
                        ['val' => 2, 'label' => 'Buruk', 'icon' => '🙁', 'color' => 'bg-orange-50 border-orange-200 text-orange-600 hover:bg-orange-100'],
                        ['val' => 3, 'label' => 'Baik', 'icon' => '🙂', 'color' => 'bg-sky-50 border-sky-200 text-sky-600 hover:bg-sky-100'],
                        ['val' => 4, 'label' => 'Sangat Baik', 'icon' => '😍', 'color' => 'bg-emerald-50 border-emerald-200 text-emerald-600 hover:bg-emerald-100']
                    ];
                    foreach ($options as $opt): ?>
                    <button type="button" onclick="answerQuestion(<?php echo $opt['val']; ?>)"
                        class="option-card flex flex-col items-center justify-center p-3 rounded-2xl border-2 <?php echo $opt['color']; ?> transition duration-200 h-32 md:h-40 w-full group shadow-sm hover:shadow-md">
                        <span class="text-3xl md:text-4xl mb-3 group-hover:scale-110 transition-transform"><?php echo $opt['icon']; ?></span>
                        <span class="text-xs font-bold text-center leading-tight uppercase tracking-wide"><?php echo $opt['label']; ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 3: Saran & Finish -->
            <div id="step-finish" class="hidden p-8 md:p-10 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Hampir Selesai!</h2>
                <p class="text-slate-500 mb-6">Ada kritik atau saran tambahan untuk pelayanan kami?</p>
                
                <textarea name="saran" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition mb-6" placeholder="Tulis saran Anda di sini (opsional)..."></textarea>
                
                <button type="submit" class="w-full bg-emerald-600 text-white font-semibold py-3.5 px-6 rounded-xl hover:bg-emerald-700 transition duration-200 shadow-lg shadow-emerald-200">
                    Kirim Survey
                </button>
            </div>

        </form>
        <?php endif; ?>
    </div>

    <script>
        // Questions Data
        const questions = <?php echo json_encode(array_map(function($q) {
            return ['id' => $q['id'], 'text' => $q['nama_unsur']];
        }, $questions)); ?>;
        
        let currentQIndex = 0;

        function nextStep() {
            // Validate Step 1
            const form = document.getElementById('survey-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            document.getElementById('step-data').classList.add('hidden');
            document.getElementById('step-questions').classList.remove('hidden');
            renderQuestion();
        }

        function renderQuestion() {
            const q = questions[currentQIndex];
            document.getElementById('q-text').textContent = q.text;
            document.getElementById('q-counter').textContent = `PERTANYAAN ${currentQIndex + 1} DARI ${questions.length}`;
            
            // Update progress
            const pct = ((currentQIndex + 1) / questions.length) * 100;
            document.getElementById('progress-bar').style.width = `${pct}%`;
        }

        function answerQuestion(val) {
            const q = questions[currentQIndex];
            
            // Set hidden input value
            document.getElementById(`input-q-${q.id}`).value = val;

            if (currentQIndex < questions.length - 1) {
                currentQIndex++;
                renderQuestion();
            } else {
                document.getElementById('step-questions').classList.add('hidden');
                document.getElementById('step-finish').classList.remove('hidden');
                document.getElementById('progress-bar').style.width = '100%';
            }
        }
    </script>
</body>
</html>
