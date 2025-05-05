<?php
/**
 * Página principal de administración del plugin
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}

// Determinar la pestaña activa
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';

?>

<div class="wrap twf-agencias-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=twf-agencias-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">
            Configuración General
        </a>
        <a href="?page=twf-agencias-settings&tab=servicios" class="nav-tab <?php echo $active_tab == 'servicios' ? 'nav-tab-active' : ''; ?>">
            Servicios
        </a>
        <a href="?page=twf-agencias-settings&tab=tooltip" class="nav-tab <?php echo $active_tab == 'tooltip' ? 'nav-tab-active' : ''; ?>">
            Tooltip
        </a>
        <a href="?page=twf-agencias-settings&tab=css" class="nav-tab <?php echo $active_tab == 'css' ? 'nav-tab-active' : ''; ?>">
            CSS
        </a>
        <a href="?page=twf-agencias-settings&tab=shortcode" class="nav-tab <?php echo $active_tab == 'shortcode' ? 'nav-tab-active' : ''; ?>">
            Shortcode
        </a>
    </h2>
    
    <div class="twf-agencias-tab-content">
        <?php
        // Cargar la plantilla correspondiente según la pestaña activa
        switch ($active_tab) {
            case 'general':
                require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/partials/tabs/tab-general.php';
                break;
            case 'servicios':
                require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/partials/tabs/tab-servicios.php';
                break;
            case 'tooltip':
                require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/partials/tabs/tab-tooltip.php';
                break;
            case 'css':
                require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/partials/tabs/tab-css.php';
                break;
            case 'shortcode':
                require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/partials/tabs/tab-shortcode.php';
                break;
            default:
                require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/partials/tabs/tab-general.php';
                break;
        }
        ?>
    </div>
</div>