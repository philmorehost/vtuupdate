<?php
$title = 'Site Settings';
require_once('includes/header.php');
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <form action="site_settings_actions.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="site_name">Site Name</label>
            <input type="text" name="site_name" id="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'My VTU') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="site_logo">Site Logo</label>
            <input type="file" name="site_logo" id="site_logo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            <?php if (!empty($settings['site_logo'])): ?>
                <img src="../<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="mt-2 h-16">
            <?php endif; ?>
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="auth_image">Auth Image</label>
            <input type="file" name="auth_image" id="auth_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            <p class="text-xs text-gray-500 mt-1">Recommended size: 400x400 pixels</p>
            <?php if (!empty($settings['auth_image'])): ?>
                <img src="../<?= htmlspecialchars($settings['auth_image']) ?>" alt="Auth Image" class="mt-2 h-16 w-auto" style="max-height: 64px;">
            <?php endif; ?>
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="session_timeout">Session Timeout (minutes)</label>
            <input type="number" name="session_timeout" id="session_timeout" value="<?= htmlspecialchars($settings['session_timeout'] ?? 30) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="cache_control">Cache Control</label>
            <select name="cache_control" id="cache_control" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                <option value="no-cache" <?= ($settings['cache_control'] ?? '') === 'no-cache' ? 'selected' : '' ?>>No Cache</option>
                <option value="public, max-age=3600" <?= ($settings['cache_control'] ?? '') === 'public, max-age=3600' ? 'selected' : '' ?>>Cache for 1 hour</option>
            </select>
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="referral_bonus_tier1">Referral Bonus Tier 1 (%)</label>
            <input type="number" step="0.01" name="referral_bonus_tier1" id="referral_bonus_tier1" value="<?= htmlspecialchars($settings['referral_bonus_tier1'] ?? 0.00) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="referral_bonus_tier2">Referral Bonus Tier 2 (%)</label>
            <input type="number" step="0.01" name="referral_bonus_tier2" id="referral_bonus_tier2" value="<?= htmlspecialchars($settings['referral_bonus_tier2'] ?? 0.00) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
        </div>
        <div class="md:col-span-2 mt-6">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                Save Settings
            </button>
        </div>
    </form>
</div>

<?php require_once('includes/footer.php'); ?>
