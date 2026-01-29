<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IKM Bapenda Kalsel</title>
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
                            50: '#ecfdf3',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d'
                        },
                        accent: '#facc15'
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?= BASEURL; ?>/assets/css/app.css">
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo / Icon -->
            <img src="<?= BASEURL; ?>/assets/img/bapendalogo.png" alt="Logo" class="mx-auto w-64 h-auto">
            
            <!-- Headings -->
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-slate-900">
                Aplikasi IKM
            </h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                Silahkan Masuk!
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[400px]">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-slate-100">
                <form action="<?= BASEURL; ?>/auth/login" method="POST" class="space-y-6">
                    <!-- Username Input -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700">
                            Username
                        </label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" placeholder="Enter your username" required 
                                class="block w-full rounded-md border border-slate-300 px-3 py-2 placeholder-slate-400 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-sky-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" placeholder="••••••••" required 
                                class="block w-full rounded-md border border-slate-300 px-3 py-2 placeholder-slate-400 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-sky-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Error Message -->
                    <?php if (!empty($data['error'])): ?>
                    <div class="rounded-md bg-red-50 p-2">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($data['error']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-sky-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition-colors">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
