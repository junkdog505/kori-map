<?php
/**
 * Clase que maneja la funcionalidad de la API REST
 */

class TWF_Agencias_REST_API {

    // El identificador único del plugin
    private $plugin_name;

    // La versión del plugin
    private $version;

    /**
     * Inicializa la clase
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Registra los endpoints de la API REST
     */
    public function register_rest_fields() {
        // Añadir campos al endpoint del CPT agencia
        register_rest_field('agencia', 'meta_datos', array(
            'get_callback' => array($this, 'get_agencia_meta_datos'),
            'schema' => null,
        ));
    }
        
    /**
     * Callback para obtener los metadatos de una agencia
     */
    public function get_agencia_meta_datos($post, $field_name, $request) {
        $post_id = $post['id'];
        
        // Obtener metadatos
        $meta_datos = array(
            'direccion' => get_post_meta($post_id, '_twf_agencias_direccion', true),
            'telefono' => get_post_meta($post_id, '_twf_agencias_telefono', true),
            'celular' => get_post_meta($post_id, '_twf_agencias_celular', true),
            'anexo' => get_post_meta($post_id, '_twf_agencias_anexo', true),
            'email' => get_post_meta($post_id, '_twf_agencias_email', true),
            'services' => get_post_meta($post_id, '_twf_agencias_services', true),
            'schedule' => get_post_meta($post_id, '_twf_agencias_schedule', true),
            'custom_icon' => $this->get_custom_icon_url($post_id),
            'latitud' => get_post_meta($post_id, '_twf_agencias_latitud', true),
            'longitud' => get_post_meta($post_id, '_twf_agencias_longitud', true),
        );
        
        return $meta_datos;
    }
    /**
     * Obtiene la URL del icono personalizado de una agencia
     */
    private function get_custom_icon_url($post_id) {
        $custom_icon_id = get_post_meta($post_id, '_twf_agencias_custom_icon', true);
        $custom_icon_url = '';
        
        if (!empty($custom_icon_id)) {
            $custom_icon_url = wp_get_attachment_image_url($custom_icon_id, 'full');
        } else {
            $global_icon_id = get_option('twf_agencias_pin_icon', 0);
            if (!empty($global_icon_id)) {
                $custom_icon_url = wp_get_attachment_image_url($global_icon_id, 'full');
            }
        }
        
        return $custom_icon_url;
    }
}