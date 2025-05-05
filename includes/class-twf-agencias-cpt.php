<?php
/**
 * Clase que maneja el registro del CPT y taxonomías
 */

class TWF_Agencias_CPT {

    // El identificador único del plugin
    private $plugin_name;

    // La versión del plugin
    private $version;

    /**
     * Inicializa la clase
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Registra el Custom Post Type "Agencias"
     */
    public function register_cpt() {
        $labels = array(
            'name'                  => 'Agencias',
            'singular_name'         => 'Agencia',
            'menu_name'             => 'Agencias',
            'name_admin_bar'        => 'Agencia',
            'archives'              => 'Archivo de agencias',
            'attributes'            => 'Atributos de agencia',
            'parent_item_colon'     => 'Agencia padre:',
            'all_items'             => 'Todas las agencias',
            'add_new_item'          => 'Añadir nueva agencia',
            'add_new'               => 'Añadir nueva',
            'new_item'              => 'Nueva agencia',
            'edit_item'             => 'Editar agencia',
            'update_item'           => 'Actualizar agencia',
            'view_item'             => 'Ver agencia',
            'view_items'            => 'Ver agencias',
            'search_items'          => 'Buscar agencia',
            'not_found'             => 'No encontrado',
            'not_found_in_trash'    => 'No encontrado en la papelera',
            'featured_image'        => 'Imagen destacada',
            'set_featured_image'    => 'Establecer imagen destacada',
            'remove_featured_image' => 'Eliminar imagen destacada',
            'use_featured_image'    => 'Usar como imagen destacada',
            'insert_into_item'      => 'Insertar en agencia',
            'uploaded_to_this_item' => 'Subido a esta agencia',
            'items_list'            => 'Lista de agencias',
            'items_list_navigation' => 'Navegación de lista de agencias',
            'filter_items_list'     => 'Filtrar lista de agencias',
        );
        
        $args = array(
            'label'                 => 'Agencia',
            'description'           => 'Agencias con ubicación en el mapa',
            'labels'                => $labels,
            'supports'              => array('title', 'thumbnail'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => $this->get_custom_menu_icon(),
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );
        
        register_post_type('agencia', $args);
    }

    /**
     * Obtiene el icono personalizado para el menú del CPT
     */
    private function get_custom_menu_icon() {
        $icon_path = TWF_AGENCIAS_PLUGIN_DIR . 'admin/images/twf-agencias-icon.png';
        $icon_url = TWF_AGENCIAS_PLUGIN_URL . 'admin/images/twf-agencias-icon.png';
        
        if (file_exists($icon_path)) {
            return $icon_url;
        }
        
        return 'dashicons-location';
    }

    /**
     * Registra la taxonomía "Ubicación" para las agencias
     */
    public function register_taxonomies() {
        // Taxonomía para ubicación (ciudades y distritos)
        $labels = array(
            'name'                       => 'Ubicaciones',
            'singular_name'              => 'Ubicación',
            'menu_name'                  => 'Ubicaciones',
            'all_items'                  => 'Todas las ubicaciones',
            'parent_item'                => 'Ubicación padre',
            'parent_item_colon'          => 'Ubicación padre:',
            'new_item_name'              => 'Nueva ubicación',
            'add_new_item'               => 'Añadir nueva ubicación',
            'edit_item'                  => 'Editar ubicación',
            'update_item'                => 'Actualizar ubicación',
            'view_item'                  => 'Ver ubicación',
            'separate_items_with_commas' => 'Separar ubicaciones con comas',
            'add_or_remove_items'        => 'Añadir o eliminar ubicaciones',
            'choose_from_most_used'      => 'Elegir entre las más usadas',
            'popular_items'              => 'Ubicaciones populares',
            'search_items'               => 'Buscar ubicaciones',
            'not_found'                  => 'No encontrado',
            'no_terms'                   => 'No hay ubicaciones',
            'items_list'                 => 'Lista de ubicaciones',
            'items_list_navigation'      => 'Navegación de lista de ubicaciones',
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true, // Habilitado para ciudades y distritos
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
        );
        
        register_taxonomy('ubicacion', array('agencia'), $args);
    }
}