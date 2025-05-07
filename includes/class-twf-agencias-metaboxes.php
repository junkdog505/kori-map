<?php
/**
 * Clase que maneja los metaboxes para el CPT Agencias
 */

class TWF_Agencias_Metaboxes {

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
     * Registra los metaboxes para el CPT Agencias
     */
    public function register_meta_boxes() {
        // Metabox para datos de contacto
        add_meta_box(
            'twf_agencias_contact_info',
            'Información de Contacto',
            array($this, 'render_contact_info_metabox'),
            'agencia',
            'normal',
            'high'
        );
        
        // Metabox para servicios
        add_meta_box(
            'twf_agencias_services',
            'Servicios',
            array($this, 'render_services_metabox'),
            'agencia',
            'normal',
            'default'
        );
        
        // Metabox para horarios
        add_meta_box(
            'twf_agencias_schedule',
            'Horarios',
            array($this, 'render_schedule_metabox'),
            'agencia',
            'normal',
            'default'
        );
    }

    /**
     * Renderiza el metabox para información de contacto
     */
    public function render_contact_info_metabox($post) {
        // Añadir nonce para verificación
        wp_nonce_field('twf_agencias_contact_info_nonce', 'twf_agencias_contact_info_nonce');
        
        // Obtener valores actuales
        $celular = get_post_meta($post->ID, '_twf_agencias_celular', true);
        $telefono = get_post_meta($post->ID, '_twf_agencias_telefono', true);
        $anexo = get_post_meta($post->ID, '_twf_agencias_anexo', true);
        $email = get_post_meta($post->ID, '_twf_agencias_email', true);
        $latitud = get_post_meta($post->ID, '_twf_agencias_latitud', true);
        $longitud = get_post_meta($post->ID, '_twf_agencias_longitud', true);
        $direccion_mostrar = get_post_meta($post->ID, '_twf_agencias_direccion_mostrar', true);
        
        // Obtener la API key
        $api_key = get_option('twf_agencias_google_maps_api_key', '');
        
        // Campos del formulario con nuevo diseño
        ?>
        <div class="twf-agencias-contact-grid">
            <div class="twf-agencias-contact-row">
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_celular">Celular:</label>
                    <input type="text" id="twf_agencias_celular" name="twf_agencias_celular" 
                        value="<?php echo esc_attr($celular); ?>" class="widefat">
                    <p class="description">Número de celular de contacto de la agencia. Formato recomendado: +51 999 999 999</p>
                </div>
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_telefono">Teléfono:</label>
                    <input type="text" id="twf_agencias_telefono" name="twf_agencias_telefono" 
                        value="<?php echo esc_attr($telefono); ?>" class="widefat">
                    <p class="description">Número de teléfono fijo de la agencia. Formato recomendado: (01) 999-9999</p>
                </div>
            </div>
            
            <div class="twf-agencias-contact-row">
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_anexo">Anexo:</label>
                    <input type="text" id="twf_agencias_anexo" name="twf_agencias_anexo" 
                        value="<?php echo esc_attr($anexo); ?>" class="widefat">
                    <p class="description">Número de anexo telefónico, si aplica. Ejemplo: 123</p>
                </div>
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_email">Correo Electrónico:</label>
                    <input type="email" id="twf_agencias_email" name="twf_agencias_email" 
                        value="<?php echo esc_attr($email); ?>" class="widefat">
                    <p class="description">Correo electrónico de contacto de la agencia. Ejemplo: contacto@agencia.com</p>
                </div>
            </div>
        </div>
        
        <!-- Campo de dirección a mostrar - Justo antes de "Ubicación en el Mapa" -->
        <div style="margin-top: 15px; margin-bottom: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
            <label for="twf_agencias_direccion_mostrar" style="display: block; margin-bottom: 8px; font-weight: 600;">Dirección a mostrar en el Pin del mapa:</label>
            <input type="text" id="twf_agencias_direccion_mostrar" name="twf_agencias_direccion_mostrar" 
                value="<?php echo esc_attr($direccion_mostrar); ?>" class="widefat">
            <p class="description">Ingrese la dirección exacta que desea mostrar en el tooltip del pin del mapa y en las tarjetas. Esta dirección no afecta la ubicación del pin, solo es el texto que se mostrará al cliente.</p>
        </div>
        
        <div class="twf-agencias-location-fields" style="margin-top: 15px;">
            <h3 style="margin-top: 0;">Ubicación en el Mapa</h3>
            <p class="description">Selecciona la ubicación exacta en el mapa.</p>
            
            <div class="twf-agencias-search-map">
                <input type="text" id="twf_agencias_location_search" 
                    placeholder="Buscar dirección..." class="widefat" 
                    style="margin-bottom: 10px;">
                <button type="button" id="twf_agencias_search_location" class="button">
                    Buscar
                </button>
            </div>
            
            <div id="twf_agencias_location_map" style="height: 300px; margin-top: 10px; border: 1px solid #ddd;"></div>
            
            <div class="twf-agencias-location-coords" style="display: flex; gap: 10px; margin-top: 10px;">
                <div style="flex: 1;">
                    <label for="twf_agencias_latitud">Latitud:</label>
                    <input type="text" id="twf_agencias_latitud" name="twf_agencias_latitud" 
                        value="<?php echo esc_attr($latitud); ?>" class="widefat" readonly>
                </div>
                <div style="flex: 1;">
                    <label for="twf_agencias_longitud">Longitud:</label>
                    <input type="text" id="twf_agencias_longitud" name="twf_agencias_longitud" 
                        value="<?php echo esc_attr($longitud); ?>" class="widefat" readonly>
                </div>
            </div>
            <div style="flex: 1; margin-top: 10px;">
                <label for="twf_agencias_direccion">Dirección (Automática):</label>
                <input type="text" id="twf_agencias_direccion" name="twf_agencias_direccion" 
                    class="widefat" readonly>
                <p class="description">Esta dirección se obtiene automáticamente de las coordenadas seleccionadas en el mapa.</p>
            </div>
        </div>
        
        <?php if (!empty($api_key)) : ?>
        <script>
            jQuery(document).ready(function($) {
                var map, marker;
                var mapDiv = document.getElementById('twf_agencias_location_map');
                var latInput = $('#twf_agencias_latitud');
                var lngInput = $('#twf_agencias_longitud');
                var addressInput = $('#twf_agencias_direccion');
                var searchInput = $('#twf_agencias_location_search');
                
                // Inicializar el mapa
                function initMap() {
                    var defaultLat = latInput.val() ? parseFloat(latInput.val()) : -12.0464;
                    var defaultLng = lngInput.val() ? parseFloat(lngInput.val()) : -77.0428;
                    var defaultLatLng = {lat: defaultLat, lng: defaultLng};
                    
                    map = new google.maps.Map(mapDiv, {
                        center: defaultLatLng,
                        zoom: 14,
                        mapTypeControl: true,
                        streetViewControl: false
                    });
                    
                    // Crear marcador
                    marker = new google.maps.Marker({
                        position: defaultLatLng,
                        map: map,
                        draggable: true
                    });
                    
                    // Evento de clic en el mapa
                    google.maps.event.addListener(map, 'click', function(event) {
                        updateMarkerPosition(event.latLng);
                    });
                    
                    // Evento de fin de arrastre del marcador
                    google.maps.event.addListener(marker, 'dragend', function() {
                        updateMarkerPosition(marker.getPosition());
                    });
                    
                    // Si tenemos coordenadas guardadas, mostrar el marcador y obtener dirección
                    if (latInput.val() && lngInput.val()) {
                        marker.setVisible(true);
                        
                        // Geocodificar inverso para mostrar la dirección
                        var geocoder = new google.maps.Geocoder();
                        var latLng = new google.maps.LatLng(defaultLat, defaultLng);
                        
                        geocoder.geocode({'location': latLng}, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                addressInput.val(results[0].formatted_address);
                                searchInput.val(results[0].formatted_address);
                            }
                        });
                    }
                    
                    // Si tenemos dirección pero no coordenadas, geocodificar
                    if ((!latInput.val() || !lngInput.val()) && searchInput.val()) {
                        searchLocation(searchInput.val());
                    }
                }
                
                // Actualizar la posición del marcador y los campos de coordenadas
                function updateMarkerPosition(latLng) {
                    marker.setPosition(latLng);
                    marker.setVisible(true);
                    latInput.val(latLng.lat().toFixed(6));
                    lngInput.val(latLng.lng().toFixed(6));
                    
                    // Geocodificar inverso para obtener la dirección
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'location': latLng}, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            addressInput.val(results[0].formatted_address);
                            searchInput.val(results[0].formatted_address);
                        }
                    });
                }
                
                // Buscar una ubicación
                function searchLocation(address) {
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'address': address}, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            map.setCenter(results[0].geometry.location);
                            updateMarkerPosition(results[0].geometry.location);
                        } else {
                            alert('No se pudo encontrar la ubicación: ' + status);
                        }
                    });
                }
                
                // Botón de búsqueda
                $('#twf_agencias_search_location').on('click', function() {
                    var address = searchInput.val();
                    if (address) {
                        searchLocation(address);
                    }
                });
                
                // Buscar al presionar Enter
                searchInput.on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        var address = searchInput.val();
                        if (address) {
                            searchLocation(address);
                        }
                    }
                });
                
                // Cargar el mapa
                var script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo esc_js($api_key); ?>&callback=initMap&libraries=places';
                script.async = true;
                document.head.appendChild(script);
                
                // Callback global para inicializar el mapa
                window.initMap = initMap;
            });
        </script>
        <?php else : ?>
        <div class="notice notice-error inline">
            <p>No se ha configurado la API Key de Google Maps. Por favor, configúrala en la <a href="<?php echo admin_url('admin.php?page=twf-agencias-settings&tab=general'); ?>">configuración general</a> del plugin.</p>
        </div>
        <?php endif; ?>
        <?php
    }

    /**
     * Renderiza el metabox para servicios
     */
    public function render_services_metabox($post) {
        // Añadir nonce para verificación
        wp_nonce_field('twf_agencias_services_nonce', 'twf_agencias_services_nonce');
        
        // Obtener servicios guardados para esta agencia
        $agency_services = get_post_meta($post->ID, '_twf_agencias_services', true);
        if (!is_array($agency_services)) {
            $agency_services = array();
        }
        
        // Obtener todos los servicios disponibles desde la configuración
        $all_services = get_option('twf_agencias_services', array());
        
        if (empty($all_services)) {
            echo '<div class="twf-agencias-services-empty">';
            echo '<p>No hay servicios configurados. Por favor, añade servicios en la configuración del plugin.</p>';
            echo '<a href="' . admin_url('admin.php?page=twf-agencias-settings&tab=servicios') . '" class="button button-primary">Configurar servicios</a>';
            echo '</div>';
            return;
        }
        
        echo '<p class="twf-agencias-services-header">Selecciona los servicios que ofrece esta agencia:</p>';
        echo '<div class="twf-agencias-services-list">';
        
        foreach ($all_services as $service_id => $service) {
            $checked = in_array($service_id, $agency_services) ? 'checked="checked"' : '';
            $active_class = $checked ? 'active' : '';
            ?>
            <div class="twf-agencias-service-item <?php echo $active_class; ?>">
                <label class="service-checkbox-label">
                    <input type="checkbox" 
                           class="twf-service-checkbox"
                           name="twf_agencias_services[]" 
                           value="<?php echo esc_attr($service_id); ?>" 
                           <?php echo $checked; ?>>
                    <span class="twf-service-name"><?php echo esc_html($service['label']); ?></span>
                </label>
            </div>
            <?php
        }
        
        echo '</div>';
    }

    /**
     * Renderiza el metabox para icono personalizado
     */
    public function render_custom_icon_metabox($post) {
        // Añadir nonce para verificación
        wp_nonce_field('twf_agencias_custom_icon_nonce', 'twf_agencias_custom_icon_nonce');
        
        // Obtener icono personalizado actual
        $custom_icon_id = get_post_meta($post->ID, '_twf_agencias_custom_icon', true);
        $custom_icon_url = '';
        
        if (!empty($custom_icon_id)) {
            $custom_icon_url = wp_get_attachment_image_url($custom_icon_id, 'thumbnail');
        }
        
        // Campos del formulario con mejor diseño
        ?>
        <div class="twf-agencias-custom-icon-container">
            <p>Si no se selecciona ningún icono, se utilizará el configurado globalmente. Si no hay configuración global, se usará el icono por defecto.</p>
            
            <div class="twf-agencias-custom-icon-preview">
                <?php if (!empty($custom_icon_url)) : ?>
                    <img src="<?php echo esc_url($custom_icon_url); ?>" alt="Icono personalizado">
                <?php else : ?>
                    <span>No hay icono personalizado</span>
                <?php endif; ?>
            </div>
            
            <input type="hidden" name="twf_agencias_custom_icon" id="twf_agencias_custom_icon" value="<?php echo esc_attr($custom_icon_id); ?>">
            
            <div class="twf-agencias-icon-buttons">
                <button type="button" class="button button-primary twf-agencias-upload-icon">
                    Seleccionar icono
                </button>
                
                <?php if (!empty($custom_icon_id)) : ?>
                    <button type="button" class="button twf-agencias-remove-icon">
                        Eliminar icono
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza el metabox para horarios
     */
    public function render_schedule_metabox($post) {
        // Añadir nonce para verificación
        wp_nonce_field('twf_agencias_schedule_nonce', 'twf_agencias_schedule_nonce');
        
        // Obtener horarios guardados
        $schedule = get_post_meta($post->ID, '_twf_agencias_schedule', true);
        if (!is_array($schedule)) {
            $schedule = array();
        }
        
        // Días de la semana
        $days = array(
            'monday'    => 'Lunes',
            'tuesday'   => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday'  => 'Jueves',
            'friday'    => 'Viernes',
            'saturday'  => 'Sábado',
            'sunday'    => 'Domingo',
        );
        
        echo '<div class="twf-agencias-schedule-container">';
        echo '<table class="form-table twf-agencias-schedule-table">';
        echo '<tr class="twf-schedule-header">
                <th>Día</th>
                <th class="twf-center">Activo</th>
                <th>Hora de apertura</th>
                <th>Hora de cierre</th>
              </tr>';
        
        foreach ($days as $day_id => $day_name) {
            $active = isset($schedule[$day_id]['active']) ? $schedule[$day_id]['active'] : false;
            $open = isset($schedule[$day_id]['open']) ? $schedule[$day_id]['open'] : '09:00';
            $close = isset($schedule[$day_id]['close']) ? $schedule[$day_id]['close'] : '18:00';
            
            ?>
            <tr class="<?php echo $active ? 'twf-day-active' : 'twf-day-inactive'; ?>">
                <td class="twf-day-name"><?php echo esc_html($day_name); ?></td>
                <td class="twf-center">
                    <label class="twf-schedule-toggle">
                        <input type="checkbox" 
                               name="twf_agencias_schedule[<?php echo $day_id; ?>][active]" 
                               id="twf_agencias_schedule_<?php echo $day_id; ?>_active" 
                               value="1" 
                               <?php checked($active, true); ?> 
                               class="twf-agencias-schedule-active">
                        <span class="twf-toggle-slider"></span>
                    </label>
                </td>
                <td>
                    <input type="time" 
                           name="twf_agencias_schedule[<?php echo $day_id; ?>][open]" 
                           id="twf_agencias_schedule_<?php echo $day_id; ?>_open" 
                           value="<?php echo esc_attr($open); ?>" 
                           <?php disabled($active, false); ?> 
                           class="twf-agencias-schedule-time">
                </td>
                <td>
                    <input type="time" 
                           name="twf_agencias_schedule[<?php echo $day_id; ?>][close]" 
                           id="twf_agencias_schedule_<?php echo $day_id; ?>_close" 
                           value="<?php echo esc_attr($close); ?>" 
                           <?php disabled($active, false); ?> 
                           class="twf-agencias-schedule-time">
                </td>
            </tr>
            <?php
        }
        
        echo '</table>';
        echo '</div>';
    }

    /**
     * Guarda los metaboxes cuando se guarda el post
     */
    public function save_meta_boxes($post_id) {
        // Verificar si es un autoguardado
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Verificar el tipo de post
        if (get_post_type($post_id) !== 'agencia') {
            return;
        }
        
        // Verificar permisos de usuario
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Guardar información de contacto
        if (isset($_POST['twf_agencias_contact_info_nonce']) && wp_verify_nonce($_POST['twf_agencias_contact_info_nonce'], 'twf_agencias_contact_info_nonce')) {
            if (isset($_POST['twf_agencias_celular'])) {
                update_post_meta($post_id, '_twf_agencias_celular', sanitize_text_field($_POST['twf_agencias_celular']));
            }
            
            if (isset($_POST['twf_agencias_telefono'])) {
                update_post_meta($post_id, '_twf_agencias_telefono', sanitize_text_field($_POST['twf_agencias_telefono']));
            }
            
            if (isset($_POST['twf_agencias_anexo'])) {
                update_post_meta($post_id, '_twf_agencias_anexo', sanitize_text_field($_POST['twf_agencias_anexo']));
            }
            
            if (isset($_POST['twf_agencias_email'])) {
                update_post_meta($post_id, '_twf_agencias_email', sanitize_email($_POST['twf_agencias_email']));
            }
            
            if (isset($_POST['twf_agencias_direccion'])) {
                update_post_meta($post_id, '_twf_agencias_direccion', sanitize_text_field($_POST['twf_agencias_direccion']));
            }

            if (isset($_POST['twf_agencias_direccion_mostrar'])) {
                update_post_meta($post_id, '_twf_agencias_direccion_mostrar', sanitize_text_field($_POST['twf_agencias_direccion_mostrar']));
            }
            
            // Guardar coordenadas
            if (isset($_POST['twf_agencias_latitud'])) {
                update_post_meta($post_id, '_twf_agencias_latitud', sanitize_text_field($_POST['twf_agencias_latitud']));
            }
            
            if (isset($_POST['twf_agencias_longitud'])) {
                update_post_meta($post_id, '_twf_agencias_longitud', sanitize_text_field($_POST['twf_agencias_longitud']));
            }
            // Guardar servicios
            if (isset($_POST['twf_agencias_services_nonce']) && wp_verify_nonce($_POST['twf_agencias_services_nonce'], 'twf_agencias_services_nonce')) {
                $services = isset($_POST['twf_agencias_services']) ? $_POST['twf_agencias_services'] : array();
                update_post_meta($post_id, '_twf_agencias_services', $services);
            }

            // Guardar horarios
            if (isset($_POST['twf_agencias_schedule_nonce']) && wp_verify_nonce($_POST['twf_agencias_schedule_nonce'], 'twf_agencias_schedule_nonce')) {
                $schedule = isset($_POST['twf_agencias_schedule']) ? $_POST['twf_agencias_schedule'] : array();
                update_post_meta($post_id, '_twf_agencias_schedule', $schedule);
            }
        }
        
        // Resto del código...
    }
}