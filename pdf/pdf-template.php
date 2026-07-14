<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AI Nutrition Plan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
        }

        h1 {
            color: #2bb07d;
        }

        h1 {
            color: #2bb07d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .summary {
            margin-bottom: 16px;
        }
    </style>
</head>

<body>
    <h1>AI Nutrition Plan</h1>
    <div class="summary">
        <p><strong>Name:</strong> <?= sanitizeInput($plan['profile']['name']) ?></p>
        <p><strong>Diet Type:</strong> <?= sanitizeInput(getDietLabel($plan['diet_type'])) ?></p>
        <p><strong>Goal:</strong> <?= sanitizeInput(getGoalLabel($plan['goal'])) ?></p>
        <p><strong>Daily Target:</strong> <?= (int) $plan['calories'] ?> kcal</p>
        <p><strong>IBW:</strong> <?= (float) $plan['ibw'] ?> kg | <strong>BMI:</strong> <?= (float) $plan['bmi'] ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Meal</th>
                <th>Time</th>
                <th>Food</th>
                <th>Quantity</th>
                <th>Calories</th>
                <th>Protein</th>
                <th>Carbs</th>
                <th>Fat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plan['meals'] as $meal): ?>
                <tr>
                    <td><?= sanitizeInput($meal['name']) ?></td>
                    <td><?= sanitizeInput($meal['time']) ?></td>
                    <td><?= sanitizeInput($meal['food']) ?></td>
                    <td><?= sanitizeInput($meal['quantity']) ?></td>
                    <td><?= (int) $meal['calories'] ?></td>
                    <td><?= (int) $meal['protein'] ?>g</td>
                    <td><?= (int) $meal['carbs'] ?>g</td>
                    <td><?= (int) $meal['fat'] ?>g</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="summary-container" style="display: flex; gap: 40px; margin-top: 24px; align-items: flex-start;">
        <!-- Left Column: Daily Nutrition Summary -->
        <div class="card">
            <h2>Daily Nutrition Summary</h2>
            <div class="stats-grid">
                <div class="stat-box"><span><strong>Calories:</strong> <?= (int) $plan['totals']['calories'] ?></span></div>
                <div class="stat-box"><span><strong>Protein:</strong> <?= (int) $plan['totals']['protein'] ?>g</span></div>
                <div class="stat-box"><span><strong>Carbs:</strong> <?= (int) $plan['totals']['carbs'] ?>g</span></div>
                <div class="stat-box"><span><strong>Fat:</strong> <?= (int) $plan['totals']['fat'] ?>g</span></div>

            </div>
            <p><strong>Target Macros:</strong> Protein <?= (int) $plan['macros']['protein'] ?>g · Carbs <?= (int) $plan['macros']['carbs'] ?>g · Fat <?= (int) $plan['macros']['fat'] ?>g</p>
        </div>
        <br>
        <div class="footer">
            <div>
                <strong style="color: #2bb07d;">AI Nutrition</strong><br>
                Personalized Nutrition Report<br><br>

                <em>This report is generated using Artificial Intelligence and is intended for informational purposes only.</em>
            </div>
        </div>


</body>

</html>