<?php
    if ($_GET['action'] === 'register') {
        require '../config/db.php';
    
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $matricula = $_POST['matricula'];
        $department = $_POST['department'];
        $institution = $_POST['institution'];
        $career = $_POST['career'];
    
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, matricula, department, institution, career) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $matricula, $department, $institution, $career]);
    
        header('Location: ../public/login.php');
        exit();
    }    
    if ($_GET['action'] === 'login') {
        session_start();
        require '../config/db.php';
    
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
    
            // Redirección según el rol del usuario
            switch ($user['role']) {
                case 'admin_eventos':
                    header('Location: ../public/dashboard_admin_eventos.php');
                    break;
                case 'admin':
                    header('Location: ../public/dashboard_admin.php');
                    break;
                case 'superadmin':
                    header('Location: ../public/dashboard_superadmin.php');
                    break;
                default: // usuario
                    header('Location: ../public/dashboard_user.php');
                    break;
            }
            exit();
        } else {
            echo "Correo o contraseña incorrectos.";
        }
    }
?>