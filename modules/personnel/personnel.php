<?php
require_once __DIR__ . '/../../config/database.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Personnel Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>Utility Personnel</h2>

<table class="table table-bordered table-striped">
<tr>
<th>ID</th>
<th>Name</th>
<th>Position</th>
<th>Department</th>
<th>Assigned Area</th>
</tr>

<?php

$query = "SELECT * FROM personnel";
$result = mysqli_query($conn, $query);

if($result && mysqli_num_rows($result) > 0){

    while($row = mysqli_fetch_assoc($result)){

        echo "<tr>";
        echo "<td>".htmlspecialchars($row['id'])."</td>";
        echo "<td>".htmlspecialchars($row['fullname'])."</td>";
        echo "<td>".htmlspecialchars($row['position'])."</td>";
        echo "<td>".htmlspecialchars($row['department'])."</td>";
        echo "<td>".htmlspecialchars($row['assigned_area'])."</td>";
        echo "</tr>";

    }

}else{

    echo "<tr>";
    echo "<td colspan='5' class='text-center text-muted'>No personnel found</td>";
    echo "</tr>";

}

?>

</table>

<a href="../../dashboard.php" class="btn btn-secondary">Back</a>

</body>
</html>