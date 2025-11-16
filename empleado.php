<?php
include 'conexion.php';

$mensaje = "";
$empleado_a_editar = null;

// --- ACCIÓN: BORRAR ---
if (isset($_GET['accion']) && $_GET['accion'] == 'borrar' && isset($_GET['id'])) {
    $idEmpleado = $_GET['id'];
    
    $conexion->begin_transaction();
    try {
        // Primero eliminar correos y teléfonos
        $sql_del_correos = "DELETE FROM correoelectronico WHERE idEmpleado = ?";
        $stmt_del_correos = $conexion->prepare($sql_del_correos);
        $stmt_del_correos->bind_param("i", $idEmpleado);
        $stmt_del_correos->execute();
        
        $sql_del_telefonos = "DELETE FROM telefono WHERE idEmpleado = ?";
        $stmt_del_telefonos = $conexion->prepare($sql_del_telefonos);
        $stmt_del_telefonos->bind_param("i", $idEmpleado);
        $stmt_del_telefonos->execute();
        
        // Luego eliminar el empleado
        $sql_borrar = "DELETE FROM empleado WHERE idEmpleado = ?";
        $stmt_borrar = $conexion->prepare($sql_borrar);
        $stmt_borrar->bind_param("i", $idEmpleado);
        $stmt_borrar->execute();
        
        $conexion->commit();
        $mensaje = "success|Empleado eliminado exitosamente.";
    } catch (Exception $e) {
        $conexion->rollback();
        $mensaje = "danger|Error al eliminar el empleado: " . $e->getMessage();
    }
    header("Location: empleado.php?msg=" . urlencode($mensaje));
    exit();
}

// --- ACCIÓN: CARGAR DATOS PARA EDITAR (AJAX) ---
if (isset($_GET['accion']) && $_GET['accion'] == 'obtener' && isset($_GET['id'])) {
    $idEmpleado = $_GET['id'];
    
    $sql = "SELECT e.*, 
            (SELECT Correo FROM correoelectronico WHERE idEmpleado = e.idEmpleado LIMIT 1) as correo,
            (SELECT numero FROM telefono WHERE idEmpleado = e.idEmpleado LIMIT 1) as telefono
            FROM empleado e WHERE e.idEmpleado = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idEmpleado);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $empleado = $resultado->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($empleado);
    }
    exit();
}

