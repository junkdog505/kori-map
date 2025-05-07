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
    
    <div class="twf-agencias-editor-container">
        <textarea id="twf_agencias_custom_css" 
                  name="twf_agencias_custom_css" 
                  class="large-text code" 
                  rows="25"><?php echo esc_textarea($custom_css); ?></textarea>
    </div>
    
    <h3>Clases CSS disponibles</h3>
    
    <p class="description">Estas son todas las clases CSS que puede modificar, organizadas por componente:</p>
    
    <div class="twf-agencias-css-classes">
        <h4>Contenedor Principal</h4>
        <ul>
            <li><code>.twf-agencias-map-container</code> - Contenedor principal del mapa y los filtros</li>
        </ul>
        
        <h4>Filtros de Búsqueda</h4>
        <ul>
            <li><code>.twf-agencias-filters</code> - Contenedor de los filtros</li>
            <li><code>.twf-agencias-filter-row</code> - Fila de filtros</li>
            <li><code>.twf-agencias-filter-column</code> - Columna dentro de una fila de filtros</li>
            <li><code>.twf-agencias-selects-row</code> - Fila que contiene los selectores</li>
            <li><code>.twf-agencias-search-wrapper</code> - Contenedor del campo de búsqueda y botón</li>
            <li><code>.twf-agencias-search-input</code> - Campo de búsqueda por texto</li>
            <li><code>.twf-agencias-city-select</code> - Selector de ciudad</li>
            <li><code>.twf-agencias-district-select</code> - Selector de distrito</li>
            <li><code>.twf-agencias-search-button</code> - Botón de búsqueda</li>
            <li><code>.twf-agencias-suggestions</code> - Contenedor de sugerencias de búsqueda</li>
            <li><code>.twf-agencias-suggestion-item</code> - Elemento individual de sugerencia</li>
        </ul>
        
        <h4>Mapa y Pines</h4>
        <ul>
            <li><code>.twf-agencias-map</code> - Contenedor del mapa de Google Maps</li>
            <li><code>.twf-agencias-marker-active</code> - Estilo para el marcador activo</li>
        </ul>
        
        <h4>Tooltip del Pin</h4>
        <ul>
            <li><code>.twf-agencias-tooltip</code> - Tooltip que se muestra al hacer clic en un pin</li>
            <li><code>.twf-agencias-tooltip-title</code> - Título del tooltip</li>
            <li><code>.twf-agencias-tooltip-address</code> - Dirección en el tooltip</li>
            <li><code>.twf-agencias-tooltip-contact</code> - Información de contacto en el tooltip</li>
            <li><code>.twf-agencias-tooltip-schedule</code> - Horarios en el tooltip</li>
            <li><code>.twf-agencias-tooltip-services</code> - Servicios en el tooltip</li>
        </ul>
        
        <h4>Tarjetas de Agencias</h4>
        <ul>
            <li><code>.twf-agencias-cards-container</code> - Contenedor principal de las tarjetas</li>
            <li><code>.twf-agencias-cards-grid</code> - Contenedor de la cuadrícula de tarjetas</li>
            <li><code>.twf-agencias-card</code> - Tarjeta individual de agencia</li>
            <li><code>.twf-agencias-card-image</code> - Imagen de la tarjeta</li>
            <li><code>.twf-agencias-card-content</code> - Contenido de la tarjeta</li>
            <li><code>.twf-agencias-card-category</code> - Categoría (ubicación) de la agencia</li>
            <li><code>.twf-agencias-card-title</code> - Título de la tarjeta</li>
            <li><code>.twf-agencias-card-section</code> - Sección dentro de la tarjeta</li>
            <li><code>.twf-agencias-card-section-title</code> - Título de sección en la tarjeta</li>
            <li><code>.twf-agencias-card-address</code> - Dirección en la tarjeta</li>
            <li><code>.twf-agencias-card-contact</code> - Contenedor de información de contacto</li>
            <li><code>.twf-agencias-card-contact-icon</code> - Ícono de contacto</li>
            <li><code>.twf-agencias-card-contact-info</code> - Información de contacto</li>
            <li><code>.twf-agencias-card-services</code> - Contenedor de servicios</li>
            <li><code>.twf-agencias-card-services-title</code> - Título de la sección de servicios</li>
            <li><code>.twf-agencias-card-services-list</code> - Lista de servicios</li>
            <li><code>.twf-agencias-card-services-item</code> - Elemento individual de servicio</li>
            <li><code>.twf-agencias-card-button</code> - Botón "Cómo llegar"</li>
        </ul>
        
        <h4>Paginación</h4>
        <ul>
            <li><code>.twf-agencias-pagination</code> - Contenedor de la paginación</li>
            <li><code>.twf-agencias-pagination-item</code> - Elemento individual de paginación</li>
            <li><code>.twf-agencias-pagination-arrow</code> - Flecha de navegación</li>
            <li><code>.twf-agencias-pagination-arrow.prev</code> - Flecha anterior</li>
            <li><code>.twf-agencias-pagination-arrow.next</code> - Flecha siguiente</li>
            <li><code>.twf-agencias-pagination-item.active</code> - Elemento activo de paginación</li>
        </ul>
        
        <h4>Resultados de Búsqueda</h4>
        <ul>
            <li><code>.twf-agencias-results</code> - Contenedor de resultados de la búsqueda</li>
            <li><code>.twf-agencias-agency-item</code> - Elemento de agencia en los resultados</li>
            <li><code>.twf-agencias-agency-title</code> - Título de la agencia en resultados</li>
            <li><code>.twf-agencias-agency-address</code> - Dirección en resultados</li>
            <li><code>.twf-agencias-agency-contact</code> - Contacto en resultados</li>
            <li><code>.twf-agencias-agency-services</code> - Servicios en resultados</li>
            <li><code>.twf-agencias-view-on-map</code> - Enlace "Ver en mapa"</li>
        </ul>
        
        <h4>Notificaciones</h4>
        <ul>
            <li><code>.twf-agencias-notification</code> - Contenedor de notificaciones</li>
            <li><code>.twf-agencias-error</code> - Mensaje de error</li>
            <li><code>.twf-agencias-no-results</code> - Mensaje de no resultados</li>
        </ul>
    </div>
    
    <?php submit_button(); ?>
