<?php
// Si no es llamado por WordPress, salir
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Eliminar Custom Post Type y sus publicaciones
$agencias = get_posts(array(
    'post_type'      => 'agencia',
    'numberposts'    => -1,
    'post_status'    => 'any'
));

foreach ($agencias as $agencia) {
    wp_delete_post($agencia->ID, true); // true para forzar eliminación (no enviar a papelera)
}

// Eliminar la taxonomía (WordPress limpiará los términos automáticamente)
// No necesitamos hacer nada adicional para la taxonomía

// Eliminar opciones del plugin
delete_option('twf_agencias_google_maps_api_key');
delete_option('twf_agencias_pin_icon');
delete_option('twf_agencias_labels');
delete_option('twf_agencias_search_button_icon');
delete_option('twf_agencias_services');
delete_option('twf_agencias_tooltip_fields');
delete_option('twf_agencias_custom_css');

// Limpiar cache y reglas de reescritura
flush_rewrite_rules();