<?php
/**
 * Pestaña de Shortcode
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Obtener el shortcode
$shortcode = '[twf_agencias_map]';
?>

<div class="twf-agencias-shortcode-tab">
    <h3>Shortcode del Mapa Multi Agencias</h3>
    
    <p class="description">
        Utiliza el siguiente shortcode para mostrar el mapa con todas las agencias y los filtros de búsqueda en cualquier página o entrada.
    </p>
    
    <div class="twf-agencias-shortcode-container">
        <code id="twf-agencias-shortcode"><?php echo esc_html($shortcode); ?></code>
        <button type="button" class="button twf-agencias-copy-shortcode" data-clipboard-target="#twf-agencias-shortcode">
            Copiar Shortcode
        </button>
    </div>
    
    <div class="twf-agencias-shortcode-instructions">
        <h4>Instrucciones de uso</h4>
        
        <ol>
            <li>Copia el shortcode anterior.</li>
            <li>Pega el shortcode en el editor de cualquier página o entrada donde desees mostrar el mapa.</li>
            <li>Guarda la página o entrada y visualiza el resultado.</li>
        </ol>
        
        <p>
            El mapa mostrará todas las agencias registradas y los usuarios podrán filtrar por nombre, ciudad y distrito.
        </p>
        
        <h4>Opciones avanzadas</h4>
        
        <p class="description">
            Puedes personalizar el shortcode con los siguientes atributos:
        </p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Atributo</th>
                    <th>Descripción</th>
                    <th>Valor predeterminado</th>
                    <th>Ejemplo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>height</code></td>
                    <td>Altura del mapa en píxeles.</td>
                    <td>400</td>
                    <td><code>[twf_agencias_map height="500"]</code></td>
                </tr>
                <tr>
                    <td><code>zoom</code></td>
                    <td>Nivel de zoom inicial del mapa (1-20).</td>
                    <td>12</td>
                    <td><code>[twf_agencias_map zoom="10"]</code></td>
                </tr>
                <tr>
                    <td><code>ubicacion</code></td>
                    <td>Filtrar agencias por ubicación específica.</td>
                    <td>Todas</td>
                    <td><code>[twf_agencias_map ubicacion="lima"]</code></td>
                </tr>
                <tr>
                    <td><code>servicios</code></td>
                    <td>Filtrar agencias por servicios específicos (separados por comas).</td>
                    <td>Todos</td>
                    <td><code>[twf_agencias_map servicios="servicio1,servicio2"]</code></td>
                </tr>
                <tr>
                    <td><code>mostrar_filtros</code></td>
                    <td>Mostrar u ocultar los filtros de búsqueda.</td>
                    <td>true</td>
                    <td><code>[twf_agencias_map mostrar_filtros="false"]</code></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Copiar shortcode al portapapeles
        $('.twf-agencias-copy-shortcode').on('click', function() {
            var shortcodeEl = document.getElementById('twf-agencias-shortcode');
            var range = document.createRange();
            range.selectNode(shortcodeEl);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            
            // Cambiar texto del botón temporalmente
            var $button = $(this);
            var originalText = $button.text();
            $button.text('¡Copiado!');
            
            setTimeout(function() {
                $button.text(originalText);
            }, 2000);
        });
    });
</script>