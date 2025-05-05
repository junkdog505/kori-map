<?php
/**
 * Pestaña de Servicios
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Obtener los servicios actuales
$services = get_option('twf_agencias_services', array());
?>

<div class="twf-agencias-services">
    <h3>Gestión de Servicios</h3>
    
    <p class="description">
        Añade, edita o elimina los servicios que pueden ofrecer las agencias. Estos servicios aparecerán como opciones en el formulario de edición de cada agencia.
    </p>
    
    <form method="post" action="options.php" id="twf-agencias-services-form">
        <?php settings_fields('twf_agencias_services_settings'); ?>
        
        <div id="twf-agencias-services-container">
            <?php if (!empty($services)) : ?>
                <?php foreach ($services as $service_id => $service) : ?>
                    <div class="twf-agencias-service-item">
                        <input type="text" 
                               name="twf_agencias_services[<?php echo esc_attr($service_id); ?>][label]" 
                               value="<?php echo esc_attr($service['label']); ?>" 
                               class="regular-text twf-agencias-service-label" 
                               placeholder="Nombre del servicio">
                        
                        <button type="button" class="button twf-agencias-remove-service">
                            Eliminar
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="twf-agencias-service-item">
                    <input type="text" 
                           name="twf_agencias_services[service_1][label]" 
                           value="" 
                           class="regular-text twf-agencias-service-label" 
                           placeholder="Nombre del servicio">
                    
                    <button type="button" class="button twf-agencias-remove-service">
                        Eliminar
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <button type="button" class="button twf-agencias-add-service">
            Añadir Servicio
        </button>
        
        <?php submit_button(); ?>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Contador para nuevos servicios
        var serviceCount = $('.twf-agencias-service-item').length;
        
        // Añadir nuevo servicio
        $('.twf-agencias-add-service').on('click', function() {
            serviceCount++;
            
            var newService = '<div class="twf-agencias-service-item">' +
                '<input type="text" ' +
                'name="twf_agencias_services[service_' + serviceCount + '][label]" ' +
                'value="" ' +
                'class="regular-text twf-agencias-service-label" ' +
                'placeholder="Nombre del servicio">' +
                '<button type="button" class="button twf-agencias-remove-service">' +
                'Eliminar' +
                '</button>' +
                '</div>';
            
            $('#twf-agencias-services-container').append(newService);
        });
        
        // Eliminar servicio
        $('#twf-agencias-services-container').on('click', '.twf-agencias-remove-service', function() {
            $(this).parent('.twf-agencias-service-item').remove();
        });
    });
</script>