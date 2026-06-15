<?php
require 'config.php';

$total_sessions = $conn->query("SELECT COUNT(DISTINCT log_date) as total FROM workout_logs")->fetch_assoc()['total'];
$total_exercises = $conn->query("SELECT COUNT(*) as total FROM workout_plans")->fetch_assoc()['total'];
$total_logs = $conn->query("SELECT COUNT(*) as total FROM workout_logs")->fetch_assoc()['total'];

$vol = $conn->query("SELECT SUM(sets_done * reps_done * weight_used) as vol FROM workout_logs")->fetch_assoc()['vol'];
$total_volume = $vol ? round($vol, 2) : 0;

$latest_bmi = $conn->query("SELECT bmi, bmi_category, recorded_date FROM body_stats ORDER BY recorded_date DESC LIMIT 1")->fetch_assoc();

$ex_list = $conn->query("SELECT DISTINCT exercise_name FROM workout_logs ORDER BY exercise_name");

$selected_ex = isset($_GET['exercise']) ? $conn->real_escape_string($_GET['exercise']) : '';
if (!$selected_ex && $ex_list->num_rows > 0) {
    $ex_list->data_seek(0);
    $first = $ex_list->fetch_assoc();
    $selected_ex = $first['exercise_name'];
    $ex_list->data_seek(0);
}

$chart_data = array();
if ($selected_ex) {
    $rows = $conn->query("SELECT log_date, weight_used, reps_done FROM workout_logs
                          WHERE exercise_name = '$selected_ex'
                          ORDER BY log_date ASC");
    while ($r = $rows->fetch_assoc()) {
        $chart_data[] = $r;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Bubbly - Progress</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Bubbly Fitness Tracker</h1>
<?php include 'nav.php'; ?>

<h2>Progress</h2>

<p>
  Total Session Days: <?= $total_sessions ?><br>
  Exercises in Plan: <?= $total_exercises ?><br>
  Total Logs: <?= $total_logs ?><br>
  Total Volume (kg): <?= $total_volume ?>
</p>

<?php if ($latest_bmi): ?>
<p>
  Latest BMI (<?= $latest_bmi['recorded_date'] ?>):
  <?= $latest_bmi['bmi'] ?> - <?= $latest_bmi['bmi_category'] ?>
</p>
<?php endif; ?>

<h3>Weight Progress by Exercise</h3>

<?php if ($ex_list->num_rows === 0): ?>
<p>No logs yet to show progress.</p>
<?php else: ?>

<form method="GET">
  Select Exercise:
  <select name="exercise" onchange="this.form.submit()">
    <?php $ex_list->data_seek(0); while ($ex = $ex_list->fetch_assoc()): ?>
    <option value="<?= htmlspecialchars($ex['exercise_name']) ?>"
      <?= ($ex['exercise_name'] === $selected_ex) ? 'selected' : '' ?>>
      <?= htmlspecialchars($ex['exercise_name']) ?>
    </option>
    <?php endwhile; ?>
  </select>
</form>

<?php if (count($chart_data) > 0): ?>
<canvas id="progressChart" width="600" height="300"></canvas>
<script>
var labels = <?= json_encode(array_column($chart_data, 'log_date')) ?>;
var weights = <?= json_encode(array_map('floatval', array_column($chart_data, 'weight_used'))) ?>;
var reps = <?= json_encode(array_map('intval', array_column($chart_data, 'reps_done'))) ?>;

var ctx = document.getElementById('progressChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Weight Used (kg)',
                data: weights,
                borderColor: 'blue',
                fill: false
            },
            {
                label: 'Reps Done',
                data: reps,
                borderColor: 'green',
                fill: false
            }
        ]
    },
    options: {
        responsive: false,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php else: ?>
<p>No logs for this exercise yet.</p>
<?php endif; ?>

<?php endif; ?>

</body>
</html>
