<?php
session_start();
require '../config/db.php';

// Validar acción solicitada
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        // Preregistrar a un usuario en un evento
        case 'pre_registrar':
            // se tiene lo siguiente `../controllers/dashboard.php?action=pre_registrar&id=${eventId}`
            if (isset($_GET['id'])) {
                $event_id = $_GET['id'];
                $stmt = $pdo->prepare("INSERT INTO pre_registrations (user_id, event_id) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], $event_id]);

                header('Location: ../public/dashboard_user.php');
                exit();
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID de evento no proporcionado']);
                exit();
            }
        break;

        case 'update_profile':
            if ($_POST['name'] && $_POST['matricula']) {
                $name = $_POST['name'];
                $matricula = $_POST['matricula'];
                $user_id = $_SESSION['user_id'];

                // Manejar foto de perfil
                $profile_picture = null;
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../public/images/profiles/';
                    $file_name = basename($_FILES['profile_picture']['name']);
                    $file_tmp_path = $_FILES['profile_picture']['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    // Validar extensión del archivo
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array($file_ext, $allowed_exts)) {
                        // Crear un nombre único para la imagen
                        $new_file_name = uniqid('profile_') . '.' . $file_ext;
                        $profile_picture_path = $upload_dir . $new_file_name;

                        // Verificar directorio y mover archivo
                        if (!file_exists($upload_dir)) {
                            die('El directorio de subida no existe.');
                        }

                        if (!is_writable($upload_dir)) {
                            die('El directorio no tiene permisos de escritura.');
                        }

                        if (move_uploaded_file($file_tmp_path, $profile_picture_path)) {
                            $profile_picture = 'images/profiles/' . $new_file_name; // Ruta relativa
                        } else {
                            $_SESSION['error'] = 'No se pudo guardar la imagen. Intenta nuevamente.';
                            header('Location: ../public/dashboard_user.php');
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = 'Formato de imagen no permitido. Solo se aceptan JPG, PNG o GIF.';
                        header('Location: ../public/dashboard_user.php');
                        exit();
                    }
                }

                // Actualizar datos del usuario
                $stmt = $pdo->prepare("UPDATE users SET name = ?, matricula = ?, profile_picture = ? WHERE id = ?");
                $stmt->execute([$name, $matricula, $profile_picture, $user_id]);

                $_SESSION['success'] = 'Perfil actualizado correctamente.';
                header('Location: ../public/dashboard_user.php');
                exit();
            }
            break;

        default:
            header('Location: ../public/dashboard_user.php');
            exit();
    }
}
?>