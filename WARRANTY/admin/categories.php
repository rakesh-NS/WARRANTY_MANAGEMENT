
<?php
session_start();
include '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Handle category deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Check if category has associated products
    $check_products_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = $id";
    $check_products_result = mysqli_query($conn, $check_products_sql);
    $product_count = mysqli_fetch_assoc($check_products_result)['count'];
    
    if ($product_count > 0) {
        $error = "Cannot delete category: There are $product_count products using this category.";
    } else {
        $delete_sql = "DELETE FROM product_categories WHERE id = $id";
        if (mysqli_query($conn, $delete_sql)) {
            $success = "Category deleted successfully!";
        } else {
            $error = "Error deleting category: " . mysqli_error($conn);
        }
    }
}

// Handle form submission for editing category
if (isset($_POST['edit_category'])) {
    $id = $_POST['category_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $warranty_duration = $_POST['warranty_duration'];
    
    $update_sql = "UPDATE product_categories SET name='$name', description='$description', 
                  warranty_duration=$warranty_duration WHERE id=$id";
    
    if (mysqli_query($conn, $update_sql)) {
        $success = "Category updated successfully!";
    } else {
        $error = "Error updating category: " . mysqli_error($conn);
    }
}

// Handle form submission for new category
if (isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $warranty_duration = $_POST['warranty_duration'];
    
    $insert_sql = "INSERT INTO product_categories (name, description, warranty_duration) 
                  VALUES ('$name', '$description', $warranty_duration)";
    
    if (mysqli_query($conn, $insert_sql)) {
        $success = "Category added successfully!";
    } else {
        $error = "Error adding category: " . mysqli_error($conn);
    }
}

// Get category for editing if edit parameter is passed
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM product_categories WHERE id=$edit_id";
    $edit_result = mysqli_query($conn, $edit_sql);
    $edit_category = mysqli_fetch_assoc($edit_result);
}

// Get all categories
$categories_sql = "SELECT pc.*, (SELECT COUNT(*) FROM products WHERE category_id = pc.id) as product_count 
                  FROM product_categories pc ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Categories - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Admin Portal</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="categories.php" class="active">Product Categories</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Product Categories</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h3>
                </div>
                <div class="card-body">
                    <form action="categories.php" method="post">
                        <?php if ($edit_category): ?>
                            <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" id="name" name="name" value="<?php echo $edit_category ? $edit_category['name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"><?php echo $edit_category ? $edit_category['description'] : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="warranty_duration">Default Warranty Duration (months)</label>
                            <input type="number" id="warranty_duration" name="warranty_duration" min="1" value="<?php echo $edit_category ? $edit_category['warranty_duration'] : ''; ?>" required>
                        </div>
                        <button type="submit" name="<?php echo $edit_category ? 'edit_category' : 'add_category'; ?>" class="btn primary-btn">
                            <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                        </button>
                        <?php if ($edit_category): ?>
                            <a href="categories.php" class="btn secondary-btn">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>All Categories</h3>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($categories_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Warranty (months)</th>
                                <th>Product Count</th>
                                <th>Actions</th>
                            </tr>
                            <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo $category['name']; ?></td>
                                <td><?php echo $category['description'] ?: 'Not provided'; ?></td>
                                <td><?php echo $category['warranty_duration']; ?></td>
                                <td><?php echo $category['product_count']; ?></td>
                                <td class="actions">
                                    <a href="categories.php?edit=<?php echo $category['id']; ?>" class="btn small-btn">Edit</a>
                                    <?php if ($category['product_count'] == 0): ?>
                                        <a href="categories.php?delete=<?php echo $category['id']; ?>" class="btn small-btn delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                    <?php else: ?>
                                        <span class="disabled-btn" title="Cannot delete: category has products">Delete</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No categories found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .small-btn {
            padding: 5px 10px;
            font-size: 0.8em;
        }
        
        .delete-btn {
            background-color: #e74c3c;
        }
        
        .delete-btn:hover {
            background-color: #c0392b;
        }
        
        .secondary-btn {
            background-color: #7f8c8d;
            color: white;
        }
        
        .secondary-btn:hover {
            background-color: #95a5a6;
        }
        
        .disabled-btn {
            padding: 5px 10px;
            font-size: 0.8em;
            background-color: #ccc;
            color: #777;
            border-radius: 3px;
            cursor: not-allowed;
        }
    </style>
</body>
</html>
