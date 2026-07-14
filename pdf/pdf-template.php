<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Nutrition Plan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
        }

        h1 {
            color: #2b6cb0;
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
    <h1>Nutrition Plan</h1>
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
</body>

</html>