
<?php
session_start();
include '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: claims.php");
    exit();
}

$claim_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get claim details (only if it belongs to this user)
$claim_sql = "SELECT c.*, p.name as product_name, p.serial_number, p.description as product_description,
             w.start_date as warranty_start, w.expiry_date as warranty_end,
             pc.name as category_name
             FROM warranty_claims c
             JOIN products p ON c.product_id = p.id
             JOIN warranties w ON p.id = w.product_id
             JOIN product_categories pc ON p.category_id = pc.id
             WHERE c.id = $claim_id AND c.user_id = $user_id";
$claim_result = mysqli_query($conn, $claim_sql);

if (mysqli_num_rows($claim_result) == 0) {
    header("Location: claims.php");
    exit();
}

$claim = mysqli_fetch_assoc($claim_result);

// Get repair records if any
$repair_sql = "SELECT * FROM repair_records WHERE claim_id = $claim_id ORDER BY repair_date";
$repair_result = mysqli_query($conn, $repair_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Claim - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Warranty Guardian</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">My Products</a></li>
                <li><a href="register_product.php">Register Product</a></li>
                <li><a href="warranties.php">My Warranties</a></li>
                <li><a href="claims.php" class="active">Warranty Claims</a></li>
                <li><a href="new_claim.php">Submit Claim</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Warranty Claim #<?php echo $claim['id']; ?></h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <div class="status-badge status-<?php echo $claim['status']; ?>">
                Status: <?php echo ucfirst($claim['status']); ?>
            </div>
            
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
                            <strong>Product:</strong> <?php echo $claim['product_name']; ?>
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
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Issue Description</h3>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br($claim['issue_description']); ?></p>
                </div>
            </div>
            
            <?php if ($claim['admin_notes']): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Admin Response</h3>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br($claim['admin_notes']); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (mysqli_num_rows($repair_result) > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Repair Information</h3>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <tr>
                            <th>Date</th>
                            <th>Technician</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                        <?php while ($repair = mysqli_fetch_assoc($repair_result)): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($repair['repair_date'])); ?></td>
                            <td><?php echo $repair['technician_name']; ?></td>
                            <td><?php echo $repair['repair_description']; ?></td>
                            <td><?php echo ucfirst($repair['status']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
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
        
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            margin-bottom: 20px;
            font-weight: 500;
            color: white;
        }
        
        .status-pending {
            background-color: #f39c12;
        }
        
        .status-approved {
            background-color: #3498db;
        }
        
        .status-rejected {
            background-color: #e74c3c;
        }
        
        .status-completed {
            background-color: #2ecc71;
        }
        
        .button-group {
            margin-top: 20px;
        }
    </style>
</body>
</html>
