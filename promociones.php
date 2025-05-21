<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Obtener promociones activas
$promociones = fetchAll("SELECT * FROM promociones WHERE fecha_fin >= CURDATE() AND activo = 1 ORDER BY fecha_fin ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Banner de Promociones -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>PROMOCIONES ESPECIALES</h1>
            <p>Aprovecha nuestras ofertas exclusivas por tiempo limitado</p>
        </div>
    </section>
    
    <!-- Lista de Promociones -->
    <section class="promociones-lista">
        <div class="container">
            <?php if (empty($promociones)): ?>
            <div class="no-promociones">
                <p>No hay promociones disponibles en este momento.</p>
            </div>
            <?php else: ?>
            <div class="promociones-grid">
                <?php foreach($promociones as $promo): ?>
                <div class="promocion-card">
                    <div class="promocion-img">
                        <img src="<?php echo $promo['imagen']; ?>" alt="<?php echo $promo['titulo']; ?>">
                        <div class="promocion-descuento">
                            <span><?php echo $promo['descuento']; ?>%</span>
                            <span>DESCUENTO</span>
                        </div>
                    </div>
                    <div class="promocion-info">
                        <h3><?php echo $promo['titulo']; ?></h3>
                        <p><?php echo $promo['descripcion']; ?></p>
                        <div class="promocion-fecha">
                            <p>Válido hasta: <?php echo date('d/m/Y', strtotime($promo['fecha_fin'])); ?></p>
                        </div>
                        <?php if (!empty($promo['codigo'])): ?>
                        <div class="promocion-codigo">
                            <span>Código: </span>
                            <strong><?php echo $promo['codigo']; ?></strong>
                        </div>
                        <?php endif; ?>
                        <div class="promocion-cta">
                            <a href="cita.php?promocion=<?php echo $promo['id']; ?>" class="btn btn-primary">Aprovechar Ahora</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Términos y Condiciones -->
    <section class="terminos-promociones">
        <div class="container">
            <div class="terminos-content">
                <h2>Términos y Condiciones</h2>
                <ul>
                    <li>Las promociones son válidas hasta la fecha indicada o hasta agotar existencias.</li>
                    <li>No son acumulables con otras promociones o descuentos.</li>
                    <li>Para hacer válida la promoción, menciona el código al momento de agendar tu cita o presentarlo en nuestro taller.</li>
                    <li>Nos reservamos el derecho de modificar o cancelar cualquier promoción sin previo aviso.</li>
                    <li>Aplican restricciones. Consulta términos completos en nuestro taller.</li>
                </ul>
            </div>
        </div>
    </section>
    
    <!-- Contacto Rápido -->
    <section class="contacto-rapido">
        <div class="container">
            <div class="contacto-content">
                <h2>¿QUIERES APROVECHAR NUESTRAS PROMOCIONES?</h2>
                <p>Llámanos ahora o agenda tu cita en línea</p>
                <div class="contacto-opciones">
                    <a href="tel:<?php echo getSiteConfig('telefono'); ?>" class="btn btn-accent">Llamar Ahora</a>
                    <a href="cita.php" class="btn btn-secondary">Agendar Cita</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
