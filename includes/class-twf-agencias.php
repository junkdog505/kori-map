<?php
/**
 * Clase principal que define la funcionalidad del plugin
 */

class TWF_Agencias {

    // El loader mantiene y registra todos los hooks del plugin
    protected $loader;

    // El identificador único del plugin
    protected $plugin_name;

    // La versión actual del plugin
    protected $version;

    /**
     * Constructor de la clase
     */
    public function __construct() {
        $this->version = TWF_AGENCIAS_VERSION;
        $this->plugin_name = TWF_AGENCIAS_PLUGIN_NAME;

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Carga las dependencias requeridas para el plugin
     */
    private function load_dependencies() {
        // El loader orquesta las acciones y filtros del plugin
        require_once TWF_AGENCIAS_PLUGIN_DIR . 'includes/class-twf-agencias-loader.php';
        
        // La clase que maneja el CPT y taxonomías
        require_once TWF_AGENCIAS_PLUGIN_DIR . 'includes/class-twf-agencias-cpt.php';
        
        // La clase que maneja los metaboxes
        require_once TWF_AGENCIAS_PLUGIN_DIR . 'includes/class-twf-agencias-metaboxes.php';
        
        // La clase que define todas las acciones del lado admin
        require_once TWF_AGENCIAS_PLUGIN_DIR . 'admin/class-twf-agencias-admin.php';
        
        // La clase que define todas las acciones para el frontend
        require_once TWF_AGENCIAS_PLUGIN_DIR . 'public/class-twf-agencias-public.php';

        $this->loader = new TWF_Agencias_Loader();
    }

    /**
     * Registra todos los hooks relacionados con la funcionalidad de admin
     */
    private function define_admin_hooks() {
        $plugin_admin = new TWF_Agencias_Admin($this->get_plugin_name(), $this->get_version());
        $plugin_cpt = new TWF_Agencias_CPT($this->get_plugin_name(), $this->get_version());
        $plugin_metaboxes = new TWF_Agencias_Metaboxes($this->get_plugin_name(), $this->get_version());

        // Estilos y scripts admin
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Registrar menú de administración
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        
        // Registrar configuraciones
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        
        // Registrar CPT y taxonomías
        $this->loader->add_action('init', $plugin_cpt, 'register_cpt');
        $this->loader->add_action('init', $plugin_cpt, 'register_taxonomies');
        
        // Registrar metaboxes
        $this->loader->add_action('add_meta_boxes', $plugin_metaboxes, 'register_meta_boxes');
        $this->loader->add_action('save_post', $plugin_metaboxes, 'save_meta_boxes');
    }

    /**
     * Registra todos los hooks relacionados con la funcionalidad pública
     */
    private function define_public_hooks() {
        $plugin_public = new TWF_Agencias_Public($this->get_plugin_name(), $this->get_version());
    
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Registrar shortcode
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');
        
        // Registrar handlers AJAX
        $this->loader->add_action('init', $plugin_public, 'register_ajax_handlers');
    }
    /**
     * Ejecuta el loader para registrar todos los hooks
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * El nombre del plugin
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * La referencia al cargador
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * La versión del plugin
     */
    public function get_version() {
        return $this->version;
    }
}