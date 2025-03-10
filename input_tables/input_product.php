<?php
// input_tables/input_product.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $productName = $_POST['productName'];
        $foodTypeID = $_POST['foodTypeID'];
        $brandID = $_POST['brandID'];
        $companyID = $_POST['companyID'];

        // Check if the combination of ProductName, FoodTypeID, BrandID, and CompanyID already exists
        $checkQuery = $conn->prepare("SELECT * FROM product WHERE ProductName = ? AND FoodTypeID = ? AND BrandID = ? AND CompanyID = ?");
        $checkQuery->bind_param("siii", $productName, $foodTypeID, $brandID, $companyID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of ProductName, FoodTypeID, BrandID, and CompanyID already exists. Please use a different combination.'); window.location.href = 'input_product.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO product (ProductName, FoodTypeID, BrandID, CompanyID) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii", $productName, $foodTypeID, $brandID, $companyID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_product.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $productName = $_POST['productName'];
        $foodTypeID = $_POST['foodTypeID'];
        $brandID = $_POST['brandID'];
        $companyID = $_POST['companyID'];
        $stmt = $conn->prepare("UPDATE product SET ProductName = ?, FoodTypeID = ?, BrandID = ?, CompanyID = ? WHERE ProductID = ?");
        $stmt->bind_param("siiii", $productName, $foodTypeID, $brandID, $companyID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_product.php");
        exit;
    }
}

// Process delete requests via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check for foreign key constraints
    $checkForeignKeyQuery = "
        SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_NAME = 'product' AND REFERENCED_COLUMN_NAME = 'ProductID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE ProductID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_product.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM product WHERE ProductID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_product.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Modify Product Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table th,
        table td {
            text-align: center;
            vertical-align: middle;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Modify Product Table</h1>

        <!-- Create Form -->
        <h3>Add New Informations</h3>
        <form method="post" action="input_product.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="foodTypeID">Food Type:</label>
                <select id="foodTypeID" name="foodTypeID" class="form-control" required>
                    <?php
                    $foodTypeResult = $conn->query("SELECT FoodTypeID, FoodTypeName FROM foodtype ORDER BY FoodTypeName ASC");
                    while ($foodTypeRow = $foodTypeResult->fetch_assoc()) {
                        echo "<option value='{$foodTypeRow['FoodTypeID']}'>" . htmlspecialchars($foodTypeRow['FoodTypeName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="brandID">Brand:</label>
                <select id="brandID" name="brandID" class="form-control" required>
                    <?php
                    $brandResult = $conn->query("SELECT BrandID, BrandName FROM brand ORDER BY BrandName ASC");
                    while ($brandRow = $brandResult->fetch_assoc()) {
                        echo "<option value='{$brandRow['BrandID']}'>" . htmlspecialchars($brandRow['BrandName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="companyID">Company:</label>
                <select id="companyID" name="companyID" class="form-control" required>
                    <?php
                    $companyResult = $conn->query("SELECT CompanyID, CompanyName FROM company ORDER BY CompanyName ASC");
                    while ($companyRow = $companyResult->fetch_assoc()) {
                        echo "<option value='{$companyRow['CompanyID']}'>" . htmlspecialchars($companyRow['CompanyName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        <!-- Product Table -->
        <h2>Table: Product</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Food Type</th>
                        <th>Brand</th>
                        <th>Company</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT p.ProductID, p.ProductName, ft.FoodTypeName, b.BrandName, c.CompanyName FROM product p JOIN foodtype ft ON p.FoodTypeID = ft.FoodTypeID JOIN brand b ON p.BrandID = b.BrandID JOIN company c ON p.CompanyID = c.CompanyID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ProductID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FoodTypeName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['BrandName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CompanyName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['ProductID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['ProductID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        <?php
        // Edit Form - show only when "edit" action is triggered
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM product WHERE ProductID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
        ?>

                <h2>Edit Product</h2>
                <form method="post" action="input_product.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ProductID']); ?>">
                    <div class="form-group">
                        <label for="productName">Product Name:</label>
                        <input type="text" id="productName" name="productName" class="form-control" value="<?php echo htmlspecialchars($row['ProductName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="foodTypeID">Food Type:</label>
                        <select id="foodTypeID" name="foodTypeID" class="form-control" required>
                            <?php
                            $foodTypeResult = $conn->query("SELECT FoodTypeID, FoodTypeName FROM foodtype ORDER BY FoodTypeName ASC");
                            while ($foodTypeRow = $foodTypeResult->fetch_assoc()) {
                                $selected = ($foodTypeRow['FoodTypeID'] == $row['FoodTypeID']) ? 'selected' : '';
                                echo "<option value='{$foodTypeRow['FoodTypeID']}' $selected>" . htmlspecialchars($foodTypeRow['FoodTypeName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brandID">Brand:</label>
                        <select id="brandID" name="brandID" class="form-control" required>
                            <?php
                            $brandResult = $conn->query("SELECT BrandID, BrandName FROM brand ORDER BY BrandName ASC");
                            while ($brandRow = $brandResult->fetch_assoc()) {
                                $selected = ($brandRow['BrandID'] == $row['BrandID']) ? 'selected' : '';
                                echo "<option value='{$brandRow['BrandID']}' $selected>" . htmlspecialchars($brandRow['BrandName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="companyID">Company:</label>
                        <select id="companyID" name="companyID" class="form-control" required>
                            <?php
                            $companyResult = $conn->query("SELECT CompanyID, CompanyName FROM company ORDER BY CompanyName ASC");
                            while ($companyRow = $companyResult->fetch_assoc()) {
                                $selected = ($companyRow['CompanyID'] == $row['CompanyID']) ? 'selected' : '';
                                echo "<option value='{$companyRow['CompanyID']}' $selected>" . htmlspecialchars($companyRow['CompanyName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                </form>
                <!-- Add space after the edit form -->
                <div class="mb-5"></div>
        <?php
            }
            $stmt->close();
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>