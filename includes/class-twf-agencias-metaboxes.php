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
        
        // Metabox para icono personalizado
        add_meta_box(
            'twf_agencias_custom_icon',
            'Icono personalizado',
            array($this, 'render_custom_icon_metabox'),
            'agencia',
            'side',
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
        $direccion = get_post_meta($post->ID, '_twf_agencias_direccion', true);
        
        // Campos del formulario con nuevo diseño
        ?>
        <div class="twf-agencias-contact-grid">
            <div class="twf-agencias-contact-row">
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_celular">Celular:</label>
                    <input type="text" id="twf_agencias_celular" name="twf_agencias_celular" 
                           value="<?php echo esc_attr($celular); ?>" class="widefat">
                </div>
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_telefono">Teléfono:</label>
                    <input type="text" id="twf_agencias_telefono" name="twf_agencias_telefono" 
                           value="<?php echo esc_attr($telefono); ?>" class="widefat">
                </div>
            </div>
            
            <div class="twf-agencias-contact-row">
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_anexo">Anexo:</label>
                    <input type="text" id="twf_agencias_anexo" name="twf_agencias_anexo" 
                           value="<?php echo esc_attr($anexo); ?>" class="widefat">
                </div>
                <div class="twf-agencias-contact-column">
                    <label for="twf_agencias_email">Correo Electrónico:</label>
                    <input type="email" id="twf_agencias_email" name="twf_agencias_email" 
                           value="<?php echo esc_attr($email); ?>" class="widefat">
                </div>
            </div>
            
            <div class="twf-agencias-contact-row">
                <div class="twf-agencias-contact-full">
                    <label for="twf_agencias_direccion">Dirección:</label>
                    <input type="text" id="twf_agencias_direccion" name="twf_agencias_direccion" 
                           value="<?php echo esc_attr($direccion); ?>" class="widefat">
                </div>
            </div>
        </div>
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
        }
        
        // Guardar servicios
        if (isset($_POST['twf_agencias_services_nonce']) && wp_verify_nonce($_POST['twf_agencias_services_nonce'], 'twf_agencias_services_nonce')) {
            $services = isset($_POST['twf_agencias_services']) ? (array) $_POST['twf_agencias_services'] : array();
            $sanitized_services = array_map('sanitize_key', $services);
            update_post_meta($post_id, '_twf_agencias_services', $sanitized_services);
        }
        
        // Guardar icono personalizado
        if (isset($_POST['twf_agencias_custom_icon_nonce']) && wp_verify_nonce($_POST['twf_agencias_custom_icon_nonce'], 'twf_agencias_custom_icon_nonce')) {
            if (isset($_POST['twf_agencias_custom_icon'])) {
                update_post_meta($post_id, '_twf_agencias_custom_icon', absint($_POST['twf_agencias_custom_icon']));
            }
        }
        
        // Guardar horarios
        if (isset($_POST['twf_agencias_schedule_nonce']) && wp_verify_nonce($_POST['twf_agencias_schedule_nonce'], 'twf_agencias_schedule_nonce')) {
            $schedule = isset($_POST['twf_agencias_schedule']) ? $_POST['twf_agencias_schedule'] : array();
            $sanitized_schedule = array();
            
            foreach ($schedule as $day => $day_data) {
                $sanitized_schedule[$day]['active'] = isset($day_data['active']) ? true : false;
                $sanitized_schedule[$day]['open'] = isset($day_data['open']) ? sanitize_text_field($day_data['open']) : '09:00';
                $sanitized_schedule[$day]['close'] = isset($day_data['close']) ? sanitize_text_field($day_data['close']) : '18:00';
            }
            
            update_post_meta($post_id, '_twf_agencias_schedule', $sanitized_schedule);
        }
    }
}