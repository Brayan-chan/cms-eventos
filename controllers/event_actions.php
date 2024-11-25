<?php
session_start();
require '../config/db.php';

// Verificar el rol del usuario
if ($_SESSION['role'] !== 'admin_eventos' && $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_event':
            if (isset($_GET['id'])) {
                $event_id = $_GET['id'];
                $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
                $stmt->execute([$event_id]);
                $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($event) {
                    echo json_encode($event); // Asegúrate de que esto devuelva un JSON válido.
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Evento no encontrado']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
            exit();
        
        
        case 'create':
            if (!empty($_POST['title']) && !empty($_POST['date']) && !empty($_POST['location']) && !empty($_POST['description'])) {
                $title = $_POST['title'];
                $date = $_POST['date'];
                $location = $_POST['location'];
                $description = $_POST['description'];
                $is_published = isset($_POST['is_published']) ? 1 : 0;

                // Manejo de la imagen destacada
                $featured_image = null;
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../public/images/events/';
                    $file_name = basename($_FILES['featured_image']['name']);
                    $file_tmp_path = $_FILES['featured_image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    // Validar la extensión de la imagen
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array($file_ext, $allowed_exts)) {
                        // Crear un nombre único para la imagen
                        $new_file_name = uniqid('event_') . '.' . $file_ext;
                        $featured_image_path = $upload_dir . $new_file_name;

                        // Verificar el directorio y mover el archivo
                        if (!file_exists($upload_dir)) {
                            die('El directorio de subida no existe.');
                        }

                        if (!is_writable($upload_dir)) {
                            die('El directorio no tiene permisos de escritura.');
                        }

                        if (move_uploaded_file($file_tmp_path, $featured_image_path)) {
                            $featured_image = 'public/images/events/' . $new_file_name; // Ruta relativa
                        } else {
                            $_SESSION['error'] = 'No se pudo guardar la imagen. Intenta nuevamente.';
                            header('Location: ../public/dashboard_admin_eventos.php');
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = 'Formato de imagen no permitido. Solo se aceptan JPG, PNG, GIF o WEBP.';
                        header('Location: ../public/dashboard_admin_eventos.php');
                        exit();
                    }
                }

                // Insertar evento en la base de datos
                $stmt = $pdo->prepare("INSERT INTO events (title, featured_image, date, location, description, is_published) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $featured_image, $date, $location, $description, $is_published]);

                $_SESSION['success'] = 'Evento creado exitosamente.';
            } else {
                $_SESSION['error'] = 'Todos los campos son obligatorios.';
            }
            header('Location: ../public/dashboard_admin_eventos.php');
            exit();

        case 'update':
            if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['date']) && !empty($_POST['location']) && !empty($_POST['description'])) {
                $id = $_POST['id'];
                $title = $_POST['title'];
                $date = $_POST['date'];
                $location = $_POST['location'];
                $description = $_POST['description'];
                $is_published = isset($_POST['is_published']) ? 1 : 0;

                // Manejo de la imagen destacada
                $featured_image = null;
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../public/images/events/';
                    $file_name = basename($_FILES['featured_image']['name']);
                    $file_tmp_path = $_FILES['featured_image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array($file_ext, $allowed_exts)) {
                        $new_file_name = uniqid('event_') . '.' . $file_ext;
                        $featured_image_path = $upload_dir . $new_file_name;

                        if (move_uploaded_file($file_tmp_path, $featured_image_path)) {
                            $featured_image = 'public/images/events/' . $new_file_name;
                        }
                    }
                }

                // Actualizar datos del evento
                $stmt = $pdo->prepare("UPDATE events SET title = ?, featured_image = IFNULL(?, featured_image), date = ?, location = ?, description = ?, is_published = ? WHERE id = ?");
                $stmt->execute([$title, $featured_image, $date, $location, $description, $is_published, $id]);

                $_SESSION['success'] = 'Evento actualizado correctamente.';
            } else {
                $_SESSION['error'] = 'Todos los campos son obligatorios.';
            }
            header('Location: ../public/dashboard_admin_eventos.php');
            exit();
    }
}
?>
