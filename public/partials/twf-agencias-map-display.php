<?php
/**
 * Vista pública del mapa de agencias
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}
?>

<div id="twf-agencias-map" class="twf-agencias-map" style="height: <?php echo esc_attr($atts['height']); ?>px;" data-zoom="<?php echo esc_attr($atts['zoom']); ?>" data-ubicacion="<?php echo esc_attr($atts['ubicacion']); ?>" data-servicios="<?php echo esc_attr($atts['servicios']); ?>"></div>
<div id="twf-agencias-results" class="twf-agencias-results"></div>