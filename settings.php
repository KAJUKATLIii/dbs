<?php
session_start();

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

// Update user settings if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validate form data
    if (empty($name) || empty($email)) {
        $_SESSION['error'] = "Name and email cannot be empty.";
    } else {
        // Update user data in session
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['success'] = "Settings updated successfully.";
    }

    // Redirect back to settings page
    header("Location: settings.php");
    exit();
}

// Include header
include_once('includes/header.php');
?>

<div class="container">
    <h2>Settings</h2>
    <?php include_once('includes/flash_messages.php'); ?>
    <form method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Settings</button>
    </form>
</div>

<?php include_once('includes/footer.php'); ?>