<?php
/**
 * Clase que maneja la funcionalidad administrativa del plugin
 */

class TWF_Agencias_Admin {

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
     * Registrar los estilos para el área de administración
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, TWF_AGENCIAS_PLUGIN_URL . 'admin/css/twf-agencias-admin.css', array(), $this->version, 'all');
    }

    /**
     * Registrar los scripts para el área de administración
     */
    public function enqueue_scripts() {
        // Cargar siempre media para todas las páginas de administración relacionadas con el plugin
        wp_enqueue_media();
        
        wp_enqueue_script($this->plugin_name, TWF_AGENCIAS_PLUGIN_URL . 'admin/js/twf-agencias-admin.js', array('jquery', 'wp-color-picker'), $this->version, false);
        
        // También pasamos algunas variables al script
        wp_localize_script(
            $this->plugin_name,
            'twf_agencias_vars',
            array(
                'default_icon_url' => TWF_AGENCIAS_PLUGIN_URL . 'admin/images/search_icon.svg',
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('twf_agencias_nonce'),
            )
        );
    }

    /**
     * Añade un elemento al menú de administración
     */
    public function add_admin_menu() {
        // Icono personalizado
        $icon_url = TWF_AGENCIAS_PLUGIN_URL . 'admin/images/twf-agencias-icon.png';
        if (!file_exists(TWF_AGENCIAS_PLUGIN_DIR . 'admin/images/twf-agencias-icon.png')) {
            $icon_url = 'dashicons-admin-generic';
        }

        // Añadir menú principal justo después de Ajustes (posición 80)
        add_menu_page(
            'Configuración de Mapa Multi Agencias',
            'Mapa Multi Agencias',
            'manage_options',
            'twf-agencias-settings',
            array($this, 'display_plugin_admin_page'),
            $icon_url,
            81 // Justo después de Ajustes/Configuración (80)
        );
    }

    /**
     * Renderiza la página de administración del plugin
     */
    public function display_plugin_admin_page() {
        // Cargar la vista principal de la página de administración
        require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/partials/twf-agencias-admin-display.php';
    }

    /**
     * Registra las configuraciones del plugin
     */
    public function register_settings() {
        // Pestaña: Configuración General
        register_setting(
            'twf_agencias_general_settings',
            'twf_agencias_google_maps_api_key',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            )
        );
        
        register_setting(
            'twf_agencias_general_settings',
            'twf_agencias_pin_icon',
            array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 0,
            )
        );
        
        register_setting(
            'twf_agencias_general_settings',
            'twf_agencias_default_zoom',
            array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 12,
            )
        );
        
        register_setting(
            'twf_agencias_general_settings',
            'twf_agencias_labels',
            array(
                'type'              => 'array',
                'sanitize_callback' => array($this, 'sanitize_labels'),
                'default'           => array(
                    'search_input'      => 'Buscar tu oficina más cercana',
                    'search_placeholder' => 'Ingresa el nombre de la oficina',
                    'city_select'       => 'Selecciona ciudad',
                    'city_placeholder'  => 'Seleccionar ciudad',
                    'district_select'   => 'Selecciona tu distrito',
                    'district_placeholder' => 'Seleccionar distrito',
                    'search_button'     => 'Buscar',
                ),
            )
        );
        
        register_setting(
            'twf_agencias_general_settings',
            'twf_agencias_search_button_icon',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            )
        );
        
        // Pestaña: Servicios
        register_setting(
            'twf_agencias_services_settings',
            'twf_agencias_services',
            array(
                'type'              => 'array',
                'sanitize_callback' => array($this, 'sanitize_services'),
                'default'           => array(),
            )
        );
        
        // Pestaña: Tooltip
        register_setting(
            'twf_agencias_tooltip_settings',
            'twf_agencias_tooltip_fields',
            array(
                'type'              => 'array',
                'sanitize_callback' => array($this, 'sanitize_tooltip_fields'),
                'default'           => array(
                    'title'     => true,
                    'address'   => true,
                    'phone'     => true,
                    'mobile'    => true,
                    'email'     => false,
                    'schedule'  => true,
                    'services'  => true,
                ),
            )
        );
        
        // Pestaña: CSS personalizado
        register_setting(
            'twf_agencias_css_settings',
            'twf_agencias_custom_css',
            array(
                'type'              => 'string',
                'sanitize_callback' => array($this, 'sanitize_custom_css'),
                'default'           => '',
            )
        );
    }

    /**
     * Sanitiza la configuración de etiquetas
     */
    public function sanitize_labels($input) {
        $sanitized_input = array();
        
        $fields = array(
            'search_input',
            'search_placeholder',
            'city_select',
            'city_placeholder',
            'district_select',
            'district_placeholder',
            'search_button',
        );
        
        foreach ($fields as $field) {
            $sanitized_input[$field] = isset($input[$field]) ? sanitize_text_field($input[$field]) : '';
        }
        
        return $sanitized_input;
    }

    /**
     * Sanitiza la configuración de servicios
     */
    public function sanitize_services($input) {
        $sanitized_input = array();
        
        if (is_array($input)) {
            foreach ($input as $key => $service) {
                if (isset($service['label'])) {
                    $id = sanitize_key($service['label']);
                    $sanitized_input[$id] = array(
                        'label' => sanitize_text_field($service['label']),
                        'value' => $id,
                    );
                }
            }
        }
        
        return $sanitized_input;
    }

    /**
     * Sanitiza la configuración de campos del tooltip
     */
    public function sanitize_tooltip_fields($input) {
        $sanitized_input = array();
        
        $fields = array(
            'title',
            'address',
            'phone',
            'mobile',
            'email',
            'schedule',
            'services',
        );
        
        foreach ($fields as $field) {
            $sanitized_input[$field] = isset($input[$field]) ? (bool) $input[$field] : false;
        }
        
        return $sanitized_input;
    }

    /**
     * Sanitiza el CSS personalizado
     */
    public function sanitize_custom_css($input) {
        // Permitir CSS pero eliminar scripts potencialmente dañinos
        $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);
        return $input;
    }
}