</form>

<style>
/* Estilos para la pestaña de CSS */
.twf-agencias-css-classes {
    background-color: #f9f9f9;
    border: 1px solid #e0e0e0;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.twf-agencias-css-classes h4 {
    margin-top: 15px;
    margin-bottom: 8px;
    color: #23282d;
    font-size: 14px;
    padding-bottom: 5px;
    border-bottom: 1px solid #eee;
}

.twf-agencias-css-classes ul {
    margin: 0;
    padding-left: 20px;
}

.twf-agencias-css-classes li {
    margin-bottom: 6px;
}

.twf-agencias-css-classes code {
    background-color: #f0f0f0;
    padding: 2px 5px;
    border-radius: 3px;
    border: 1px solid #ddd;
    font-size: 12px;
    font-family: monospace;
}

.twf-agencias-editor-container {
    border: 1px solid #ddd;
    margin-bottom: 20px;
}

#twf_agencias_custom_css {
    font-family: monospace;
    width: 100%;
    min-height: 400px;
}

@media screen and (max-width: 782px) {
    .twf-agencias-css-classes li {
        margin-bottom: 8px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Si wp.codeEditor está disponible, usar la implementación básica de WordPress
    if (typeof wp !== 'undefined' && wp.codeEditor) {
        var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
        editorSettings.codemirror = _.extend(
            {},
            editorSettings.codemirror,
            {
                mode: 'css',
                lineNumbers: true,
                indentUnit: 2
            }
        );
        
        var editor = wp.codeEditor.initialize($('#twf_agencias_custom_css'), editorSettings);
    }
});
</script>