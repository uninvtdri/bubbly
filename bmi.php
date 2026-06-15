<?php
require 'config.php';

$message = "";
$bmi_result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $weight = (float)$_POST['weight_kg'];
    $height = (float)$_POST['height_cm'];
    $age    = (int)$_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $date   = $conn->real_escape_string($_POST['recorded_date']);

    $height_m = $height / 100;
    $bmi = round($weight / ($height_m * $height_m), 2);

    if ($bmi < 18.5) {
        $category = "Underweight";
    } elseif ($bmi < 25) {
        $category = "Normal";
    } elseif ($bmi < 30) {
        $category = "Overweight";
    } else {
        $category = "Obese";
    }

    $conn->query("INSERT INTO body_stats (weight_kg, height_cm, age, gender, bmi, bmi_category, recorded_date)
                  VALUES ($weight, $height, $age, '$gender', $bmi, '$category', '$date')");

    $bmi_result = array('bmi' => $bmi, 'category' => $category);
    $message = "Body stats saved.";
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM body_stats WHERE id = $id");
    $message = "Record deleted.";
}

$history = $conn->query("SELECT * FROM body_stats ORDER BY recorded_date DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Bubbly - Body Stats / BMI</title></head>
<body>

<h1>Bubbly Fitness Tracker</h1>
<?php include 'nav.php'; ?>

<h2>Body Stats / BMI Calculator</h2>

<?php if ($message): ?>
<p><?= $message ?></p>
<?php endif; ?>

<?php if ($bmi_result): ?>
<p>
  Your BMI: <strong><?= $bmi_result['bmi'] ?></strong><br>
  Category: <strong><?= $bmi_result['category'] ?></strong>
</p>
<?php endif; ?>

<form method="POST">
  <p>
    Weight (kg): <input type="number" name="weight_kg" step="0.1" required><br>
    Height (cm): <input type="number" name="height_cm" step="0.1" required><br>
    Age: <input type="number" name="age" min="1"><br>
    Gender:
    <select name="gender">
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      <option value="Other">Other</option>
    </select><br>
    Date: <input type="date" name="recorded_date" value="<?= date('Y-m-d') ?>" required><br>
    <button type="submit" name="save">Save & Calculate BMI</button>
  </p>
</form>

<h3>BMI Reference</h3>
<table border="1" cellpadding="5">
  <tr><th>BMI Range</th><th>Category</th></tr>
  <tr><td>Below 18.5</td><td>Underweight</td></tr>
  <tr><td>18.5 - 24.9</td><td>Normal</td></tr>
  <tr><td>25.0 - 29.9</td><td>Overweight</td></tr>
  <tr><td>30.0 and above</td><td>Obese</td></tr>
</table>

<h3>History</h3>

<?php if ($history->num_rows === 0): ?>
<p>No records yet.</p>
<?php else: ?>
<table border="1" cellpadding="5">
  <tr>
    <th>Date</th>
    <th>Weight (kg)</th>
    <th>Height (cm)</th>
    <th>Age</th>
    <th>Gender</th>
    <th>BMI</th>
    <th>Category</th>
    <th>Action</th>
  </tr>
  <?php while ($row = $history->fetch_assoc()): ?>
  <tr>
    <td><?= $row['recorded_date'] ?></td>
    <td><?= $row['weight_kg'] ?></td>
    <td><?= $row['height_cm'] ?></td>
    <td><?= $row['age'] ?></td>
    <td><?= $row['gender'] ?></td>
    <td><?= $row['bmi'] ?></td>
    <td><?= $row['bmi_category'] ?></td>
    <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a></td>
  </tr>
  <?php endwhile; ?>
</table>
<?php endif; ?>

</body>
</html>
