<?php
session_start();
require '../config/db.php';

// Verificar rol
if ($_SESSION['role'] !== 'admin_eventos' && $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    header('Location: ../index.php');
    exit();
}

// Obtener la lista de eventos
$stmt = $pdo->prepare("SELECT * FROM events ORDER BY created_at DESC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Eventos</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <header>
        <h1>Gestión de Eventos</h1>
        <?php if (isset($_SESSION['success'])) : ?>
            <p class="success"><?php echo $_SESSION['success'];
                                unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])) : ?>
            <p class="error"><?php echo $_SESSION['error'];
                                unset($_SESSION['error']); ?></p>
        <?php endif; ?>
    </header>

    <main>
        <a href="../controllers/logout.php">Logout</a>
        <!-- Crear Evento -->
        <section>
            <h2>Crear Nuevo Evento</h2>
            <form id="createEventForm" action="../controllers/event_actions.php?action=create" method="POST" enctype="multipart/form-data">
                <label for="title">Título:</label>
                <input type="text" name="title" id="title" required>

                <label for="date">Fecha:</label>
                <input type="datetime-local" name="date" id="date" required>

                <label for="location">Ubicación:</label>
                <input type="text" name="location" id="location" required>

                <label for="description">Descripción:</label>
                <textarea name="description" id="description" required></textarea>

                <label for="featured_image">Imagen destacada:</label>
                <input type="file" name="featured_image" id="featured_image">

                <label for="is_published">
                    <input type="checkbox" name="is_published" id="is_published">
                    Publicar
                </label>

                <button type="submit">Crear Evento</button>
            </form>
        </section>

        <!-- Listar eventos -->
        <section>
            <h2>Historial de Eventos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Ubicación</th>
                        <th>Publicado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo htmlspecialchars($event['date']); ?></td>
                            <td><?php echo htmlspecialchars($event['location']); ?></td>
                            <td><?php echo $event['is_published'] ? 'Sí' : 'No'; ?></td>
                            <td>
                                <button class="editEventBtn" data-id="<?php echo $event['id']; ?>">Editar</button>
                                <form action="../controllers/event_actions.php?action=delete&id=<?php echo $event['id']; ?>" method="POST" style="display:inline;">
                                    <button type="submit" class="deleteEventBtn">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Modal para editar eventos -->
    <div id="eventModal" class="modal hidden">
        <div class="modal-content">
            <form id="editEventForm" method="POST" action="../controllers/event_actions.php?action=update" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editEventId">

                <label for="editTitle">Título:</label>
                <input type="text" name="title" id="editTitle" required>

                <label for="editDate">Fecha:</label>
                <input type="datetime-local" name="date" id="editDate" required>

                <label for="editLocation">Ubicación:</label>
                <input type="text" name="location" id="editLocation" required>

                <label for="editDescription">Descripción:</label>
                <textarea name="description" id="editDescription" required></textarea>

                <label for="editFeaturedImage">Imagen destacada:</label>
                <input type="file" name="featured_image" id="editFeaturedImage">

                <label for="editIsPublished">
                    <input type="checkbox" name="is_published" id="editIsPublished">
                    Publicar
                </label>

                <button type="submit">Guardar Cambios</button>
                <button type="button" id="closeModalBtn">Cerrar</button>
            </form>
        </div>
    </div>

    <script src="js/dashboard_admin_eventos.js"></script>
</body>

</html>