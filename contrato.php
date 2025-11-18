<?php
include 'conexion.php';

$mensaje = "";

// --- ACCIÓN: BORRAR ---
if (isset($_GET['accion']) && $_GET['accion'] == 'borrar' && isset($_GET['id'])) {
    $idContrato = $_GET['id'];
    $sql_borrar = "DELETE FROM contrato WHERE idContrato = ?";
    $stmt_borrar = $conexion->prepare($sql_borrar);
    $stmt_borrar->bind_param("i", $idContrato);
    if ($stmt_borrar->execute()) {
        $mensaje = "success|Contrato eliminado exitosamente.";
    } else {
        $mensaje = "danger|Error al eliminar el contrato.";
    }
    header("Location: contrato.php?msg=" . urlencode($mensaje));
    exit();
}

// --- ACCIÓN: OBTENER DATOS PARA EDITAR (AJAX) ---
if (isset($_GET['accion']) && $_GET['accion'] == 'obtener' && isset($_GET['id'])) {
    $idContrato = $_GET['id'];
    $sql = "SELECT * FROM contrato WHERE idContrato = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idContrato);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $contrato = $resultado->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($contrato);
    }
    exit();
}

// --- ACCIÓN: CREAR O ACTUALIZAR ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    $cargo = $_POST['cargo'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $sueldo = $_POST['sueldo'];
    $estado_vigencia = isset($_POST['estado_vigencia']) ? 1 : 0;
    $fecha_finalizacion = $_POST['fecha_finalizacion'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $horario_laboral = $_POST['horario_laboral'];
    $modalidad = $_POST['modalidad'];
    $idEmpleado = $_POST['idEmpleado'];
    
    if (empty($fecha_finalizacion)) {
        $fecha_finalizacion = NULL;
    }
    
    try {
        if ($accion == 'crear') {
            $sql = "INSERT INTO contrato (Cargo, Fecha_inicio, Sueldo, Estado_vigencia, Fecha_finalizacion, Tipo_contrato, Horario_laboral, Modalidad, idEmpleado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssdissssi", $cargo, $fecha_inicio, $sueldo, $estado_vigencia, $fecha_finalizacion, $tipo_contrato, $horario_laboral, $modalidad, $idEmpleado);
            $stmt->execute();
            $mensaje = "success|Contrato creado exitosamente.";
        } elseif ($accion == 'actualizar') {
            $idContrato = $_POST['idContrato'];
            $sql = "UPDATE contrato SET Cargo = ?, Fecha_inicio = ?, Sueldo = ?, Estado_vigencia = ?, 
                    Fecha_finalizacion = ?, Tipo_contrato = ?, Horario_laboral = ?, Modalidad = ?, idEmpleado = ?
                    WHERE idContrato = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssdissssii", $cargo, $fecha_inicio, $sueldo, $estado_vigencia, $fecha_finalizacion, $tipo_contrato, $horario_laboral, $modalidad, $idEmpleado, $idContrato);
            $stmt->execute();
            $mensaje = "success|Contrato actualizado exitosamente.";
        }
    } catch (Exception $e) {
        $mensaje = "danger|Error: " . $e->getMessage();
    }
    
    header("Location: contrato.php?msg=" . urlencode($mensaje));
    exit();
}

// --- LEER EMPLEADOS (para el select) ---
$lista_empleados = [];
$sql_empleados = "SELECT idEmpleado, Nombre, Apellido FROM empleado ORDER BY Nombre";
$resultado_empleados = $conexion->query($sql_empleados);
if ($resultado_empleados && $resultado_empleados->num_rows > 0) {
    while($fila = $resultado_empleados->fetch_assoc()) {
        $lista_empleados[] = $fila;
    }
}

// --- LEER CONTRATOS ---
$lista_contratos = [];
$sql_contratos = "SELECT c.*, e.Nombre, e.Apellido 
                  FROM contrato c
                  JOIN empleado e ON c.idEmpleado = e.idEmpleado
                  ORDER BY c.Fecha_inicio DESC";
