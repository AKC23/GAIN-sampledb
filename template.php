<?php
// index.php

// Database connection parameters – change these as needed
$host = 'localhost:3308';
$user = 'root';
$password = '';
$dbname = 'sampledb';



// Create connection using MySQLi
$mysqli = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Process form submissions (create, update, update specific column)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $mysqli->prepare("INSERT INTO items (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
    // UPDATE an entire row
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $mysqli->prepare("UPDATE items SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
    // UPDATE a specific column (for example, update only the name)
    elseif (isset($_POST['action']) && $_POST['action'] === 'update_column') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $stmt = $mysqli->prepare("UPDATE items SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
}

// Process delete requests via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CRUD Operations with PHP and MySQL</title>
    <style>
        table { border-collapse: collapse; width: 80%; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>CRUD Operations Example</h1>
    
    <!-- Create Form -->
    <h2>Create New Item</h2>
    <form method="post" action="index.php">
        <input type="hidden" name="action" value="create">
        <label>Name: <input type="text" name="name" required></label><br><br>
        <label>Description: <input type="text" name="description" required></label><br><br>
        <input type="submit" value="Create">
    </form>
    
    <!-- Items Table -->
    <h2>Items List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $mysqli->query("SELECT * FROM items");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>";
            echo "<a href='?action=edit&id=" . $row['id'] . "'>Edit</a> | ";
            echo "<a href='?action=delete&id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <?php
    // Edit Form - show only when "edit" action is triggered
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $mysqli->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            ?>
            <h2>Edit Item</h2>
            <form method="post" action="index.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required></label><br><br>
                <label>Description: <input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" required></label><br><br>
                <input type="submit" value="Update">
            </form>
            <h3>Update Specific Column (Name Only)</h3>
            <form method="post" action="index.php">
                <input type="hidden" name="action" value="update_column">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <label>New Name: <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required></label><br><br>
                <input type="submit" value="Update Name">
            </form>
            <?php
        }
        $stmt->close();
    }
    
    // Close the database connection
    $mysqli->close();
    ?>
</body>
</html>