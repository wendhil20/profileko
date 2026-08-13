<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <?php include ROOT_PATH . '/link/top.php'; ?>
</head>
<body class="min-h-screen bg-neutral-800 flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-md">

        <div class="bg-neutral-900 rounded-3xl shadow-2xl shadow-black/50 ring-1 ring-white/10 overflow-hidden p-8 md:p-10">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="font-pixel text-3xl font-semibold text-white">Admin Panel</h2>
                <p class="text-sm text-white/50 mt-1">Sign in to manage the system</p>
            </div>

            <?php if (!empty($_SESSION['login_error'])): ?>
    <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm text-center">
        <?= htmlspecialchars($_SESSION['login_error']) ?>
    </div>
    <?php unset($_SESSION['login_error']); ?>
<?php endif; ?>


            <!-- Form -->
            <form action="<?= BASE_URL ?>/adminhandler" method="POST" class="space-y-6">

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-white/70 mb-1.5">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        class="w-full bg-transparent text-white placeholder-white/30 px-1 py-2 text-sm border-0 border-b-2 border-white/20 focus:border-white outline-none transition-all"
                        placeholder="Enter admin username"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white/70 mb-1.5">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full bg-transparent text-white placeholder-white/30 px-1 py-2 text-sm border-0 border-b-2 border-white/20 focus:border-white outline-none transition-all"
                        placeholder="Enter admin password"
                    >
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-white text-neutral-900 font-medium rounded-xl px-4 py-3 text-sm hover:bg-white/90 active:scale-95 transition-all duration-150 mt-4"
                >
                    Sign In
                </button>

            </form>

        </div>

        <p class="text-center text-xs text-white/30 mt-6">Restricted access. Authorized personnel only.</p>

    </div>

</body>
</html>