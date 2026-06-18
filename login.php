<?php
session_start();
require 'config.php'; // Include database connection

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$error = ""; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Secure query with prepared statement
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $username, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['user_logged_in'] = true;
                $_SESSION['role'] = $role;

                // Redirect based on user role
                if ($role === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
     body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #6B8DD6, #8E37D7);
    /* background-color: white; */
    min-height: 100vh;
   
    justify-content: center;
    align-items: center;
    margin: 0;
    margin-bottom: 200px;
}


        .main-container {
            width: 90%;
            margin-top: 5%;
            /* padding: top 150px; ; */
            max-width: 900px;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.5s ease-in;
        }

        .image-section {
    background: linear-gradient(45deg, #5B6BD1, #8E37D7);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
   
}



        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-section {
            padding: 40px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #dde;
            background: #f8f9fa;
            font-size: 16px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(107, 141, 214, 0.3);
            border-color: #6B8DD6;
        }

        .btn-primary {
            background: linear-gradient(45deg, #6B8DD6, #8E37D7);
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(107, 141, 214, 0.3);
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="btn btn-danger text-white btn-custom" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-light m-2 btn-custom" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary m-2 btn-custom" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container main-container m-25">
        <div class="row g-0">
            <div class="col-md-6">
                <div class="image-section">
                    <img src="login.webp" alt="Login illustration">
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

                    <h2 class="text-center mb-4 fw-bold">Welcome Back</h2>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>

                        <p class="text-center mb-0">Don't have an account? <a href="register.php" class="text-decoration-none">Register</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