$resultado_contratos = $conexion->query($sql_contratos);
if ($resultado_contratos && $resultado_contratos->num_rows > 0) {
    while($fila = $resultado_contratos->fetch_assoc()) {
        $lista_contratos[] = $fila;
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contratos - Talentum</title>
    <link rel="icon" href="ruta/hacia/tu/mi_icono.png" type="image/png">
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
                        <a class="nav-link" href="empleado.php">Empleados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contrato.php">Contratos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title">Gestión de Contratos</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contratoModal" onclick="resetForm()">
                <i class="bi bi-plus-circle me-2"></i>Agregar Contrato
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
                                <th>Empleado</th>
                                <th>Cargo</th>
                                <th>Sueldo</th>
                                <th>Tipo</th>
                                <th>Modalidad</th>
                                <th>Fecha Inicio</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lista_contratos)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        No hay contratos registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lista_contratos as $contrato): ?>
                                    <tr>
                                        <td><?php echo $contrato['idContrato']; ?></td>
                                        <td><strong><?php echo $contrato['Nombre'] . ' ' . $contrato['Apellido']; ?></strong></td>
                                        <td><?php echo $contrato['Cargo']; ?></td>
                                        <td>$<?php echo number_format($contrato['Sueldo'], 0, ',', '.'); ?></td>
                                        <td><?php echo $contrato['Tipo_contrato']; ?></td>
                                        <td>
                                            <?php 
                                            $modalidad_icon = [
                                                'Presencial' => 'building',
                                                'Remoto' => 'laptop',
                                                'Híbrido' => 'phone-laptop'
                                            ];
                                            $icon = $modalidad_icon[$contrato['Modalidad']] ?? 'circle';
                                            ?>
                                            <i class="bi bi-<?php echo $icon; ?> me-1"></i><?php echo $contrato['Modalidad']; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($contrato['Fecha_inicio'])); ?></td>
                                        <td>
                                            <?php if ($contrato['Estado_vigencia'] == 1): ?>
                                                <span class="badge bg-success">Vigente</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning" onclick="editarContrato(<?php echo $contrato['idContrato']; ?>)" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="contrato.php?accion=borrar&id=<?php echo $contrato['idContrato']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('¿Estás seguro de eliminar este contrato?')"
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

    <!-- Modal para Agregar/Editar Contrato -->
    <div class="modal fade" id="contratoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Agregar Nuevo Contrato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="contrato.php" method="POST" id="contratoForm">
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="crear">
                        <input type="hidden" name="idContrato" id="idContrato">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="idEmpleado" class="form-label">Empleado *</label>
                                <select class="form-select" id="idEmpleado" name="idEmpleado" required>
                                    <option value="">Seleccione un empleado...</option>
                                    <?php foreach ($lista_empleados as $empleado): ?>
                                        <option value="<?php echo $empleado['idEmpleado']; ?>">
                                            <?php echo $empleado['Nombre'] . ' ' . $empleado['Apellido']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cargo" class="form-label">Cargo *</label>
                                <input type="text" class="form-control" id="cargo" name="cargo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sueldo" class="form-label">Sueldo *</label>
                                <input type="number" step="0.01" class="form-control" id="sueldo" name="sueldo" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_contrato" class="form-label">Tipo de Contrato *</label>
                                <select class="form-select" id="tipo_contrato" name="tipo_contrato" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Término fijo">Término fijo</option>
                                    <option value="Término indefinido">Término indefinido</option>
                                    <option value="Obra o labor">Obra o labor</option>
                                    <option value="Prestación de servicios">Prestación de servicios</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modalidad" class="form-label">Modalidad *</label>
                                <select class="form-select" id="modalidad" name="modalidad" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Presencial">Presencial</option>
                                    <option value="Remoto">Remoto</option>
                                    <option value="Híbrido">Híbrido</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="horario_laboral" class="form-label">Horario Laboral *</label>
                            <input type="text" class="form-control" id="horario_laboral" name="horario_laboral" 
                                   placeholder=" Ej: 32 - Cantidad de horas semanles" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_finalizacion" class="form-label">Fecha de Finalización (Opcional)</label>
                                <input type="date" class="form-control" id="fecha_finalizacion" name="fecha_finalizacion">
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="estado_vigencia" name="estado_vigencia" value="1" checked>
                            <label class="form-check-label" for="estado_vigencia">Contrato Vigente</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Guardar Contrato</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('contratoForm').reset();
            document.getElementById('accion').value = 'crear';
            document.getElementById('idContrato').value = '';
            document.getElementById('modalTitle').textContent = 'Agregar Nuevo Contrato';
            document.getElementById('btnSubmit').textContent = 'Guardar Contrato';
            document.getElementById('estado_vigencia').checked = true;
        }

        function editarContrato(id) {
            fetch('contrato.php?accion=obtener&id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('accion').value = 'actualizar';
                    document.getElementById('idContrato').value = data.idContrato;
                    document.getElementById('idEmpleado').value = data.idEmpleado;
                    document.getElementById('cargo').value = data.Cargo;
                    document.getElementById('sueldo').value = data.Sueldo;
                    document.getElementById('tipo_contrato').value = data.Tipo_contrato;
                    document.getElementById('modalidad').value = data.Modalidad;
                    document.getElementById('horario_laboral').value = data.Horario_laboral;
                    document.getElementById('fecha_inicio').value = data.Fecha_inicio;
                    document.getElementById('fecha_finalizacion').value = data.Fecha_finalizacion || '';
                    document.getElementById('estado_vigencia').checked = data.Estado_vigencia == 1;
                    
                    document.getElementById('modalTitle').textContent = 'Editar Contrato';
                    document.getElementById('btnSubmit').textContent = 'Actualizar Contrato';
                    
                    new bootstrap.Modal(document.getElementById('contratoModal')).show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
