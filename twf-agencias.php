<?php
/**
 * TWF - Plugin para Agencias
 * 
 * Plugin para gestionar agencias con ubicaciones y servicios, incluyendo integración con Google Maps
 *
 * Plugin Name: TWF - Plugin para Agencias
 * Version: 1.0.0
 * Author: Cristian Amezquita
 */

// Si este archivo es llamado directamente, abortamos.
if (!defined('WPINC')) {
    die;
}

// Definir constantes del plugin
define('TWF_AGENCIAS_VERSION', '1.0.0');
define('TWF_AGENCIAS_PLUGIN_NAME', 'twf-agencias');
define('TWF_AGENCIAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TWF_AGENCIAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TWF_AGENCIAS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Función que se ejecuta durante la activación del plugin.
 */
function activate_twf_agencias() {
    require_once TWF_AGENCIAS_PLUGIN_DIR . 'includes/class-twf-agencias-activator.php';
    TWF_Agencias_Activator::activate();
}

/**
 * Función que se ejecuta durante la desactivación del plugin.
 */
function deactivate_twf_agencias() {
    require_once TWF_AGENCIAS_PLUGIN_DIR . 'includes/class-twf-agencias-deactivator.php';
    TWF_Agencias_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_twf_agencias');
register_deactivation_hook(__FILE__, 'deactivate_twf_agencias');

/**
 * El núcleo del plugin.
 */
require TWF_AGENCIAS_PLUGIN_DIR . 'includes/class-twf-agencias.php';

/**
 * Inicia la ejecución del plugin.
 */
function run_twf_agencias() {
    $plugin = new TWF_Agencias();
    $plugin->run();
}
run_twf_agencias();