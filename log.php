<?php
require 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log'])) {
    $name   = $conn->real_escape_string($_POST['exercise_name']);
    $sets   = (int)$_POST['sets_done'];
    $reps   = (int)$_POST['reps_done'];
    $weight = (float)$_POST['weight_used'];
    $date   = $conn->real_escape_string($_POST['log_date']);

    $conn->query("INSERT INTO workout_logs (exercise_name, sets_done, reps_done, weight_used, log_date)
                  VALUES ('$name', $sets, $reps, $weight, '$date')");
    $message = "Session logged.";
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM workout_logs WHERE id = $id");
    $message = "Log deleted.";
}

$exercises = $conn->query("SELECT DISTINCT exercise_name FROM workout_plans ORDER BY exercise_name");

$logs = $conn->query("SELECT * FROM workout_logs ORDER BY log_date DESC, id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Bubbly - Log Session</title></head>
<body>

<h1>Bubbly Fitness Tracker</h1>
<?php include 'nav.php'; ?>

<h2>Log Session</h2>

<?php if ($message): ?>
<p><?= $message ?></p>
<?php endif; ?>

<form method="POST">
  <p>
    Exercise:
    <select name="exercise_name" required>
      <option value="">-- Select or type below --</option>
      <?php while ($ex = $exercises->fetch_assoc()): ?>
      <option value="<?= htmlspecialchars($ex['exercise_name']) ?>">
        <?= htmlspecialchars($ex['exercise_name']) ?>
      </option>
      <?php endwhile; ?>
    </select>
    or type: <input type="text" name="exercise_name_manual" placeholder="Manual entry"><br>
    Sets Done: <input type="number" name="sets_done" value="3" min="1"><br>
    Reps Done: <input type="number" name="reps_done" value="10" min="1"><br>
    Weight Used (kg): <input type="number" name="weight_used" step="0.5" value="0"><br>
    Date: <input type="text" name="log_date" value="<?= date('Y-m-d') ?>" placeholder="YYYY-MM-DD" required><br>
    <button type="submit" name="log">Log Session</button>
  </p>
</form>

<script>
document.querySelector('form').addEventListener('submit', function() {
    var manual = document.querySelector('[name=exercise_name_manual]').value.trim();
    if (manual) {
        document.querySelector('[name=exercise_name]').value = manual;
    }
});
</script>

<h3>Session History</h3>

<?php if ($logs->num_rows === 0): ?>
<p>No sessions logged yet.</p>
<?php else: ?>
<table border="1" cellpadding="5">
  <tr>
    <th>Date</th>
    <th>Exercise</th>
    <th>Sets</th>
    <th>Reps</th>
    <th>Weight (kg)</th>
    <th>Action</th>
  </tr>
  <?php while ($row = $logs->fetch_assoc()): ?>
  <tr>
    <td><?= $row['log_date'] ?></td>
    <td><?= htmlspecialchars($row['exercise_name']) ?></td>
    <td><?= $row['sets_done'] ?></td>
    <td><?= $row['reps_done'] ?></td>
    <td><?= $row['weight_used'] ?></td>
    <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this log?')">Delete</a></td>
  </tr>
  <?php endwhile; ?>
</table>
<?php endif; ?>

</body>
</html>
