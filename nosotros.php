<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Banner de Nosotros -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>SOBRE NOSOTROS</h1>
            <p>Conoce nuestra historia y compromiso con la calidad</p>
        </div>
    </section>
    
    <!-- Historia -->
    <section class="nosotros-historia">
        <div class="container">
            <div class="historia-content">
                <div class="historia-texto">
                    <h2>Nuestra Historia</h2>
                    <p>Fundado en 2005, nuestro taller mecánico comenzó como un pequeño negocio familiar con la visión de ofrecer servicios de calidad a precios justos. A lo largo de los años, hemos crecido y evolucionado, pero nuestra filosofía sigue siendo la misma: proporcionar un servicio honesto, transparente y profesional.</p>
                    <p>Nos especializamos en servicios de cambio de aceite y venta de llantas, áreas en las que hemos desarrollado una experiencia incomparable. Nuestro compromiso con la excelencia nos ha permitido ganar la confianza de miles de clientes que nos eligen día tras día para el mantenimiento de sus vehículos.</p>
                    <p>Hoy en día, contamos con un equipo de técnicos certificados y utilizamos equipos de última generación para garantizar que cada servicio se realice con los más altos estándares de calidad.</p>
                </div>
                <div class="historia-imagen">
                    <img src="img/nosotros/historia.jpg" alt="Historia del Taller">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Misión, Visión y Valores -->
    <section class="nosotros-mvv">
        <div class="container">
            <div class="mvv-grid">
                <div class="mvv-card">
                    <h2>Misión</h2>
                    <p>Proporcionar servicios de mantenimiento automotriz de alta calidad, con honestidad y transparencia, garantizando la satisfacción de nuestros clientes y contribuyendo a la seguridad vial.</p>
                </div>
                <div class="mvv-card">
                    <h2>Visión</h2>
                    <p>Ser reconocidos como el taller mecánico líder en nuestra región, destacando por la excelencia en el servicio, la innovación constante y el compromiso con nuestros clientes y el medio ambiente.</p>
                </div>
                <div class="mvv-card">
                    <h2>Valores</h2>
                    <ul>
                        <li><strong>Honestidad:</strong> Trabajamos con transparencia y ética en cada servicio.</li>
                        <li><strong>Calidad:</strong> Nos comprometemos a ofrecer el mejor servicio posible.</li>
                        <li><strong>Responsabilidad:</strong> Cumplimos con nuestros compromisos y plazos.</li>
                        <li><strong>Innovación:</strong> Buscamos constantemente mejorar nuestros procesos y servicios.</li>
                        <li><strong>Respeto:</strong> Valoramos a nuestros clientes, empleados y al medio ambiente.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Nuestro Equipo -->
    <section class="nosotros-equipo">
        <div class="container">
            <h2 class="section-title">NUESTRO EQUIPO</h2>
            <div class="equipo-grid">
                <div class="miembro-card">
                    <div class="miembro-img">
                        <img src="img/nosotros/tecnico1.jpg" alt="Juan Pérez">
                    </div>
                    <div class="miembro-info">
                        <h3>Juan Pérez</h3>
                        <p class="cargo">Director General</p>
                        <p>Con más de 20 años de experiencia en el sector automotriz, Juan lidera nuestro equipo con pasión y dedicación.</p>
                    </div>
                </div>
                <div class="miembro-card">
                    <div class="miembro-img">
                        <img src="img/nosotros/tecnico2.jpg" alt="María Rodríguez">
                    </div>
                    <div class="miembro-info">
                        <h3>María Rodríguez</h3>
                        <p class="cargo">Gerente de Servicio</p>
                        <p>María se asegura de que cada cliente reciba un servicio excepcional y personalizado.</p>
                    </div>
                </div>
                <div class="miembro-card">
                    <div class="miembro-img">
                        <img src="img/nosotros/tecnico3.jpg" alt="Carlos Gómez">
                    </div>
                    <div class="miembro-info">
                        <h3>Carlos Gómez</h3>
                        <p class="cargo">Técnico Principal</p>
                        <p>Certificado por las principales marcas, Carlos lidera nuestro equipo técnico con precisión y eficiencia.</p>
                    </div>
                </div>
                <div class="miembro-card">
                    <div class="miembro-img">
                        <img src="img/nosotros/tecnico4.jpg" alt="Ana Martínez">
                    </div>
                    <div class="miembro-info">
                        <h3>Ana Martínez</h3>
                        <p class="cargo">Especialista en Llantas</p>
                        <p>Ana conoce todo sobre llantas y ayuda a nuestros clientes a encontrar la mejor opción para sus vehículos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Instalaciones -->
    <section class="nosotros-instalaciones">
        <div class="container">
            <h2 class="section-title">NUESTRAS INSTALACIONES</h2>
            <div class="instalaciones-grid">
                <div class="instalacion-img">
                    <img src="img/nosotros/instalacion1.jpg" alt="Área de Servicio">
                    <p>Área de Servicio</p>
                </div>
                <div class="instalacion-img">
                    <img src="img/nosotros/instalacion2.jpg" alt="Sala de Espera">
                    <p>Sala de Espera</p>
                </div>
                <div class="instalacion-img">
                    <img src="img/nosotros/instalacion3.jpg" alt="Almacén de Llantas">
                    <p>Almacén de Llantas</p>
                </div>
                <div class="instalacion-img">
                    <img src="img/nosotros/instalacion4.jpg" alt="Equipos Especializados">
                    <p>Equipos Especializados</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contacto Rápido -->
    <section class="contacto-rapido">
        <div class="container">
            <div class="contacto-content">
                <h2>¿QUIERES CONOCER MÁS SOBRE NOSOTROS?</h2>
                <p>Visítanos o contáctanos para más información</p>
                <div class="contacto-opciones">
                    <a href="contacto.php" class="btn btn-accent">Contactar</a>
                    <a href="cita.php" class="btn btn-secondary">Agendar Cita</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
