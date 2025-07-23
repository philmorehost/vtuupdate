<?php
require_once('includes/db.php');
$stmt = $pdo->prepare("SELECT * FROM site_settings WHERE id = ?");
$stmt->execute([1]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= htmlspecialchars($settings['site_name'] ?? '') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <div class="hidden md:block w-1/2 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($settings['auth_image'] ?? 'assets/images/auth-bg.jpg') ?>');"></div>
        <div class="w-full md:w-1/2 flex items-center justify-center">
            <div class="w-full max-w-md p-8">
                <form action="auth_user.php?action=register" method="POST" class="bg-white shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4" style="box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                    <div class="flex justify-center mb-6">
                        <?php if (!empty($settings['site_logo'])): ?>
                            <img src="<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="h-16">
                        <?php else: ?>
                            <h2 class="text-2xl font-bold text-center"><?= htmlspecialchars($settings['site_name'] ?? 'VTU Platform') ?></h2>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-center mb-6">Create Account</h2>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Full Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" type="text" placeholder="Full Name" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" name="email" type="email" placeholder="Email" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                            Phone Number
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="phone" name="phone" type="tel" placeholder="Phone Number" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="******************" required autocomplete="new-password">
                        <div class="mt-2">
                            <div class="h-2 bg-gray-200 rounded-full">
                                <div id="password-strength-bar" class="h-2 rounded-full" style="width: 0;"></div>
                            </div>
                            <p id="password-strength-text" class="text-sm mt-1"></p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                            Register
                        </button>
                        <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="login.php">
                            Login
                        </a>
                    </div>
                    <input type="hidden" name="ref" value="<?php echo htmlspecialchars($_GET['ref'] ?? ''); ?>">
                </form>
            </div>
        </div>
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const passwordStrengthBar = document.getElementById('password-strength-bar');
        const passwordStrengthText = document.getElementById('password-strength-text');

        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            let barColor = '';
            let strengthText = '';

            switch (strength) {
                case 0:
                case 1:
                    barColor = 'bg-red-500';
                    strengthText = 'Weak';
                    break;
                case 2:
                    barColor = 'bg-yellow-500';
                    strengthText = 'Medium';
                    break;
                case 3:
                case 4:
                case 5:
                    barColor = 'bg-green-500';
                    strengthText = 'Strong';
                    break;
            }

            passwordStrengthBar.style.width = (strength * 20) + '%';
            passwordStrengthBar.className = 'h-2 rounded-full ' + barColor;
            passwordStrengthText.innerText = 'Password Strength: ' + strengthText;
        });
    </script>
</body>
</html>
