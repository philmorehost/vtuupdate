<?php
$title = 'Manage Services';
require_once('includes/header.php');

// In a real app, this would be more dynamic, probably from the services table
$services = [
    'data' => [
        ['id' => 1, 'network' => 'MTN', 'plan' => '1GB', 'price' => 300, 'duration' => '1 Day'],
        ['id' => 2, 'network' => 'Glo', 'plan' => '3GB', 'price' => 700, 'duration' => '14 Days'],
    ],
    'airtime' => [
        ['id' => 1, 'network' => 'All', 'discount' => '1%']
    ]
];
?>

<!-- Data Plans -->
<div class="bg-white p-6 rounded-lg shadow-md mb-6 table-container">
    <h2 class="text-2xl font-bold mb-4">Data Plans</h2>
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Network</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Plan</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Price</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php foreach ($services['data'] as $plan): ?>
                <tr>
                    <td class="text-left py-3 px-4"><?= htmlspecialchars($plan['network']) ?></td>
                    <td class="text-left py-3 px-4"><?= htmlspecialchars($plan['plan']) ?></td>
                    <td class="text-left py-3 px-4">₦<?= htmlspecialchars($plan['price']) ?></td>
                    <td class="text-left py-3 px-4">
                        <a href="#" class="text-blue-500 hover:text-blue-700">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Airtime Discounts -->
<div class="bg-white p-6 rounded-lg shadow-md table-container">
    <h2 class="text-2xl font-bold mb-4">Airtime Discounts</h2>
     <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Network</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Discount</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php foreach ($services['airtime'] as $service): ?>
                <tr>
                    <td class="text-left py-3 px-4"><?= htmlspecialchars($service['network']) ?></td>
                    <td class="text-left py-3 px-4"><?= htmlspecialchars($service['discount']) ?></td>
                    <td class="text-left py-3 px-4">
                        <a href="#" class="text-blue-500 hover:text-blue-700">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer.php'); ?>
