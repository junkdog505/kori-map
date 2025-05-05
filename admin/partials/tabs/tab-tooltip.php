<?php
/**
 * Pestaña de Tooltip
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Obtener la configuración actual
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
?>

<form method="post" action="options.php" class="twf-agencias-form">
    <?php settings_fields('twf_agencias_tooltip_settings'); ?>
    
    <h3>Campos a mostrar en el tooltip</h3>
    
    <p class="description">
        Selecciona qué información deseas mostrar en el tooltip que aparece al hacer clic en un pin del mapa.
    </p>
    
    <table class="form-table">
        <tr>
            <th scope="row">Información básica</th>
            <td>
                <fieldset>
                    <label for="twf_agencias_tooltip_fields_title">
                        <input type="checkbox" 
                               id="twf_agencias_tooltip_fields_title" 
                               name="twf_agencias_tooltip_fields[title]" 
                               value="1" 
                               <?php checked($tooltip_fields['title'], true); ?>>
                        Nombre de la agencia
                    </label>
                    <br>
                    
                    <label for="twf_agencias_tooltip_fields_address">
                        <input type="checkbox" 
                               id="twf_agencias_tooltip_fields_address" 
                               name="twf_agencias_tooltip_fields[address]" 
                               value="1" 
                               <?php checked($tooltip_fields['address'], true); ?>>
                        Dirección
                    </label>
                </fieldset>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Información de contacto</th>
            <td>
                <fieldset>
                    <label for="twf_agencias_tooltip_fields_phone">
                        <input type="checkbox" 
                               id="twf_agencias_tooltip_fields_phone" 
                               name="twf_agencias_tooltip_fields[phone]" 
                               value="1" 
                               <?php checked($tooltip_fields['phone'], true); ?>>
                        Teléfono
                    </label>
                    <br>
                    
                    <label for="twf_agencias_tooltip_fields_mobile">
                        <input type="checkbox" 
                               id="twf_agencias_tooltip_fields_mobile" 
                               name="twf_agencias_tooltip_fields[mobile]" 
                               value="1" 
                               <?php checked($tooltip_fields['mobile'], true); ?>>
                        Celular
                    </label>
                    <br>
                    
                    <label for="twf_agencias_tooltip_fields_email">
                        <input type="checkbox" 
                               id="twf_agencias_tooltip_fields_email" 
                               name="twf_agencias_tooltip_fields[email]" 
                               value="1" 
                               <?php checked($tooltip_fields['email'], true); ?>>
                        Correo electrónico
                    </label>
                </fieldset>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Información adicional</th>
            <td>
                <fieldset>
                    <label for="twf_agencias_tooltip_fields_schedule">
                        <input type="checkbox" 
                               id="twf_agencias_tooltip_fields_schedule" 
                               name="twf_agencias_tooltip_fields[schedule]" 
                               value="1" 
                               <?php checked($tooltip_fields['schedule'], true); ?>>
                        Horarios
                    </label>
                    <br>
                    
                    <label for="twf_agencias_tooltip_fields_services">
                        <input type="checkbox" 
                               id="twf_agencias_tooltip_fields_services" 
                               name="twf_agencias_tooltip_fields[services]" 
                               value="1" 
                               <?php checked($tooltip_fields['services'], true); ?>>
                        Servicios
                    </label>
                </fieldset>
            </td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
</form>