<?php
/**
 * Pestaña de Configuración General
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Obtener valores actuales
$api_key = get_option('twf_agencias_google_maps_api_key', '');
$pin_icon_id = get_option('twf_agencias_pin_icon', 0);
$default_zoom = get_option('twf_agencias_default_zoom', 12);
$labels = get_option('twf_agencias_labels', array());
$search_button_icon = get_option('twf_agencias_search_button_icon', '');

// Valores por defecto para las etiquetas
$default_labels = array(
    'search_input' => 'Buscar tu oficina más cercana',
    'search_placeholder' => 'Ingresa el nombre de la oficina',
    'city_select' => 'Selecciona ciudad',
    'city_placeholder' => 'Seleccionar ciudad',
    'district_select' => 'Selecciona tu distrito',
    'district_placeholder' => 'Seleccionar distrito',
    'search_button' => 'Buscar',
);

// Combinar con valores predeterminados
$labels = wp_parse_args($labels, $default_labels);

// URL del ícono actual
$pin_icon_url = '';
if (!empty($pin_icon_id)) {
    $pin_icon_url = wp_get_attachment_image_url($pin_icon_id, 'thumbnail');
}

// Ruta por defecto del icono de búsqueda
$default_search_icon = TWF_AGENCIAS_PLUGIN_URL . 'admin/images/search_icon.svg';
?>

<form method="post" action="options.php" class="twf-agencias-form">
    <?php settings_fields('twf_agencias_general_settings'); ?>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="twf_agencias_google_maps_api_key">API Key de Google Maps</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_google_maps_api_key" 
                       name="twf_agencias_google_maps_api_key" 
                       value="<?php echo esc_attr($api_key); ?>" 
                       class="regular-text">
                <p class="description">
                    Ingresa tu API Key de Google Maps. <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Obtener una clave API</a>
                </p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_default_zoom">Nivel de Zoom Predeterminado</label>
            </th>
            <td>
                <select id="twf_agencias_default_zoom" name="twf_agencias_default_zoom" class="regular-text">
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php selected($default_zoom, $i); ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <p class="description">
                    Selecciona el nivel de zoom inicial para el mapa. Un valor más alto muestra un área más pequeña con más detalle.
                </p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_pin_icon">Ícono para el pin del mapa</label>
            </th>
            <td>
                <div class="twf-agencias-icon-preview">
                    <?php if (!empty($pin_icon_url)) : ?>
                        <img src="<?php echo esc_url($pin_icon_url); ?>" alt="Ícono del pin">
                    <?php else : ?>
                        <span>No hay ícono seleccionado. Se usará el ícono predeterminado de Google Maps.</span>
                    <?php endif; ?>
                </div>
                
                <input type="hidden" name="twf_agencias_pin_icon" id="twf_agencias_pin_icon" value="<?php echo esc_attr($pin_icon_id); ?>">
                
                <button type="button" class="button twf-agencias-upload-icon">
                    Seleccionar ícono
                </button>
                
                <?php if (!empty($pin_icon_id)) : ?>
                    <button type="button" class="button twf-agencias-remove-icon">
                        Eliminar ícono
                    </button>
                <?php endif; ?>
                
                <p class="description">
                    Selecciona un ícono para los pines del mapa. Recomendado: 40x40 píxeles.
                </p>
            </td>
        </tr>
    </table>
    
    <h3>Etiquetas de los campos</h3>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="twf_agencias_labels_search_input">Etiqueta del campo de búsqueda</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_labels_search_input" 
                       name="twf_agencias_labels[search_input]" 
                       value="<?php echo esc_attr($labels['search_input']); ?>" 
                       class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_labels_search_placeholder">Placeholder del campo de búsqueda</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_labels_search_placeholder" 
                       name="twf_agencias_labels[search_placeholder]" 
                       value="<?php echo esc_attr($labels['search_placeholder']); ?>" 
                       class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_labels_city_select">Etiqueta del selector de ciudad</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_labels_city_select" 
                       name="twf_agencias_labels[city_select]" 
                       value="<?php echo esc_attr($labels['city_select']); ?>" 
                       class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_labels_city_placeholder">Placeholder del selector de ciudad</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_labels_city_placeholder" 
                       name="twf_agencias_labels[city_placeholder]" 
                       value="<?php echo esc_attr($labels['city_placeholder']); ?>" 
                       class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_labels_district_select">Etiqueta del selector de distrito</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_labels_district_select" 
                       name="twf_agencias_labels[district_select]" 
                       value="<?php echo esc_attr($labels['district_select']); ?>" 
                       class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_labels_district_placeholder">Placeholder del selector de distrito</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_labels_district_placeholder" 
                       name="twf_agencias_labels[district_placeholder]" 
                       value="<?php echo esc_attr($labels['district_placeholder']); ?>" 
                       class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_labels_search_button">Etiqueta del botón de búsqueda</label>
            </th>
            <td>
                <input type="text" 
                       id="twf_agencias_labels_search_button" 
                       name="twf_agencias_labels[search_button]" 
                       value="<?php echo esc_attr($labels['search_button']); ?>" 
                       class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="twf_agencias_search_button_icon">Ícono del botón de búsqueda (SVG)</label>
            </th>
            <td>
                <div class="twf-agencias-search-icon-preview">
                    <div class="icon-display">
                        <?php if (!empty($search_button_icon)) : ?>
                            <?php echo $search_button_icon; ?>
                        <?php else : ?>
                            <img src="<?php echo esc_url($default_search_icon); ?>" alt="Ícono de búsqueda" width="24" height="24">
                        <?php endif; ?>
                    </div>
                </div>
                
                <input type="hidden" name="twf_agencias_search_button_icon" id="twf_agencias_search_button_icon" value="<?php echo esc_attr($search_button_icon); ?>">
                
                <button type="button" class="button twf-agencias-upload-search-icon">
                    Seleccionar ícono de búsqueda
                </button>
                
                <?php if (!empty($search_button_icon)) : ?>
                    <button type="button" class="button twf-agencias-remove-search-icon">
                        Usar ícono predeterminado
                    </button>
                <?php endif; ?>
                
                <p class="description">
                    Selecciona un ícono para el botón de búsqueda. Se recomienda un SVG simple.
                </p>
            </td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
</form>