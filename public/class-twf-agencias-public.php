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
        
        // Obtener servicios
        $all_services = get_option('twf_agencias_services', array());
        
        // Pasar variables al script
        $api_key = get_option('twf_agencias_google_maps_api_key', '');
        $labels = get_option('twf_agencias_labels', array());
        
        // Valores por defecto para las etiquetas
        $default_labels = array(
            'search_input'       => 'Buscar oficina por nombre o dirección',
            'search_placeholder' => 'Ingresa el nombre o dirección de la oficina',
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
                'plugin_url' => TWF_AGENCIAS_PLUGIN_URL,
                'services' => $all_services,
                'location_terms' => $this->get_location_terms() 
            )
        );
    }
    
    /**
     * Registrar shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('twf_agencias_map', array($this, 'render_map_shortcode'));
        add_shortcode('twf_agencias_filters', array($this, 'render_filters_shortcode'));
        add_shortcode('twf_agencias_cards', array($this, 'render_cards_shortcode'));
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
            ),
            $atts,
            'twf_agencias_map'
        );
        
        // Cargar los scripts necesarios
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        // Iniciar el buffer de salida
        ob_start();
        
        // Incluir la vista del mapa
        include TWF_AGENCIAS_PLUGIN_DIR . 'public/partials/twf-agencias-map-display.php';
        
        return ob_get_clean();
    }

    /**
     * Renderiza el shortcode de tarjetas de agencias
     */
    public function render_cards_shortcode($atts) {
        // Extraer atributos del shortcode
        $atts = shortcode_atts(
            array(
                'items_per_page' => '4',
                'ubicacion'      => '',
                'servicios'      => '',
            ),
            $atts,
            'twf_agencias_cards'
        );
        
        // Cargar los scripts necesarios
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        // Iniciar el buffer de salida
        ob_start();
        
        // Incluir la vista de las tarjetas
        include TWF_AGENCIAS_PLUGIN_DIR . 'public/partials/twf-agencias-cards-display.php';
        
        return ob_get_clean();
    }

    /**
     * Renderiza el shortcode de los filtros
     */
    public function render_filters_shortcode($atts) {
        // Extraer atributos del shortcode
        $atts = shortcode_atts(
            array(
                'ubicacion'      => '',
                'servicios'      => '',
            ),
            $atts,
            'twf_agencias_filters'
        );
        
        // Obtener las etiquetas configuradas
        $labels = get_option('twf_agencias_labels', array());
        
        // Valores por defecto para las etiquetas
        $default_labels = array(
            'search_input'       => 'Buscar oficina por nombre o dirección',
            'search_placeholder' => 'Ingresa el nombre o dirección de la oficina',
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
        
        // Incluir la vista de los filtros
        include TWF_AGENCIAS_PLUGIN_DIR . 'public/partials/twf-agencias-filters-display.php';
        
        return ob_get_clean();
    }

    /**
     * Registrar endpoints AJAX
     */
    public function register_ajax_handlers() {
        add_action('wp_ajax_twf_agencias_get_districts', array($this, 'get_districts_callback'));
        add_action('wp_ajax_nopriv_twf_agencias_get_districts', array($this, 'get_districts_callback'));
        
        add_action('wp_ajax_twf_agencias_get_agencies', array($this, 'get_agencies_callback'));
        add_action('wp_ajax_nopriv_twf_agencias_get_agencies', array($this, 'get_agencies_callback'));
        
        add_action('wp_ajax_twf_agencias_search_agencies', array($this, 'search_agencies_callback'));
        add_action('wp_ajax_nopriv_twf_agencias_search_agencies', array($this, 'search_agencies_callback'));
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
    
    /**
     * Callback para obtener todas las agencias
     */
    public function get_agencies_callback() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'twf_agencias_nonce')) {
            wp_send_json_error('Acceso no autorizado.');
        }
        
        // Obtener filtros
        $filters = isset($_POST['filters']) ? $_POST['filters'] : array();
        
        // Construir argumentos para la consulta
        $args = array(
            'post_type'      => 'agencia',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        
        // Filtrar por ubicación
        if (!empty($filters['ubicacion'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'ubicacion',
                    'field'    => 'slug',
                    'terms'    => $filters['ubicacion'],
                ),
            );
        }
        
        // Ejecutar la consulta
        $query = new WP_Query($args);
        
        // Obtener todos los términos de ubicación para poder acceder a los padres
        $all_location_terms = $this->get_location_terms();
        
        // Preparar los datos para la respuesta
        $agencies = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                // Datos básicos
                $agency_id = get_the_ID();
                $agency_title = get_the_title();
                
                // Imagen destacada
                $featured_image = '';
                if (has_post_thumbnail($agency_id)) {
                    $featured_image = get_the_post_thumbnail_url($agency_id, 'large');
                }
                
                // Términos de taxonomía
                $terms = array();
                $taxonomies = array('ubicacion');
                
                foreach ($taxonomies as $taxonomy) {
                    $post_terms = get_the_terms($agency_id, $taxonomy);
                    if (!empty($post_terms) && !is_wp_error($post_terms)) {
                        $terms[$taxonomy] = array();
                        foreach ($post_terms as $term) {
                            // Obtener información del término padre si existe
                            $parent_term = null;
                            if ($term->parent > 0 && isset($all_location_terms[$term->parent])) {
                                $parent_term = $all_location_terms[$term->parent];
                            }
                            
                            $terms[$taxonomy][] = array(
                                'id' => $term->term_id,
                                'name' => $term->name,
                                'slug' => $term->slug,
                                'parent' => $term->parent,
                                'parent_name' => $parent_term ? $parent_term['name'] : '',
                                'parent_slug' => $parent_term ? $parent_term['slug'] : ''
                            );
                        }
                    }
                }
                
                // Metadatos
                $direccion = get_post_meta($agency_id, '_twf_agencias_direccion', true);
                $telefono = get_post_meta($agency_id, '_twf_agencias_telefono', true);
                $celular = get_post_meta($agency_id, '_twf_agencias_celular', true);
                $anexo = get_post_meta($agency_id, '_twf_agencias_anexo', true);
                $email = get_post_meta($agency_id, '_twf_agencias_email', true);
                $services = get_post_meta($agency_id, '_twf_agencias_services', true);
                $schedule = get_post_meta($agency_id, '_twf_agencias_schedule', true);
                $latitud = get_post_meta($agency_id, '_twf_agencias_latitud', true);
                $longitud = get_post_meta($agency_id, '_twf_agencias_longitud', true);
                
                // Icono personalizado
                $custom_icon_id = get_post_meta($agency_id, '_twf_agencias_custom_icon', true);
                $custom_icon_url = '';
                
                if (!empty($custom_icon_id)) {
                    $custom_icon_url = wp_get_attachment_image_url($custom_icon_id, 'full');
                } else {
                    $global_icon_id = get_option('twf_agencias_pin_icon', 0);
                    if (!empty($global_icon_id)) {
                        $custom_icon_url = wp_get_attachment_image_url($global_icon_id, 'full');
                    }
                }
                
                // Generar contenido del tooltip
                $tooltip_content = $this->generate_tooltip_content($agency_id, $agency_title, $direccion, $telefono, $celular, $anexo, $email, $services, $schedule);
                
                // Añadir a la lista de agencias
                $agencies[] = array(
                    'id'         => $agency_id,
                    'title'      => $agency_title,
                    'featured_image' => $featured_image,
                    'terms'      => $terms,
                    'direccion'  => $direccion,
                    'latitud'    => $latitud,
                    'longitud'   => $longitud,
                    'icon'       => $custom_icon_url,
                    'infoWindow' => $tooltip_content,
                    'meta_datos' => array(
                        'latitud'    => $latitud,
                        'longitud'   => $longitud,
                        'direccion'  => $direccion,
                        'telefono'   => $telefono,
                        'celular'    => $celular,
                        'anexo'      => $anexo,
                        'email'      => $email,
                        'services'   => $services,
                        'schedule'   => $schedule,
                        'custom_icon' => $custom_icon_url
                    )
                );
            }
            
            wp_reset_postdata();
        }
        
        // Enviar respuesta JSON
        wp_send_json_success($agencies);
    }
     
    /**
     * Obtiene todos los términos de la taxonomía ubicación
     */
    private function get_location_terms() {
        $terms = get_terms(array(
            'taxonomy' => 'ubicacion',
            'hide_empty' => false
        ));
        
        $terms_array = array();
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $terms_array[$term->term_id] = array(
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'parent' => $term->parent
                );
            }
        }
        
        return $terms_array;
    }

    /**
     * Callback para buscar agencias
     */
    public function search_agencies_callback() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'twf_agencias_nonce')) {
            wp_send_json_error('Acceso no autorizado.');
        }
        
        // Obtener filtros
        $filters = isset($_POST['filters']) ? $_POST['filters'] : array();
        
        // Construir argumentos para la consulta
        $args = array(
            'post_type'      => 'agencia',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        
        // Búsqueda por nombre o dirección
        if (!empty($filters['search'])) {
            $search_term = sanitize_text_field($filters['search']);
            
            // Primero buscar por título (nombre)
            $args['s'] = $search_term;
            
            // Después buscaremos por dirección en el procesamiento
        }
        
        // Filtrar por ciudad
        if (!empty($filters['city'])) {
            if (empty($args['tax_query'])) {
                $args['tax_query'] = array('relation' => 'AND');
            }
            
            $args['tax_query'][] = array(
                'taxonomy' => 'ubicacion',
                'field'    => 'slug',
                'terms'    => $filters['city'],
            );
        }
        
        // Filtrar por distrito
        if (!empty($filters['district'])) {
            if (empty($args['tax_query'])) {
                $args['tax_query'] = array('relation' => 'AND');
            }
            
            $args['tax_query'][] = array(
                'taxonomy' => 'ubicacion',
                'field'    => 'slug',
                'terms'    => $filters['district'],
            );
        }
        
        // Ejecutar la consulta
        $query = new WP_Query($args);
        
        // Preparar los datos para la respuesta
        $agencies = array();
        $search_by_address = !empty($filters['search']);
        $search_term_lower = !empty($filters['search']) ? strtolower($filters['search']) : '';
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                $agency_id = get_the_ID();
                $agency_title = get_the_title();
                
                // Metadatos
                $direccion = get_post_meta($agency_id, '_twf_agencias_direccion', true);
                $telefono = get_post_meta($agency_id, '_twf_agencias_telefono', true);
                $celular = get_post_meta($agency_id, '_twf_agencias_celular', true);
                $anexo = get_post_meta($agency_id, '_twf_agencias_anexo', true);
                $email = get_post_meta($agency_id, '_twf_agencias_email', true);
                $services = get_post_meta($agency_id, '_twf_agencias_services', true);
                $schedule = get_post_meta($agency_id, '_twf_agencias_schedule', true);
                $latitud = get_post_meta($agency_id, '_twf_agencias_latitud', true);
                $longitud = get_post_meta($agency_id, '_twf_agencias_longitud', true);
                
                // Si estamos buscando por dirección también y no hay coincidencia en el título
                if ($search_by_address && empty($args['s']) && !empty($search_term_lower)) {
                    // Si la dirección no contiene el término de búsqueda, saltar esta agencia
                    if (strpos(strtolower($direccion), $search_term_lower) === false) {
                        continue;
                    }
                }
                
                // Icono personalizado
                $custom_icon_id = get_post_meta($agency_id, '_twf_agencias_custom_icon', true);
                $custom_icon_url = '';
                
                if (!empty($custom_icon_id)) {
                    $custom_icon_url = wp_get_attachment_image_url($custom_icon_id, 'full');
                } else {
                    $global_icon_id = get_option('twf_agencias_pin_icon', 0);
                    if (!empty($global_icon_id)) {
                        $custom_icon_url = wp_get_attachment_image_url($global_icon_id, 'full');
                    }
                }
                
                // Generar contenido del tooltip
                $tooltip_content = $this->generate_tooltip_content($agency_id, $agency_title, $direccion, $telefono, $celular, $anexo, $email, $services, $schedule);
                
                // Añadir a la lista de agencias
                $agencies[] = array(
                    'id'         => $agency_id,
                    'title'      => $agency_title,
                    'direccion'  => $direccion,
                    'latitud'    => $latitud,
                    'longitud'   => $longitud,
                    'icon'       => $custom_icon_url,
                    'infoWindow' => $tooltip_content,
                    'meta_datos' => array(
                        'latitud'  => $latitud,
                        'longitud' => $longitud,
                        'direccion' => $direccion,
                        'custom_icon' => $custom_icon_url
                    )
                );
            }
            
            wp_reset_postdata();
        }
        
        // Enviar respuesta JSON
        wp_send_json_success($agencies);
    }
    
    /**
     * Genera el contenido HTML del tooltip
     */
    private function generate_tooltip_content($agency_id, $title, $direccion, $telefono, $celular, $anexo, $email, $services, $schedule) {
        // Obtener configuración de campos a mostrar
        $tooltip_fields = get_option('twf_agencias_tooltip_fields', array());
        
        // Valores por defecto
        $default_fields = array(
            'title'     => true,
            'address'   => true,
            'phone'     => true,
            'mobile'    => true,
            'email'     => false,
            'schedule'  => true,
            'services'  => true,
        );
        
        // Combinar con valores predeterminados
        $tooltip_fields = wp_parse_args($tooltip_fields, $default_fields);
        
        // Iniciar contenido
        $content = '<div class="twf-agencias-tooltip">';
        
        // Título
        if ($tooltip_fields['title'] && !empty($title)) {
            $content .= '<h3 class="twf-agencias-tooltip-title">' . esc_html($title) . '</h3>';
        }
        
        // Dirección
        if ($tooltip_fields['address'] && !empty($direccion)) {
            $content .= '<div class="twf-agencias-tooltip-address">' . esc_html($direccion) . '</div>';
        }
        
        // Información de contacto
        $has_contact = ($tooltip_fields['phone'] && !empty($telefono)) || 
                    ($tooltip_fields['mobile'] && !empty($celular)) || 
                    ($tooltip_fields['email'] && !empty($email));
        
        if ($has_contact) {
            $content .= '<div class="twf-agencias-tooltip-contact">';
            
            // Teléfono
            if ($tooltip_fields['phone'] && !empty($telefono)) {
                $content .= '<div><strong>Teléfono:</strong> ' . esc_html($telefono);
                
                // Añadir anexo si existe
                if (!empty($anexo)) {
                    $content .= ' Anexo: ' . esc_html($anexo);
                }
                
                $content .= '</div>';
            }
            
            // Celular
            if ($tooltip_fields['mobile'] && !empty($celular)) {
                $content .= '<div><strong>Celular:</strong> ' . esc_html($celular) . '</div>';
            }
            
            // Email
            if ($tooltip_fields['email'] && !empty($email)) {
                $content .= '<div><strong>Email:</strong> <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></div>';
            }
            
            $content .= '</div>';
        }
        
        // Horarios
        if ($tooltip_fields['schedule'] && !empty($schedule) && is_array($schedule)) {
            $content .= '<div class="twf-agencias-tooltip-schedule">';
            $content .= '<strong>Horarios:</strong>';
            $content .= '<ul>';
            
            $days_map = array(
                'monday'    => 'Lunes',
                'tuesday'   => 'Martes',
                'wednesday' => 'Miércoles',
                'thursday'  => 'Jueves',
                'friday'    => 'Viernes',
                'saturday'  => 'Sábado',
                'sunday'    => 'Domingo',
            );
            
            foreach ($schedule as $day_id => $day_data) {
                if (isset($day_data['active']) && $day_data['active']) {
                    $day_name = isset($days_map[$day_id]) ? $days_map[$day_id] : ucfirst($day_id);
                    $open = isset($day_data['open']) ? $day_data['open'] : '09:00';
                    $close = isset($day_data['close']) ? $day_data['close'] : '18:00';
                    
                    $content .= '<li>' . esc_html($day_name) . ': ' . esc_html($open) . ' - ' . esc_html($close) . '</li>';
                }
            }
            
            $content .= '</ul>';
            $content .= '</div>';
        }
        
        // Servicios
        if ($tooltip_fields['services'] && !empty($services) && is_array($services)) {
            $content .= '<div class="twf-agencias-tooltip-services">';
            $content .= '<strong>Servicios:</strong>';
            $content .= '<ul>';
            
            // Obtener todos los servicios disponibles desde la configuración
            $all_services = get_option('twf_agencias_services', array());
            
            foreach ($services as $service_id) {
                if (isset($all_services[$service_id]['label'])) {
                    $service_name = $all_services[$service_id]['label'];
                    $content .= '<li>' . esc_html($service_name) . '</li>';
                }
            }
            
            $content .= '</ul>';
            $content .= '</div>';
        }
        
        $content .= '</div>';
        
        return $content;
    }
}