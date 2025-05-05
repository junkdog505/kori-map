<?php
/**
 * Se ejecuta durante la activación del plugin
 */

class TWF_Agencias_Activator {

    /**
     * Se ejecuta durante la activación del plugin
     */
    public static function activate() {
        // Crear las opciones predeterminadas si no existen
        if (!get_option('twf_agencias_google_maps_api_key')) {
            add_option('twf_agencias_google_maps_api_key', '');
        }
        
        if (!get_option('twf_agencias_pin_icon')) {
            add_option('twf_agencias_pin_icon', 0);
        }
        
        if (!get_option('twf_agencias_labels')) {
            add_option('twf_agencias_labels', array(
                'search_input'       => 'Buscar oficina por nombre o dirección',
                'search_placeholder' => 'Ingresa el nombre o dirección de la oficina',
                'city_select'        => 'Selecciona ciudad',
                'city_placeholder'   => 'Seleccionar ciudad',
                'district_select'    => 'Selecciona tu distrito',
                'district_placeholder' => 'Seleccionar distrito',
                'search_button'      => 'Buscar',
            ));
        }
        
        if (!get_option('twf_agencias_search_button_icon')) {
            add_option('twf_agencias_search_button_icon', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>');
        }
        
        if (!get_option('twf_agencias_services')) {
            add_option('twf_agencias_services', array());
        }
        
        if (!get_option('twf_agencias_tooltip_fields')) {
            add_option('twf_agencias_tooltip_fields', array(
                'title'     => true,
                'address'   => true,
                'phone'     => true,
                'mobile'    => true,
                'email'     => false,
                'schedule'  => true,
                'services'  => true,
            ));
        }
        
        if (!get_option('twf_agencias_custom_css')) {
            add_option('twf_agencias_custom_css', '');
        }
        
        // Forzar la recreación de las reglas de reescritura
        flush_rewrite_rules();
    }
}