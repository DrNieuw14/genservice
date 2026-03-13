<?php
session_start();
include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit();
}

$successMessage = "";
$errorMessage = "";
$allowedGenders = ['Male', 'Female', 'Other'];

$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['user'];

if (isset($_POST['update_profile'])) {
    $birthdate = trim($_POST['birthdate'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    if ($birthdate === '' || $gender === '') {
        $errorMessage = "Birthdate and gender are required.";
    } elseif (!in_array($gender, $allowedGenders, true)) {
        $errorMessage = "Invalid gender selected.";
    } else {
        $updateQuery = "UPDATE users SET birthdate = ?, gender = ? WHERE ";

        if ($userId !== null) {
            $updateQuery .= "id = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssi", $birthdate, $gender, $userId);
                if (mysqli_stmt_execute($stmt)) {
                    $successMessage = "Profile updated successfully.";
                } else {
                    $errorMessage = "Failed to update profile.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $errorMessage = "A server error occurred.";
            }
        } else {
            $updateQuery .= "username = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $birthdate, $gender, $username);
                if (mysqli_stmt_execute($stmt)) {
                    $successMessage = "Profile updated successfully.";
                } else {
                    $errorMessage = "Failed to update profile.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $errorMessage = "A server error occurred.";
            }
        }
    }
}

if ($userId !== null) {
    $profileQuery = "SELECT first_name, middle_initial, last_name, birthdate, gender, username, role FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $profileQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
    }
} else {
    $profileQuery = "SELECT first_name, middle_initial, last_name, birthdate, gender, username, role FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $profileQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
    }
}

$userProfile = null;

if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) === 1) {
        $userProfile = mysqli_fetch_assoc($result);
    } else {
        $errorMessage = "Unable to load profile.";
    }
    mysqli_stmt_close($stmt);
} else {
    $errorMessage = "A server error occurred.";
}

if (!$userProfile) {
    $userProfile = [
        'first_name' => '',
        'middle_initial' => '',
        'last_name' => '',
        'birthdate' => '',
        'gender' => '',
        'username' => $username,
        'role' => $_SESSION['role'] ?? ''
    ];
}

$middleInitial = trim($userProfile['middle_initial'] ?? '');
$fullName = trim(($userProfile['first_name'] ?? '') . ' ' . ($middleInitial !== '' ? $middleInitial . '. ' : '') . ($userProfile['last_name'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">GENSERVIS</span>
            <a href="../../dashboard.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">My Profile</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($successMessage !== ""): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>

                        <?php if ($errorMessage !== ""): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Username</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['username'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Role</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['role'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        </div>

                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="birthdate" class="form-label">Birthdate</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="birthdate"
                                    name="birthdate"
                                    value="<?php echo htmlspecialchars($userProfile['birthdate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    required
                                >
                            </div>

                            <div class="mb-4">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select gender</option>
                                    <?php foreach ($allowedGenders as $genderOption): ?>
                                        <option
                                            value="<?php echo htmlspecialchars($genderOption, ENT_QUOTES, 'UTF-8'); ?>"
                                            <?php echo (($userProfile['gender'] ?? '') === $genderOption) ? 'selected' : ''; ?>
                                        >
                                            <?php echo htmlspecialchars($genderOption, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>