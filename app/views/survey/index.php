<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title']; ?> - IKM Bapenda Kalsel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e'
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
        .swal2-popup { font-family: 'Inter', sans-serif; border-radius: 1.5rem; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="max-w-3xl mx-auto min-h-[600px] flex flex-col justify-center py-10 px-4">
        
        <?php if ($data['success']): ?>
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-10 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-sky-100 text-sky-600 mb-6 animate-bounce">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Terima Kasih!</h2>
            <p class="text-slate-500 mb-8">Survei Anda telah berhasil dikirim. Masukan Anda sangat berharga bagi peningkatan layanan kami.</p>
            <a href="<?= BASEURL; ?>/survey" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-sky-500 hover:bg-sky-600 transition">
                Isi Survei Lagi
            </a>
        </div>
        <script>
            // Celebration Confetti
            const duration = 3000;
            const animationEnd = Date.now() + duration;
            const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

            function random(min, max) { return Math.random() * (max - min) + min; }

            const interval = setInterval(function() {
                const timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) { return clearInterval(interval); }
                const particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
            
            Swal.fire({
                title: 'Terima Kasih!',
                text: 'Survei Anda telah kami terima.',
                icon: 'success',
                confirmButtonColor: '#0ea5e9',
                confirmButtonText: 'Tutup'
            });
        </script>
        <?php else: ?>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Survei Kepuasan Masyarakat</h1>
            <?php if (isset($data['selected_unit_name']) && $data['selected_unit_name']): ?>
                <div class="inline-flex items-center gap-2 mt-2 px-4 py-1.5 bg-sky-100 text-sky-700 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <?= htmlspecialchars($data['selected_unit_name']); ?>
                </div>
            <?php else: ?>
                <p class="text-slate-500 mt-2">Bantu kami meningkatkan pelayanan dengan penilaian Anda.</p>
            <?php endif; ?>
        </div>

        <?php if ($data['error']): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($data['error']); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Wizard -->
        <form action="<?= BASEURL; ?>/survey" method="POST" id="survey-form" class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden relative min-h-[400px]">
            <!-- Progress Bar -->
            <div class="absolute top-0 left-0 w-full h-1.5 bg-slate-100">
                <div id="progress-bar" class="h-full bg-sky-500 transition-all duration-500 ease-out w-0"></div>
            </div>

            <!-- Step 1: Data Responden -->
            <div id="step-data" class="p-8 md:p-10 space-y-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-sky-100 text-sky-600 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-slate-900">Data Responden</h2>
                    <p class="text-sm text-slate-500">Silakan masukkan Nomor Polisi kendaraan Anda.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-4 uppercase tracking-wide text-center">Nomor Polisi</label>
                    
                    <!-- License Plate Container -->
                    <div class="bg-white border-[6px] border-slate-900 rounded-3xl p-3 w-full max-w-[340px] mx-auto shadow-2xl relative transform transition-transform hover:scale-105 duration-300">
                        <!-- Top Row: DA 1234 ABC -->
                        <div class="flex items-center justify-center gap-3 mt-2 mb-1">
                            <!-- Fixed DA -->
                            <div class="text-4xl font-bold font-mono text-slate-900 select-none tracking-wider w-14 text-center">DA</div>
                            
                            <!-- Numbers Input -->
                            <input type="text" id="nopol_2" class="bg-transparent text-4xl font-bold font-mono text-slate-900 text-center w-28 focus:outline-none border-b-2 border-transparent focus:border-slate-900 transition placeholder-slate-200 tracking-widest" placeholder="1234" maxlength="4" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length === 4) document.getElementById('nopol_3').focus();" required>
                            
                            <!-- Suffix Input -->
                            <input type="text" id="nopol_3" class="bg-transparent text-4xl font-bold font-mono text-slate-900 text-center w-20 focus:outline-none border-b-2 border-transparent focus:border-slate-900 transition placeholder-slate-200 tracking-widest uppercase" placeholder="XX" maxlength="3" oninput="this.value = this.value.toUpperCase();" required>
                        </div>
                        
                        <!-- Bottom Row: Validity Date (Decorative) -->
                        <div class="flex justify-between items-end mt-1 px-4 pb-1">
                            <!-- Side Emblem -->
                            <div class="opacity-80">
                                 <svg class="w-6 h-6 text-slate-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"/></svg>
                            </div>

                            <span class="text-sm font-bold font-mono text-slate-900 tracking-widest mr-8">
                                XX.XX
                            </span>
                            
                            <div class="w-6"></div> <!-- Spacer for balance -->
                        </div>
                    </div>
                    
                    <p class="mt-4 text-xs text-slate-400 text-center">Masukkan nomor dan huruf belakang plat kendaraan Anda.</p>
                    
                    <!-- Hidden field to store combined value -->
                    <input type="hidden" name="nomor_polisi" id="nomor_polisi">
                </div>

                <!-- Unit Layanan Selection (Hidden if pre-selected) -->
                <?php if (isset($data['selected_unit_id']) && $data['selected_unit_id']): ?>
                    <input type="hidden" name="unit_id" value="<?= htmlspecialchars($data['selected_unit_id']); ?>">
                <?php else: ?>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Unit Layanan</label>
                        <select name="unit_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition" required>
                            <option value="">Pilih Unit...</option>
                            <?php foreach ($data['units'] as $u): ?>
                            <option value="<?= $u['id']; ?>"><?= htmlspecialchars($u['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="pt-4">
                    <button type="button" onclick="nextStep()" class="w-full bg-sky-500 text-white font-semibold py-3.5 px-6 rounded-xl hover:bg-sky-600 focus:ring-4 focus:ring-sky-200 transition duration-200 shadow-lg shadow-sky-200 transform hover:-translate-y-0.5">
                        Mulai Survei
                    </button>
                </div>
            </div>

            <!-- Step 2: Questions Loop -->
            <div id="step-questions" class="hidden p-8 md:p-10 flex flex-col items-center justify-center min-h-[400px]">
                <div class="text-center w-full max-w-2xl mb-8">
                    <span id="q-counter" class="text-xs font-bold tracking-widest text-sky-600 uppercase mb-2 block">Pertanyaan 1 dari <?= count($data['questions']); ?></span>
                    <h3 id="q-text" class="text-xl md:text-2xl font-bold text-slate-900 leading-snug">
                        <!-- Question Text populated by JS -->
                    </h3>
                </div>

                <!-- Hidden inputs for answers -->
                <?php foreach ($data['questions'] as $q): ?>
                    <input type="hidden" name="jawaban[<?= $q['id']; ?>]" id="input-q-<?= $q['id']; ?>" value="">
                <?php endforeach; ?>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
                    <?php 
                    $options = [
                        ['val' => 1, 'label' => 'Sangat Buruk', 'icon' => '😡', 'color' => 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100'],
                        ['val' => 2, 'label' => 'Buruk', 'icon' => '🙁', 'color' => 'bg-orange-50 border-orange-200 text-orange-600 hover:bg-orange-100'],
                        ['val' => 3, 'label' => 'Baik', 'icon' => '🙂', 'color' => 'bg-emerald-50 border-emerald-200 text-emerald-600 hover:bg-emerald-100'],
                        ['val' => 4, 'label' => 'Sangat Baik', 'icon' => '😍', 'color' => 'bg-sky-50 border-sky-200 text-sky-600 hover:bg-sky-100']
                    ];
                    foreach ($options as $opt): ?>
                    <button type="button" onclick="answerQuestion(<?= $opt['val']; ?>)"
                        class="option-card flex flex-col items-center justify-center p-3 rounded-2xl border-2 <?= $opt['color']; ?> transition duration-200 h-32 md:h-40 w-full group shadow-sm hover:shadow-md">
                        <span class="text-3xl md:text-4xl mb-3 group-hover:scale-110 transition-transform"><?= $opt['icon']; ?></span>
                        <span class="text-xs font-bold text-center leading-tight uppercase tracking-wide"><?= $opt['label']; ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 3: Saran & Finish -->
            <div id="step-finish" class="hidden p-8 md:p-10 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-100 text-sky-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Hampir Selesai!</h2>
                <p class="text-slate-500 mb-6">Ada kritik atau saran tambahan untuk pelayanan kami?</p>
                
                <textarea name="saran" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition mb-6" placeholder="Tulis saran Anda di sini (opsional)..."></textarea>
                
                <button type="submit" class="w-full bg-sky-500 text-white font-semibold py-3.5 px-6 rounded-xl hover:bg-sky-600 transition duration-200 shadow-lg shadow-sky-200">
                    Kirim Survey
                </button>
            </div>

        </form>
        
        <div class="mt-8 text-center">
            <a href="<?= BASEURL; ?>/auth/login" class="text-sm text-slate-400 hover:text-slate-600 transition-colors">
                Login Administrator
            </a>
        </div>
        <script>
            // Welcome Animation
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Selamat Datang!',
                    text: 'Mari bantu kami meningkatkan pelayanan.',
                    icon: 'info',
                    timer: 2000,
                    showConfirmButton: false,
                    backdrop: `rgba(0,0,123,0.4)`
                }).then(() => {
                    // Festive confetti on start
                    const count = 200;
                    const defaults = {
                        origin: { y: 0.7 }
                    };

                    function fire(particleRatio, opts) {
                        confetti(Object.assign({}, defaults, opts, {
                            particleCount: Math.floor(count * particleRatio)
                        }));
                    }

                    fire(0.25, { spread: 26, startVelocity: 55, });
                    fire(0.2, { spread: 60, });
                    fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
                    fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
                    fire(0.1, { spread: 120, startVelocity: 45, });
                });
            });
        </script>
        <?php endif; ?>
    </div>

    <script>
        // Questions Data
        const questions = <?= json_encode(array_map(function($q) {
            return ['id' => $q['id'], 'text' => $q['nama_unsur']];
        }, $data['questions'])); ?>;
        
        let currentQIndex = 0;
        const totalQuestions = questions.length;

        function nextStep() {
            // Combine Nopol
            const n2 = document.getElementById('nopol_2').value;
            const n3 = document.getElementById('nopol_3').value;
            document.getElementById('nomor_polisi').value = `DA ${n2} ${n3}`.trim();

            // Validate Step 1
            const form = document.getElementById('survey-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Welcome Animation
            Swal.fire({
                title: 'Selamat Datang!',
                text: 'Mari mulai pengisian survei.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
                backdrop: `rgba(0,0,123,0.4)`
            }).then(() => {
                document.getElementById('step-data').classList.add('hidden');
                document.getElementById('step-questions').classList.remove('hidden');
                renderQuestion();
                updateProgressBar();
            });
        }

        function renderQuestion() {
            const q = questions[currentQIndex];
            document.getElementById('q-text').textContent = q.text;
            document.getElementById('q-counter').textContent = `Pertanyaan ${currentQIndex + 1} dari ${totalQuestions}`;
        }

        function answerQuestion(val) {
            // Save answer
            const q = questions[currentQIndex];
            document.getElementById(`input-q-${q.id}`).value = val;

            // Next question
            currentQIndex++;
            updateProgressBar();

            if (currentQIndex < totalQuestions) {
                renderQuestion();
            } else {
                // Finish questions, show saran
                document.getElementById('step-questions').classList.add('hidden');
                document.getElementById('step-finish').classList.remove('hidden');
            }
        }

        function updateProgressBar() {
            let percent = 0;
            if (currentQIndex === 0 && document.getElementById('step-data').classList.contains('hidden')) {
                percent = 10;
            } else if (currentQIndex > 0) {
                percent = 10 + ((currentQIndex / totalQuestions) * 80);
            }
            
            if (!document.getElementById('step-finish').classList.contains('hidden')) {
                percent = 100;
            }

            document.getElementById('progress-bar').style.width = `${percent}%`;
        }
    </script>
</body>
</html>