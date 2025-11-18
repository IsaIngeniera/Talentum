<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talentum - Sistema de Gestión de Empleados</title>
    <link rel="icon" href="images/Logo.jpg" type="image/jpg">
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
                        <a class="nav-link active" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="empleado.php">Empleados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contrato.php">Contratos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Carousel -->
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/img3.png" class="d-block w-100" alt="Gestión HR">
                    <div class="carousel-caption">
                        <h1 class="display-3 fw-bold mb-4">TALENTUM</h1>
                        <p class="lead">Sistema de Gestión de Empleados</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/img2.png" class="d-block w-100" alt="Sistema Integrado">
                    <div class="carousel-caption">
                        <h1 class="display-3 fw-bold mb-4">GESTIÓN INTELIGENTE</h1>
                        <p class="lead">Control total de tu equipo de trabajo</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/img0.jpg" class="d-block w-100" alt="Tecnología Avanzada">
                    <div class="carousel-caption">
                        <h1 class="display-3 fw-bold mb-4">EFICIENCIA TOTAL</h1>
                        <p class="lead">Simplifica tus procesos de GH</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <h2 class="section-title mb-4">Sobre Talentum</h2>
                        <p class="lead text-muted">
                            Talentum es una empresa líder en soluciones de gestión de recursos humanos que se enfoca en proporcionar una herramienta fácil e intuitiva para el área de gestión humana. Nuestro objetivo es que el ingreso de empleados y el manejo de información sea más útil, sencillo y eficiente.
                        </p>
                        <p class="text-muted">
                            Con Talentum, las empresas pueden optimizar sus procesos de contratación, seguimiento de empleados y gestión de contratos, todo desde una plataforma centralizada y de fácil acceso. Creemos que la tecnología debe simplificar el trabajo, no complicarlo, por eso diseñamos cada funcionalidad pensando en la experiencia del usuario.
                        </p>
                    </div>

                    <div class="row g-4 mt-5">
                        <div class="col-md-3 col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <h5>Gestión de Empleados</h5>
                                <p class="text-muted small">Control completo de tu personal</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <h5>Contratos</h5>
                                <p class="text-muted small">Administra toda la documentación</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <h5>Reportes</h5>
                                <p class="text-muted small">Análisis y métricas en tiempo real</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <h5>Seguridad</h5>
                                <p class="text-muted small">Datos protegidos y encriptados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Nuestro Equipo</h2>
                <p class="text-muted">Conoce a las personas detrás de Talentum</p>
            </div>
            <div class="row justify-content-center g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="team-member text-center">
                        <div class="team-photo mx-auto">
                            <img src="images/isac.jpeg" alt="Isabella Cadavid" class="img-fluid">
                        </div>
                        <h5 class="mt-3 mb-1">Isabella Cadavid Posada</h5>
                        <p class="text-muted small">Desarrolladora</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-member text-center">
                        <div class="team-photo mx-auto">
                            <img src="images/isao.jpeg" alt="Isabella Ocampo" class="img-fluid">
                        </div>
                        <h5 class="mt-3 mb-1">Isabella Ocampo Sánchez</h5>
                        <p class="text-muted small">Desarrolladora</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-member text-center">
                        <div class="team-photo mx-auto">
                            <img src="images/wendy.jpeg" alt="Wendy Atehortua" class="img-fluid">
                        </div>
                        <h5 class="mt-3 mb-1">Wendy Vanessa Atehortua Chaverra</h5>
                        <p class="text-muted small">Desarrolladora</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-member text-center">
                        <div class="team-photo mx-auto">
                            <img src="images/pablo.jpeg" alt="Pablo Benitez" class="img-fluid">
                        </div>
                        <h5 class="mt-3 mb-1">Pablo José Benitez Trujillo</h5>
                        <p class="text-muted small">Desarrollador</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row py-4">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2025 Talentum. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Sistema de Gestión de Empleados</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