// --- ACCIÓN: CREAR O ACTUALIZAR ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $fecha_nac = $_POST['fecha_nacimiento'];
    $tipo_id = $_POST['tipo_identificacion'];
    $num_id = $_POST['numero_identificacion'];
    $direccion = $_POST['direccion_residencia'];
    $eps = $_POST['eps'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $id_jefe = $_POST['idEmpleadoResponsable'];
    
    if (empty($id_jefe)) {
        $id_jefe = NULL;
    }
    
    $conexion->begin_transaction();
    try {
        if ($accion == 'crear') {
            // Insertar empleado
            $sql_empleado = "INSERT INTO empleado (Nombre, Apellido, Genero, Fecha_Nacimiento, Tipo_Identificacion, Numero_Identificacion, Direccion_Residencia, EPS, idEmpleadoResponsable) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_empleado = $conexion->prepare($sql_empleado);
            $stmt_empleado->bind_param("sssssissi", $nombre, $apellido, $genero, $fecha_nac, $tipo_id, $num_id, $direccion, $eps, $id_jefe);
            $stmt_empleado->execute();
            $idNuevoEmpleado = $conexion->insert_id;
            
            // Insertar correo
            $sql_correo = "INSERT INTO correoelectronico (Correo, idEmpleado) VALUES (?, ?)";
            $stmt_correo = $conexion->prepare($sql_correo);
            $stmt_correo->bind_param("si", $correo, $idNuevoEmpleado);
            $stmt_correo->execute();
            
            // Insertar teléfono
            $sql_telefono = "INSERT INTO telefono (numero, idEmpleado) VALUES (?, ?)";
            $stmt_telefono = $conexion->prepare($sql_telefono);
            $stmt_telefono->bind_param("ii", $telefono, $idNuevoEmpleado);
            $stmt_telefono->execute();
            
            $conexion->commit();
            $mensaje = "success|Empleado creado exitosamente.";
        } elseif ($accion == 'actualizar') {
            $idEmpleado = $_POST['idEmpleado'];
            
            // Actualizar empleado
            $sql = "UPDATE empleado SET Nombre = ?, Apellido = ?, Genero = ?, Fecha_Nacimiento = ?, 
                    Tipo_Identificacion = ?, Numero_Identificacion = ?, Direccion_Residencia = ?, EPS = ?, 
                    idEmpleadoResponsable = ? WHERE idEmpleado = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssssissii", $nombre, $apellido, $genero, $fecha_nac, $tipo_id, $num_id, $direccion, $eps, $id_jefe, $idEmpleado);
            $stmt->execute();
            
            // Actualizar correo
            $sql_correo = "UPDATE correoelectronico SET Correo = ? WHERE idEmpleado = ?";
            $stmt_correo = $conexion->prepare($sql_correo);
            $stmt_correo->bind_param("si", $correo, $idEmpleado);
            $stmt_correo->execute();
            
            // Actualizar teléfono
            $sql_telefono = "UPDATE telefono SET numero = ? WHERE idEmpleado = ?";
            $stmt_telefono = $conexion->prepare($sql_telefono);
            $stmt_telefono->bind_param("ii", $telefono, $idEmpleado);
            $stmt_telefono->execute();
            
            $conexion->commit();
            $mensaje = "success|Empleado actualizado exitosamente.";
        }
    } catch (Exception $e) {
        $conexion->rollback();
        $mensaje = "danger|Error: " . $e->getMessage();
    }
    
    header("Location: empleado.php?msg=" . urlencode($mensaje));
    exit();
}

// --- LEER EMPLEADOS ---
$lista_empleados = [];
$sql_empleados = "SELECT e.*, 
                  (SELECT Correo FROM correoelectronico WHERE idEmpleado = e.idEmpleado LIMIT 1) as correo,
                  (SELECT numero FROM telefono WHERE idEmpleado = e.idEmpleado LIMIT 1) as telefono,
                  jefe.Nombre as JefeNombre, jefe.Apellido as JefeApellido
                  FROM empleado e
                  LEFT JOIN empleado jefe ON e.idEmpleadoResponsable = jefe.idEmpleado
                  ORDER BY e.Nombre";
$resultado_empleados = $conexion->query($sql_empleados);
if ($resultado_empleados && $resultado_empleados->num_rows > 0) {
    while($fila = $resultado_empleados->fetch_assoc()) {
        $lista_empleados[] = $fila;
    }
}

