<?php
// Show errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli("localhost", "root", "", "chillspot");
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

// DELETE USER
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// UPDATE USER
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $regno = $conn->real_escape_string($_POST['regno']);
    $name = $conn->real_escape_string($_POST['name']);
    $year = $conn->real_escape_string($_POST['year']);
    $dept = $conn->real_escape_string($_POST['dept']);

    // Fetch old image
    $oldUser = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
    $imagePath = $oldUser['image'];

    // Handle new image upload
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $newName = "../uploads/" . uniqid("IMG_", true) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $newName);
        $imagePath = "uploads/" . basename($newName);
    }

    // Update DB
    $conn->query("
        UPDATE users SET 
        regno='$regno',
        name='$name',
        year='$year',
        dept='$dept',
        image='$imagePath'
        WHERE id=$id
    ");

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch users
$users = $conn->query("SELECT * FROM users");

// Check if editing
$editUser = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editUser = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - ChillSpot Users</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- HEADER -->
<header class="bg-gradient-to-r from-teal-700 to-teal-600 text-white shadow-md sticky top-0 z-50">
  <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
    <a class="text-2xl font-bold hover:text-white/80">ChillSpot CUCEK</a>
    <button onclick="location.href='../login.html'" 
            class="bg-white text-teal-700 px-5 py-2.5 rounded-lg font-semibold hover:bg-teal-50 shadow-sm">
      Logout
    </button>
  </nav>
</header>

<div class="container mx-auto p-6">

<!-- USERS TABLE -->
<div class="bg-white p-6 rounded-xl shadow-md mb-8">
<h2 class="text-2xl font-semibold mb-4">Registered Users</h2>

<table class="min-w-full bg-white border">
<thead class="bg-gray-100">
<tr>
  <th class="border px-4 py-2">Image</th>
  <th class="border px-4 py-2">Reg No</th>
  <th class="border px-4 py-2">Name</th>
  <th class="border px-4 py-2">Year</th>
  <th class="border px-4 py-2">Dept</th>
  <th class="border px-4 py-2">Actions</th>
</tr>
</thead>
<tbody>
<?php while ($row = $users->fetch_assoc()) { ?>
<tr>
  <td class="border px-4 py-2">
    <img src="../<?= $row['image'] ?>" class="w-16 h-16 rounded object-cover">
  </td>
  <td class="border px-4 py-2"><?= $row['regno'] ?></td>
  <td class="border px-4 py-2"><?= $row['name'] ?></td>
  <td class="border px-4 py-2"><?= $row['year'] ?></td>
  <td class="border px-4 py-2"><?= $row['dept'] ?></td>
  <td class="border px-4 py-2 space-x-2">
    <a href="<?= $_SERVER['PHP_SELF'] ?>?edit=<?= $row['id'] ?>" 
       class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</a>
    <a href="<?= $_SERVER['PHP_SELF'] ?>?delete=<?= $row['id'] ?>" 
       onclick="return confirm('Delete user?')"
       class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

<!-- EDIT FORM -->
<?php if ($editUser) { ?>
<div class="bg-white p-6 rounded-xl shadow-md">
<h2 class="text-2xl font-semibold mb-4">Edit User</h2>

<form method="POST" enctype="multipart/form-data" class="space-y-4" action="<?= $_SERVER['PHP_SELF'] ?>">

<input type="hidden" name="id" value="<?= $editUser['id'] ?>">

<div>
  <label class="block font-medium">Reg No</label>
  <input type="text" name="regno" value="<?= $editUser['regno'] ?>" class="w-full border p-2 rounded">
</div>

<div>
  <label class="block font-medium">Name</label>
  <input type="text" name="name" value="<?= $editUser['name'] ?>" class="w-full border p-2 rounded">
</div>

<div>
  <label class="block font-medium">Year</label>
  <input type="text" name="year" value="<?= $editUser['year'] ?>" class="w-full border p-2 rounded">
</div>

<div>
  <label class="block font-medium">Dept</label>
  <input type="text" name="dept" value="<?= $editUser['dept'] ?>" class="w-full border p-2 rounded">
</div>

<div>
  <label class="block font-medium">Current Image</label>
  <img src="../<?= $editUser['image'] ?>" class="w-24 h-24 rounded object-cover mb-2">
  <input type="file" name="image" class="w-full border p-2 rounded">
</div>

<button name="update" class="bg-teal-700 text-white px-6 py-2 rounded hover:bg-teal-800">
  Update
</button>

</form>
</div>
<?php } ?>

</div>

</body>
</html>
