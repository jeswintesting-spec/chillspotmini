<?php
$conn = new mysqli("localhost", "root", "", "chillspot");

$id = $_POST['id'];
$regno = $_POST['regno'];
$name = $_POST['name'];
$year = $_POST['year'];
$dept = $_POST['dept'];

// Get old user
$old = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
$imagePath = $old['image'];

// If new image uploaded
if (!empty($_FILES['image']['name'])) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $newName = "uploads/" . uniqid("IMG_", true) . "." . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], "../" . $newName);
    $imagePath = $newName;
}

$conn->query("UPDATE users SET regno='$regno', name='$name', year='$year', dept='$dept', image='$imagePath' WHERE id=$id");

header("Location: user_admin.php");
exit;
