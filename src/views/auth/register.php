<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Crear Cuenta</h3>
                    <form action="../../controllers/RegisterController.php" method="POST">
                        <div class="mb-3">
                            <label for="nombre_completo" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_compania" class="form-label">Nombre de la Compañía</label>
                            <input type="text" class="form-control" id="nombre_compania" name="nombre_compania" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol_id" class="form-label">Rol de Usuario</label>
                            <select class="form-select" id="rol_id" name="rol_id" required>
                                <option value="" disabled selected>-- Elige un rol --</option>
                                <option value="1">Administrador</option>
                                <option value="2">Analista de Datos</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Registrarme</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                         <a href="../../public/index.php">¿Ya tienes cuenta? Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>