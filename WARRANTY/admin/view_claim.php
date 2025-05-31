
<?php
session_start();
include '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: claims.php");
    exit();
}

$claim_id = $_GET['id'];
$error = '';
$success = '';

// Get claim details
$claim_sql = "SELECT c.*, p.name as product_name, p.serial_number, p.description as product_description, 
             u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
             w.start_date as warranty_start, w.expiry_date as warranty_end,
             pc.name as category_name
             FROM warranty_claims c
             JOIN products p ON c.product_id = p.id
             JOIN users u ON c.user_id = u.id
             JOIN warranties w ON p.id = w.product_id
             JOIN product_categories pc ON p.category_id = pc.id
             WHERE c.id = $claim_id";
$claim_result = mysqli_query($conn, $claim_sql);

if (mysqli_num_rows($claim_result) == 0) {
    header("Location: claims.php");
    exit();
}

$claim = mysqli_fetch_assoc($claim_result);

// Get repair records if any
$repair_sql = "SELECT * FROM repair_records WHERE claim_id = $claim_id ORDER BY repair_date";
$repair_result = mysqli_query($conn, $repair_sql);

// Process status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == 'update_status') {
        $new_status = $_POST['status'];
        $admin_notes = $_POST['admin_notes'];
        
        $update_sql = "UPDATE warranty_claims SET status = '$new_status', admin_notes = '$admin_notes', 
                      processed_date = CURDATE() WHERE id = $claim_id";
        
        if (mysqli_query($conn, $update_sql)) {
            $success = "Claim status updated successfully!";
            // Refresh claim data
            $claim_result = mysqli_query($conn, $claim_sql);
            $claim = mysqli_fetch_assoc($claim_result);
        } else {
            $error = "Error updating claim: " . mysqli_error($conn);
        }
    } elseif ($action == 'add_repair') {
        $repair_desc = $_POST['repair_description'];
        $repair_date = $_POST['repair_date'];
        $technician = $_POST['technician_name'];
        $repair_cost = $_POST['cost'];
        $repair_status = $_POST['repair_status'];
        
        $repair_sql = "INSERT INTO repair_records (claim_id, repair_date, repair_description, cost, technician_name, status) 
                      VALUES ($claim_id, '$repair_date', '$repair_desc', $repair_cost, '$technician', '$repair_status')";
        
        if (mysqli_query($conn, $repair_sql)) {
            $success = "Repair record added successfully!";
            // Refresh repair data
            $repair_result = mysqli_query($conn, "SELECT * FROM repair_records WHERE claim_id = $claim_id ORDER BY repair_date");
            
            // If repair is completed, update claim status
            if ($repair_status == 'completed') {
                mysqli_query($conn, "UPDATE warranty_claims SET status = 'completed' WHERE id = $claim_id");
                $claim['status'] = 'completed';
            }
        } else {
            $error = "Error adding repair record: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Claim - Admin - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Admin Portal</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="claims.php" class="active">Warranty Claims</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="categories.php">Product Categories</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Warranty Claim #<?php echo $claim['id']; ?></h1>
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
                    <h3>Claim Information</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Claim ID:</strong> <?php echo $claim['id']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Date Submitted:</strong> <?php echo date('M d, Y', strtotime($claim['date_submitted'])); ?>
                        </div>
                        <div class="info-item">
                            <strong>Status:</strong> <?php echo ucfirst($claim['status']); ?>
                        </div>
                        <div class="info-item">
                            <strong>Processed Date:</strong> 
                            <?php echo $claim['processed_date'] ? date('M d, Y', strtotime($claim['processed_date'])) : 'Not processed yet'; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Product Information</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Product Name:</strong> <?php echo $claim['product_name']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Serial Number:</strong> <?php echo $claim['serial_number']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Category:</strong> <?php echo $claim['category_name']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Warranty Period:</strong> 
                            <?php echo date('M d, Y', strtotime($claim['warranty_start'])); ?> - 
                            <?php echo date('M d, Y', strtotime($claim['warranty_end'])); ?>
                        </div>
                        <div class="info-item full-width">
                            <strong>Description:</strong> <?php echo $claim['product_description']; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Customer Information</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Name:</strong> <?php echo $claim['customer_name']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong> <?php echo $claim['customer_email']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Phone:</strong> <?php echo $claim['customer_phone']; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Issue Description</h3>
                </div>
                <div class="card-body">
                    <p><?php echo $claim['issue_description']; ?></p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Admin Notes</h3>
                </div>
                <div class="card-body">
                    <p><?php echo $claim['admin_notes'] ? $claim['admin_notes'] : 'No notes added yet.'; ?></p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Update Claim Status</h3>
                </div>
                <div class="card-body">
                    <form action="view_claim.php?id=<?php echo $claim_id; ?>" method="post">
                        <input type="hidden" name="action" value="update_status">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="pending" <?php echo $claim['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $claim['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $claim['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="completed" <?php echo $claim['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="admin_notes">Admin Notes</label>
                            <textarea id="admin_notes" name="admin_notes" rows="4"><?php echo $claim['admin_notes']; ?></textarea>
                        </div>
                        <button type="submit" class="btn primary-btn">Update Status</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Repair Records</h3>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($repair_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>Date</th>
                                <th>Technician</th>
                                <th>Description</th>
                                <th>Cost</th>
                                <th>Status</th>
                            </tr>
                            <?php while ($repair = mysqli_fetch_assoc($repair_result)): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($repair['repair_date'])); ?></td>
                                <td><?php echo $repair['technician_name']; ?></td>
                                <td><?php echo $repair['repair_description']; ?></td>
                                <td>$<?php echo number_format($repair['cost'], 2); ?></td>
                                <td><?php echo ucfirst($repair['status']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No repair records found for this claim.</p>
                    <?php endif; ?>
                    
                    <?php if ($claim['status'] == 'approved'): ?>
                        <h4>Add Repair Record</h4>
                        <form action="view_claim.php?id=<?php echo $claim_id; ?>" method="post">
                            <input type="hidden" name="action" value="add_repair">
                            <div class="form-group">
                                <label for="repair_date">Repair Date</label>
                                <input type="date" id="repair_date" name="repair_date" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="repair_description">Repair Description</label>
                                <textarea id="repair_description" name="repair_description" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="technician_name">Technician Name</label>
                                <input type="text" id="technician_name" name="technician_name" required>
                            </div>
                            <div class="form-group">
                                <label for="cost">Cost ($)</label>
                                <input type="number" id="cost" name="cost" step="0.01" min="0" required value="0.00">
                            </div>
                            <div class="form-group">
                                <label for="repair_status">Status</label>
                                <select id="repair_status" name="repair_status" required>
                                    <option value="in-progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <button type="submit" class="btn primary-btn">Add Repair Record</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="button-group">
                <a href="claims.php" class="btn secondary-btn">Back to Claims</a>
            </div>
        </div>
    </div>
    
    <style>
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .info-item {
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .full-width {
            grid-column: span 2;
        }
        
        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</body>
</html>
