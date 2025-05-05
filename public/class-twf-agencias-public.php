<?php
/**
 * La funcionalidad del área pública del plugin
 */

class TWF_Agencias_Public {

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
     * Registrar los estilos para el área pública
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, TWF_AGENCIAS_PLUGIN_URL . 'public/css/twf-agencias-public.css', array(), $this->version, 'all');
        
        // Agregar CSS personalizado
        $custom_css = get_option('twf_agencias_custom_css', '');
        if (!empty($custom_css)) {
            wp_add_inline_style($this->plugin_name, $custom_css);
        }
    }

    /**
     * Registrar los scripts para el área pública
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, TWF_AGENCIAS_PLUGIN_URL . 'public/js/twf-agencias-public.js', array('jquery'), $this->version, false);
        
        // Pasar variables al script
        $api_key = get_option('twf_agencias_google_maps_api_key', '');
        $labels = get_option('twf_agencias_labels', array());
        
        // Valores por defecto para las etiquetas
        $default_labels = array(
            'search_input'       => 'Buscar tu oficina más cercana',
            'search_placeholder' => 'Ingresa el nombre de la oficina',
            'city_select'        => 'Selecciona ciudad',
            'city_placeholder'   => 'Seleccionar ciudad',
            'district_select'    => 'Selecciona tu distrito',
            'district_placeholder' => 'Seleccionar distrito',
            'search_button'      => 'Buscar',
        );
        
        // Combinar con valores predeterminados
        $labels = wp_parse_args($labels, $default_labels);
        
        wp_localize_script(
            $this->plugin_name,
            'twf_agencias_vars',
            array(
                'api_key' => $api_key,
                'labels'  => $labels,
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('twf_agencias_nonce'),
            )
        );
    }

    /**
     * Registrar shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('twf_agencias_map', array($this, 'render_map_shortcode'));
    }

    /**
     * Renderiza el shortcode del mapa
     */
    public function render_map_shortcode($atts) {
        // Extraer atributos del shortcode
        $atts = shortcode_atts(
            array(
                'height'         => '400',
                'zoom'           => '12',
                'ubicacion'      => '',
                'servicios'      => '',
                'mostrar_filtros' => 'true',
            ),
            $atts,
            'twf_agencias_map'
        );
        
        // Obtener las etiquetas configuradas
        $labels = get_option('twf_agencias_labels', array());
        
        // Valores por defecto para las etiquetas
        $default_labels = array(
            'search_input'       => 'Buscar tu oficina más cercana',
            'search_placeholder' => 'Ingresa el nombre de la oficina',
            'city_select'        => 'Selecciona ciudad',
            'city_placeholder'   => 'Seleccionar ciudad',
            'district_select'    => 'Selecciona tu distrito',
            'district_placeholder' => 'Seleccionar distrito',
            'search_button'      => 'Buscar',
        );
        
        // Combinar con valores predeterminados
        $labels = wp_parse_args($labels, $default_labels);
        
        // Obtener el ícono del botón de búsqueda
        $search_button_icon = get_option('twf_agencias_search_button_icon', '');
        
        // Comprobar si mostrar filtros
        $mostrar_filtros = filter_var($atts['mostrar_filtros'], FILTER_VALIDATE_BOOLEAN);
        
        // Obtener todas las ubicaciones (términos de taxonomía)
        $terms = get_terms(array(
            'taxonomy'   => 'ubicacion',
            'hide_empty' => false,
            'parent'     => 0,  // Solo ciudades (términos padre)
        ));
        
        // Cargar los scripts necesarios
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        // Iniciar el buffer de salida
        ob_start();
        
        // Incluir la vista del mapa
        include TWF_AGENCIAS_PLUGIN_DIR . 'public/partials/twf-agencias-public-display.php';
        
        return ob_get_clean();
    }

    /**
     * Registrar endpoints AJAX
     */
    public function register_ajax_handlers() {
        add_action('wp_ajax_twf_agencias_get_districts', array($this, 'get_districts_callback'));
        add_action('wp_ajax_nopriv_twf_agencias_get_districts', array($this, 'get_districts_callback'));
    }

    /**
     * Callback para obtener los distritos de una ciudad
     */
    public function get_districts_callback() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'twf_agencias_nonce')) {
            wp_send_json_error('Acceso no autorizado.');
        }
        
        // Obtener el slug de la ciudad
        $city_slug = isset($_POST['city_id']) ? sanitize_text_field($_POST['city_id']) : '';
        
        if (empty($city_slug)) {
            wp_send_json_error('Parámetro de ciudad no válido.');
        }
        
        // Obtener el término de la taxonomía
        $city_term = get_term_by('slug', $city_slug, 'ubicacion');
        
        if (!$city_term) {
            wp_send_json_error('Ciudad no encontrada.');
        }
        
        // Obtener los distritos (términos hijos)
        $districts = get_terms(array(
            'taxonomy'   => 'ubicacion',
            'hide_empty' => false,
            'parent'     => $city_term->term_id,
        ));
        
        $districts_data = array();
        
        if (!is_wp_error($districts) && !empty($districts)) {
            foreach ($districts as $district) {
                $districts_data[] = array(
                    'id'   => $district->term_id,
                    'slug' => $district->slug,
                    'name' => $district->name,
                );
            }
        }
        
        // Enviar respuesta JSON
        wp_send_json_success($districts_data);
    }
}