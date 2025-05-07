<?php
/**
 * Pestaña de Shortcode
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Obtener los shortcodes
$shortcode_map = '[twf_agencias_map]';
$shortcode_filters = '[twf_agencias_filters]';
$shortcode_cards = '[twf_agencias_cards items_per_page="4"]'; // Shortcode con el atributo por defecto
?>

<div class="twf-agencias-shortcode-tab">
    <h3>Shortcodes disponibles</h3>
    
    <p class="description">
        Utiliza estos shortcodes para mostrar diferentes elementos del plugin de Multi Agencias en cualquier página o entrada.
    </p>
    
    <div class="twf-agencias-shortcodes-list">
        <!-- Mapa con Filtros -->
        <div class="twf-agencias-shortcode-item">
            <h4>Mapa con Filtros (Todo en uno)</h4>
            <p>Muestra el mapa con todos los filtros de búsqueda.</p>
            <div class="twf-agencias-shortcode-code">
                <code id="twf-agencias-shortcode-map"><?php echo esc_html($shortcode_map); ?></code>
                <button type="button" class="button twf-agencias-copy-shortcode" data-clipboard-target="#twf-agencias-shortcode-map">
                    Copiar Shortcode
                </button>
            </div>
            <p class="description">Atributo principal: <code>height="500"</code> - Define la altura del mapa en píxeles.</p>
        </div>
        
        <!-- Solo Filtros -->
        <div class="twf-agencias-shortcode-item">
            <h4>Solo Filtros</h4>
            <p>Muestra solo los filtros de búsqueda (útil para diseños personalizados).</p>
            <div class="twf-agencias-shortcode-code">
                <code id="twf-agencias-shortcode-filters"><?php echo esc_html($shortcode_filters); ?></code>
                <button type="button" class="button twf-agencias-copy-shortcode" data-clipboard-target="#twf-agencias-shortcode-filters">
                    Copiar Shortcode
                </button>
            </div>
        </div>
        
        <!-- Tarjetas de Agencias -->
        <div class="twf-agencias-shortcode-item">
            <h4>Tarjetas de Agencias</h4>
            <p>Muestra las agencias en formato de tarjetas.</p>
            <div class="twf-agencias-shortcode-code">
                <code id="twf-agencias-shortcode-cards"><?php echo esc_html($shortcode_cards); ?></code>
                <button type="button" class="button twf-agencias-copy-shortcode" data-clipboard-target="#twf-agencias-shortcode-cards">
                    Copiar Shortcode
                </button>
            </div>
            <p class="description">El atributo <code>items_per_page="4"</code> ya viene configurado por defecto.</p>
        </div>
    </div>
    
    <div class="twf-agencias-shortcode-instructions">
        <h4>Atributos disponibles</h4>
        
        <table class="form-table">
            <tr>
                <th scope="row">Mapa con Filtros</th>
                <td>
                    <p><code>[twf_agencias_map height="500"]</code> - Altura del mapa en píxeles (predeterminado: 400)</p>
                    <p><code>[twf_agencias_map zoom="12"]</code> - Nivel de zoom del mapa (1-20)</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Tarjetas de Agencias</th>
                <td>
                    <p><code>[twf_agencias_cards items_per_page="4"]</code> - Número de tarjetas por página (predeterminado: 4)</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Atributos comunes</th>
                <td>
                    <p><code>ubicacion="lima"</code> - Filtrar por ubicación específica</p>
                    <p><code>servicios="servicio1,servicio2"</code> - Filtrar por servicios específicos</p>
                </td>
            </tr>
        </table>
        
        <h4>Ejemplos de uso</h4>
        
        <ul>
            <li><code>[twf_agencias_map height="600"]</code> - Mapa con altura de 600 píxeles</li>
            <li><code>[twf_agencias_cards items_per_page="8"]</code> - Tarjetas con 8 elementos por página</li>
        </ul>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Copiar shortcode al portapapeles
    $('.twf-agencias-copy-shortcode').on('click', function() {
        var shortcodeEl = document.getElementById($(this).data('clipboard-target').substring(1));
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

<style>
/* Estilos al estilo WordPress */
.twf-agencias-shortcodes-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.twf-agencias-shortcode-item {
    flex: 1;
    min-width: 250px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.twf-agencias-shortcode-item h4 {
    margin-top: 0;
    margin-bottom: 10px;
    color: #23282d;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
}

.twf-agencias-shortcode-code {
    display: flex;
    align-items: center;
    margin: 10px 0;
}

.twf-agencias-shortcode-code code {
    display: block;
    padding: 8px 12px;
    margin-right: 10px;
    background-color: #f1f1f1;
    border: 1px solid #ddd;
    font-size: 13px;
    flex-grow: 1;
    font-family: monospace;
    border-radius: 3px;
    word-break: break-all;
}

.twf-agencias-shortcode-instructions {
    margin-top: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.twf-agencias-shortcode-instructions h4 {
    color: #23282d;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
    margin-top: 0;
}

.form-table th {
    width: 200px;
    vertical-align: top;
    text-align: left;
    padding: 15px 10px 15px 0;
}

.form-table code {
    background-color: #f1f1f1;
    padding: 3px 5px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 12px;
    border: 1px solid #ddd;
}

.form-table p {
    margin: 6px 0;
}

/* Estilos responsive mejorados */
@media screen and (max-width: 782px) {
    .twf-agencias-shortcodes-list {
        flex-direction: column;
    }
    
    .twf-agencias-shortcode-item {
        width: 100%;
        margin-right: 0;
    }
    
    .twf-agencias-shortcode-code {
        flex-direction: column;
        align-items: stretch;
    }
    
    .twf-agencias-shortcode-code code {
        margin-right: 0;
        margin-bottom: 10px;
        width: 100%;
    }
    
    .form-table th {
        width: 100%;
        display: block;
        padding: 10px 0 5px 0;
    }
    
    .form-table td {
        display: block;
        padding: 0 0 10px 0;
    }
}

/* Mejoras para pantallas muy pequeñas */
@media screen and (max-width: 480px) {
    .twf-agencias-shortcode-item {
        padding: 10px;
    }
    
    .twf-agencias-shortcode-code code {
        font-size: 12px;
        padding: 6px 8px;
        overflow-x: auto;
    }
    
    .button.twf-agencias-copy-shortcode {
        width: 100%;
        text-align: center;
    }
}
</style>