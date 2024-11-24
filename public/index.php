<?php
session_start();
require '../config/db.php';

// Verificar si el usuario ya ha iniciado sesión
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin_eventos':
            header('Location: dashboard_admin_eventos.php');
            exit();
        case 'admin':
            header('Location: dashboard_admin.php');
            exit();
        case 'superadmin':
            header('Location: dashboard_superadmin.php');
            exit();
        default: // Usuario normal
            header('Location: dashboard_user.php');
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Culturales - Universidad</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Bienvenidos a la Plataforma de Eventos Culturales</h1>
        <p>Conéctate con la cultura y participa en eventos organizados por nuestra universidad.</p>
    </header>

    <main>
        <section>
            <h2>Eventos Destacados</h2>
            <div class="event-list">
                <?php
                // Consultar eventos destacados de la base de datos
                $stmt = $pdo->query("SELECT id, title, date, location FROM events WHERE is_published = 1 ORDER BY date LIMIT 3");
                $events = $stmt->fetchAll();

                if (count($events) > 0):
                    foreach ($events as $event):
                ?>
                    <div class="event-card">
                        <h3><?= htmlspecialchars($event['title']); ?></h3>
                        <p>Fecha: <?= date("d/m/Y H:i", strtotime($event['date'])); ?></p>
                        <p>Lugar: <?= htmlspecialchars($event['location']); ?></p>
                        <a href="login.php" class="btn">Ver Más</a>
                    </div>
                <?php
                    endforeach;
                else:
                ?>
                    <p>No hay eventos destacados en este momento.</p>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h2>Comienza a participar</h2>
            <div class="actions">
                <a href="login.php" class="btn">Iniciar Sesión</a>
                <a href="register.php" class="btn">Registrarse</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Universidad. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
