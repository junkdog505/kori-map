<?php
/**
 * Vista pública de las tarjetas de agencias
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Obtener el número de página actual
$paged = isset($_GET['agencias_page']) ? intval($_GET['agencias_page']) : 1;
if ($paged < 1) $paged = 1;

// Número de items por página
$items_per_page = intval($atts['items_per_page']);
?>

<div class="twf-agencias-cards-container" data-ubicacion="<?php echo esc_attr($atts['ubicacion']); ?>" data-servicios="<?php echo esc_attr($atts['servicios']); ?>" data-page="<?php echo esc_attr($paged); ?>" data-items-per-page="<?php echo esc_attr($items_per_page); ?>">
    <!-- Las cards se cargarán dinámicamente aquí -->
    <div class="twf-agencias-cards-grid"></div>
    
    <div class="twf-agencias-pagination">
        <!-- La paginación se generará dinámicamente -->
    </div>
</div>