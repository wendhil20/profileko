<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php include ROOT_PATH . '/link/top.php'; ?>
</head>
<body class="min-h-screen bg-neutral-800 flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-4xl flex flex-col md:flex-row rounded-3xl shadow-2xl shadow-black/50 ring-1 ring-white/10 overflow-hidden">

        <!-- LEFT: Info / Branding panel (Minecraft style) -->
        <div class="w-full md:w-1/2 bg-neutral-900 flex flex-col items-center justify-center text-center p-10 relative overflow-hidden">

            <!-- subtle pixel grid background accent -->
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: linear-gradient(white 1px, transparent 1px), linear-gradient(90deg, white 1px, transparent 1px); background-size: 24px 24px;">
            </div>

            <div class="relative z-10">
                <h1 class="font-pixel text-4xl md:text-5xl text-white mb-3 tracking-wide"
                    style="text-shadow: 3px 3px 0 rgba(0,0,0,0.6), -1px -1px 0 rgba(0,0,0,0.3);">
                    PORTFOLIO DESEASE
                </h1>
                <p class="text-sm text-white/50 max-w-xs mx-auto leading-relaxed mb-6">
                    Welcome back, builder. Sign in showcase your work.
                </p>

                <!-- Demo credentials -->
                <div class="inline-flex items-center gap-3 text-left">

                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-white/70">
                            <span class="text-white/40">Username:</span>
                            <span id="demo-username" class="text-white font-mono blur-sm select-none transition-all duration-200">wen</span>
                        </p>
                        <p class="text-sm text-white/70">
                            <span class="text-white/40">Password:</span>
                            <span id="demo-password" class="text-white font-mono blur-sm select-none transition-all duration-200">portwen</span>
                        </p>
                    </div>

                    <!-- Arrow/eye button: reveal credentials -->
                    <button
                        type="button"
                        onclick="revealCredentials()"
                        title="Show demo account"
                        class="flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all duration-150 active:scale-90"
                    >
                       <i class="fa-solid fa-circle-info"></i>
                    </button>

                </div>
            </div>

        </div>
        <!-- RIGHT: Login form -->
        <div class="w-full md:w-1/2 bg-neutral-900 border-t md:border-t-0 md:border-l border-white/10 flex items-center justify-center p-8 md:p-10">

            <div class="w-full max-w-sm">

                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="font-pixel text-3xl font-semibold text-white">Jr Dev 2026</h2>
                    <p class="text-sm text-white/50 mt-1">Sign in to your account</p>
                </div>

                <!-- Form -->
                <form action="<?= BASE_URL ?>/login" method="POST" class="space-y-6">

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-white/70 mb-1.5">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            required
                            class="w-full bg-transparent text-white placeholder-white/30 px-1 py-2 text-sm border-0 border-b-2 border-white/20 focus:border-white outline-none transition-all"
                            placeholder="Enter your username"
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
                            placeholder="Enter your password"
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

        </div>

    </div>

    <script>
  function revealCredentials() {
    document.getElementById('demo-username').classList.toggle('blur-sm');
    document.getElementById('demo-username').classList.toggle('select-none');
    document.getElementById('demo-password').classList.toggle('blur-sm');
    document.getElementById('demo-password').classList.toggle('select-none');
}
    </script>

</body>
</html>