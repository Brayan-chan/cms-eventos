<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<form method="POST" action="../controllers/auth.php?action=register">
    <label for="name">Nombre:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Correo Electrónico:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required>

    <label for="matricula">Matrícula:</label>
    <input type="text" id="matricula" name="matricula" required>

    <label for="department">Departamento:</label>
    <input type="text" id="department" name="department">

    <label for="institution">Institución:</label>
    <input type="text" id="institution" name="institution">

    <label for="career">Carrera:</label>
    <input type="text" id="career" name="career">

    <button type="submit">Registrar</button>
</form>

</body>
</html>