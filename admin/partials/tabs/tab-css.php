<?php
/**
 * Pestaña de CSS personalizado
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Obtener el CSS personalizado actual
$custom_css = get_option('twf_agencias_custom_css', '');
?>

<form method="post" action="options.php" class="twf-agencias-form">
    <?php settings_fields('twf_agencias_css_settings'); ?>
    
    <h3>CSS Personalizado</h3>
    
    <p class="description">
        Añade estilos CSS personalizados para modificar la apariencia del mapa y los filtros.
    </p>
    
    <textarea id="twf_agencias_custom_css" 
              name="twf_agencias_custom_css" 
              class="large-text code" 
              rows="20"><?php echo esc_textarea($custom_css); ?></textarea>
    
    <h3>Clases CSS disponibles</h3>
    
    <div class="twf-agencias-css-classes">
        <ul>
            <li><code>.twf-agencias-map-container</code> - Contenedor principal del mapa y los filtros</li>
            <li><code>.twf-agencias-filters</code> - Contenedor de los filtros</li>
            <li><code>.twf-agencias-search-input</code> - Campo de búsqueda por texto</li>
            <li><code>.twf-agencias-city-select</code> - Selector de ciudad</li>
            <li><code>.twf-agencias-district-select</code> - Selector de distrito</li>
            <li><code>.twf-agencias-search-button</code> - Botón de búsqueda</li>
            <li><code>.twf-agencias-map</code> - Contenedor del mapa de Google Maps</li>
            <li><code>.twf-agencias-results</code> - Contenedor de resultados de la búsqueda</li>
            <li><code>.twf-agencias-agency-item</code> - Elemento de agencia en los resultados</li>
            <li><code>.twf-agencias-tooltip</code> - Tooltip que se muestra al hacer clic en un pin</li>
            <li><code>.twf-agencias-tooltip-title</code> - Título del tooltip</li>
            <li><code>.twf-agencias-tooltip-address</code> - Dirección en el tooltip</li>
            <li><code>.twf-agencias-tooltip-contact</code> - Información de contacto en el tooltip</li>
            <li><code>.twf-agencias-tooltip-schedule</code> - Horarios en el tooltip</li>
            <li><code>.twf-agencias-tooltip-services</code> - Servicios en el tooltip</li>
        </ul>
    </div>
    
    <h3>Ejemplo de CSS personalizado</h3>
    
    <div class="twf-agencias-css-example">
        <pre>
/* Personalizar el contenedor del mapa */
.twf-agencias-map-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Arial', sans-serif;
}

/* Personalizar los filtros */
.twf-agencias-filters {
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

/* Personalizar los campos de entrada */
.twf-agencias-search-input,
.twf-agencias-city-select,
.twf-agencias-district-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
}

/* Personalizar el botón de búsqueda */
.twf-agencias-search-button {
    background-color: #0073aa;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.twf-agencias-search-button:hover {
    background-color: #005177;
}

/* Personalizar el mapa */
.twf-agencias-map {
    height: 500px;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Personalizar el tooltip */
.twf-agencias-tooltip {
    max-width: 300px;
    padding: 15px;
    border-radius: 5px;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.twf-agencias-tooltip-title {
    font-weight: bold;
    margin-bottom: 10px;
    color: #0073aa;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}
        </pre>
    </div>
    
    <?php submit_button(); ?>
</form>