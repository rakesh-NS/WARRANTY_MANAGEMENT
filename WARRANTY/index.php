
<?php
session_start();
// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: customer/dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warranty Guardian System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .hero-section {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 60px 20px;
        }
        
        .hero-section h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .hero-section p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 30px;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        .features-section {
            padding: 60px 20px;
            text-align: center;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .feature-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <h1>Warranty Guardian System</h1>
            <p>Efficiently manage your product warranties, submit claims, and track repairs with our comprehensive warranty management system.</p>
            <div class="cta-buttons">
                <a href="login.php" class="btn primary-btn">Login</a>
                <a href="register.php" class="btn secondary-btn">Register</a>
            </div>
        </div>
    </div>
    
    <div class="features-section">
        <div class="container">
            <h2>Our Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3>Product Registration</h3>
                    <p>Register your products and warranties in one place for easy management.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔄</div>
                    <h3>Warranty Tracking</h3>
                    <p>Keep track of your warranty status and expiration dates.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚠️</div>
                    <h3>Claim Submission</h3>
                    <p>Easily submit warranty claims for your registered products.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Service Tracking</h3>
                    <p>Monitor the repair status of your products in real-time.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
