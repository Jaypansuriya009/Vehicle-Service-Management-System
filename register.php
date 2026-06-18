<?php
session_start();
require 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $verify_password = trim($_POST['verify_password']);
   
    // Validate inputs
    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($verify_password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: register.php");
        exit();
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $_SESSION['error'] = "Invalid phone number!";
        header("Location: register.php");
        exit();
    }

    if ($password !== $verify_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered!";
        header("Location: register.php");
        exit();
    }
    $stmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);

    if ($stmt->execute()) {
       
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: register.php");
        exit();
    }
    $stmt->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Login & Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6B8DD6, #8E37D7);
            /* background-color: whitesmoke; */
            min-height: 100vh;
        }
        
        .main-container {
            padding: 0;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.5s ease-in;
        }
        
        .image-section {
    background: linear-gradient(45deg, #5B6BD1, #8E37D7);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 700px;
    height: 100%; /* Ensure it takes full height of the container */
}

.image-section img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image covers the entire container */
}

        
        .form-section {
            padding: 40px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #dde;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(107, 141, 214, 0.2);
            border-color: #6B8DD6;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #6B8DD6, #8E37D7);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-row {
            margin-bottom: 15px;
        }

        .form-row input {
            width: 100%;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8) !important;
        }
        .hero-section {
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: url('https://source.unsplash.com/1600x900/?luxury,car') center/cover no-repeat;
            position: relative;
        }
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .btn-custom {
            font-size: 1.2rem;
            padding: 10px 20px;
            border-radius: 50px;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: none;
            transition: 0.4s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0px 10px 20px rgba(255, 255, 255, 0.2);
        }
        footer {
            background: #000;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fs-3" href="home.php"><i class="fas fa-car"></i> VSMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                </ul>
                <ul class="navbar-nav ms-3">
                   
                   
                        <li class="nav-item">
                            <a class="btn btn-light m-2 btn-custom" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary m-2 btn-custom" href="register.php">Register</a>
                        </li>
                   
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="login-container">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="image-section">
                                <img src="login.webp" alt="Welcome illustration">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-section">
                            <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); // Clear error after displaying ?>
<?php endif; ?>

                                <h2 class="text-center mb-4 fw-bold" id="formTitle">Create Account</h2>
                                <form id="authForm" action="register.php" method="POST">
                                    <div class="form-row">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="form-row">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="form-row">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                    <div class="form-row">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="form-row">
                                        <label class="form-label">Verify Password</label>
                                        <input type="password" name="verify_password" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>
                                    <p class="text-center mb-0">Already have an account? <a href="login.php" class="text-decoration-none" >Sign in</a></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