// Lista de jefes para el select
$lista_jefes = [];
$sql_jefes = "SELECT idEmpleado, Nombre, Apellido FROM empleado ORDER BY Nombre";
$resultado_jefes = $conexion->query($sql_jefes);
if ($resultado_jefes && $resultado_jefes->num_rows > 0) {
    while($fila = $resultado_jefes->fetch_assoc()) {
        $lista_jefes[] = $fila;
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados - Talentum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/Logo.jpg" alt="Talentum Logo" class="logo-img">
                <span class="ms-2 fw-bold">TALENTUM</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="empleado.php">Empleados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contrato.php">Contratos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title">Gestión de Empleados</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#empleadoModal" onclick="resetForm()">
                <i class="bi bi-plus-circle me-2"></i>Agregar Empleado
            </button>
        </div>

        <?php if (isset($_GET['msg'])): 
            $msg_parts = explode('|', $_GET['msg']);
            $tipo = $msg_parts[0];
            $texto = $msg_parts[1];
        ?>
        <div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show" role="alert">
            <?php echo $texto; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Identificación</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>EPS</th>
                                <th>Jefe Inmediato</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lista_empleados)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        No hay empleados registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lista_empleados as $empleado): ?>
                                    <tr>
                                        <td><?php echo $empleado['idEmpleado']; ?></td>
                                        <td>
                                            <strong><?php echo $empleado['Nombre'] . ' ' . $empleado['Apellido']; ?></strong>
                                            <br><small class="text-muted"><?php echo $empleado['Genero']; ?></small>
                                        </td>
                                        <td>
                                            <?php echo $empleado['Tipo_Identificacion']; ?>
                                            <br><small class="text-muted"><?php echo $empleado['Numero_Identificacion']; ?></small>
                                        </td>
                                        <td><?php echo $empleado['correo'] ?? '-'; ?></td>
                                        <td><?php echo $empleado['telefono'] ?? '-'; ?></td>
                                        <td><?php echo $empleado['EPS']; ?></td>
                                        <td>
                                            <?php 
                                            if ($empleado['JefeNombre']) {
                                                echo $empleado['JefeNombre'] . ' ' . $empleado['JefeApellido'];
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning" onclick="editarEmpleado(<?php echo $empleado['idEmpleado']; ?>)" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="empleado.php?accion=borrar&id=<?php echo $empleado['idEmpleado']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('¿Estás seguro de eliminar este empleado?')"
                                               title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar/Editar Empleado -->
    <div class="modal fade" id="empleadoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Agregar Nuevo Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="empleado.php" method="POST" id="empleadoForm">
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="crear">
                        <input type="hidden" name="idEmpleado" id="idEmpleado">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido *</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="genero" class="form-label">Género *</label>
                                <select class="form-select" id="genero" name="genero" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_identificacion" class="form-label">Tipo de Identificación *</label>
                                <select class="form-select" id="tipo_identificacion" name="tipo_identificacion" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_identificacion" class="form-label">Número de Identificación *</label>
                                <input type="text" class="form-control" id="numero_identificacion" name="numero_identificacion" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="direccion_residencia" class="form-label">Dirección *</label>
                                <input type="text" class="form-control" id="direccion_residencia" name="direccion_residencia" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="eps" class="form-label">EPS *</label>
                                <input type="text" class="form-control" id="eps" name="eps" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="correo" class="form-label">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono *</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="idEmpleadoResponsable" class="form-label">Jefe Inmediato (Opcional)</label>
                            <select class="form-select" id="idEmpleadoResponsable" name="idEmpleadoResponsable">
                                <option value="">-- Ninguno --</option>
                                <?php foreach ($lista_jefes as $jefe): ?>
                                    <option value="<?php echo $jefe['idEmpleado']; ?>">
                                        <?php echo $jefe['Nombre'] . ' ' . $jefe['Apellido']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Guardar Empleado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('empleadoForm').reset();
            document.getElementById('accion').value = 'crear';
            document.getElementById('idEmpleado').value = '';
            document.getElementById('modalTitle').textContent = 'Agregar Nuevo Empleado';
            document.getElementById('btnSubmit').textContent = 'Guardar Empleado';
        }

        function editarEmpleado(id) {
            fetch('empleado.php?accion=obtener&id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('accion').value = 'actualizar';
                    document.getElementById('idEmpleado').value = data.idEmpleado;
                    document.getElementById('nombre').value = data.Nombre;
                    document.getElementById('apellido').value = data.Apellido;
                    document.getElementById('genero').value = data.Genero;
                    document.getElementById('fecha_nacimiento').value = data.Fecha_Nacimiento;
                    document.getElementById('tipo_identificacion').value = data.Tipo_Identificacion;
                    document.getElementById('numero_identificacion').value = data.Numero_Identificacion;
                    document.getElementById('direccion_residencia').value = data.Direccion_Residencia;
                    document.getElementById('eps').value = data.EPS;
                    document.getElementById('correo').value = data.correo || '';
                    document.getElementById('telefono').value = data.telefono || '';
                    document.getElementById('idEmpleadoResponsable').value = data.idEmpleadoResponsable || '';
                    
                    document.getElementById('modalTitle').textContent = 'Editar Empleado';
                    document.getElementById('btnSubmit').textContent = 'Actualizar Empleado';
                    
                    new bootstrap.Modal(document.getElementById('empleadoModal')).show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
