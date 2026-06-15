<?php
require 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name   = $conn->real_escape_string($_POST['exercise_name']);
    $muscle = $conn->real_escape_string($_POST['muscle_group']);
    $sets   = (int)$_POST['sets'];
    $reps   = (int)$_POST['reps'];
    $weight = (float)$_POST['weight'];

    $conn->query("INSERT INTO workout_plans (exercise_name, muscle_group, sets, reps, weight)
                  VALUES ('$name', '$muscle', $sets, $reps, $weight)");
    $message = "Exercise added.";
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM workout_plans WHERE id = $id");
    $message = "Exercise deleted.";
}

$result = $conn->query("SELECT * FROM workout_plans ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Bubbly - Workout Plan</title></head>
<body>

<h1>Bubbly Fitness Tracker</h1>
<?php include 'nav.php'; ?>

<h2>Workout Plan</h2>

<?php if ($message): ?>
<p><?= $message ?></p>
<?php endif; ?>

<form method="POST">
  <p>
    Exercise Name: <input type="text" name="exercise_name" required><br>
    Muscle Group: <input type="text" name="muscle_group"><br>
    Sets: <input type="number" name="sets" value="3" min="1"><br>
    Reps: <input type="number" name="reps" value="10" min="1"><br>
    Weight (kg): <input type="number" name="weight" step="0.5" value="0"><br>
    <button type="submit" name="add">Add Exercise</button>
  </p>
</form>

<h3>Your Exercises</h3>

<?php if ($result->num_rows === 0): ?>
<p>No exercises added yet.</p>
<?php else: ?>
<table border="1" cellpadding="5">
  <tr>
    <th>Exercise</th>
    <th>Muscle Group</th>
    <th>Sets</th>
    <th>Reps</th>
    <th>Weight (kg)</th>
    <th>Action</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($row['exercise_name']) ?></td>
    <td><?= htmlspecialchars($row['muscle_group']) ?></td>
    <td><?= $row['sets'] ?></td>
    <td><?= $row['reps'] ?></td>
    <td><?= $row['weight'] ?></td>
    <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this?')">Delete</a></td>
  </tr>
  <?php endwhile; ?>
</table>
<?php endif; ?>

</body>
</html>
