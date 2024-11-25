<?php
session_start();
require '../config/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'usuario') {
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Consultar eventos publicados
$eventos_stmt = $pdo->query("SELECT * FROM events WHERE is_published = 1 ORDER BY date");
$eventos = $eventos_stmt->fetchAll();

// Consultar historial de pre-registros
$historial_stmt = $pdo->prepare("
    SELECT pr.prereg_id, e.title, e.date, e.location 
    FROM pre_registros pr
    JOIN events e ON pr.event_id = e.id
    WHERE pr.user_id = ?
");
$historial_stmt->execute([$_SESSION['user_id']]);
$historial = $historial_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuario</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <h1>Bienvenido, <?= htmlspecialchars($user['name']); ?>
            <img class="profile-picture" src="<?= htmlspecialchars($user['profile_picture'] ?? 'images/profiles/default.png'); ?>" alt="Foto de perfil">
        </h1>

        <nav>
            <a href="dashboard_user.php">Inicio</a>
            <a href="#configuracion">Configuración</a>
            <a href="../controllers/logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <main>
        <!-- Cartelera de Eventos -->
        <section>
            <h2>Cartelera de Eventos</h2>
            <div class="event-list">
                <?php foreach ($eventos as $evento) : ?>
                    <div class="event-card">
                        <h3><?= htmlspecialchars($evento['title']); ?></h3>
                        <!-- Mostrar la imagen destacada del evento -->
                        <img class="featured_image" src="<?= htmlspecialchars($evento['featured_image'] ?? 'images/events/default.png'); ?>" alt="Foto de perfil">
                        <p>Fecha: <?= date("d/m/Y H:i", strtotime($evento['date'])); ?></p>
                        <p>Lugar: <?= htmlspecialchars($evento['location']); ?></p>
                        <form method="POST" action="`../controllers/dashboard.php?action=pre-registrar&id=${eventId}`">
                            <input type="hidden" name="event_id" value="<?= $evento['id']; ?>">
                            <button type="submit">Pre-registrarse</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Historial de Pre-registros -->
        <section>
            <h2>Historial de Pre-registros</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Pre-registro</th>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $item) : ?>
                        <tr>
                            <td><?= htmlspecialchars($item['prereg_id']); ?></td>
                            <td><?= htmlspecialchars($item['title']); ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($item['date'])); ?></td>
                            <td><?= htmlspecialchars($item['location']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Configuración -->
        <section id="configuracion">
            <h2>Configuración</h2>
            <form method="POST" action="../controllers/dashboard.php?action=update_profile" enctype="multipart/form-data">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

                <label for="matricula">Matrícula:</label>
                <input type="text" id="matricula" name="matricula" value="<?= htmlspecialchars($user['matricula']); ?>" required>

                <label for="profile_picture">Foto de Perfil:</label>
                <input type="file" id="profile_picture" name="profile_picture">

                <button type="submit">Actualizar</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Universidad. Todos los derechos reservados.</p>
    </footer>
</body>

</html>