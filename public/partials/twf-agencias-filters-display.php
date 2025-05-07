<?php
/**
 * Vista pública de los filtros de agencias
 */

// Evitar acceso directo
if (!defined('WPINC')) {
    die;
}
?>

<div class="twf-agencias-filters-container" data-ubicacion="<?php echo esc_attr($atts['ubicacion']); ?>" data-servicios="<?php echo esc_attr($atts['servicios']); ?>">
    <div class="twf-agencias-filters">
        <div class="twf-agencias-filter-row twf-agencias-selects-row">
            <div class="twf-agencias-filter-column">
                <?php if (!empty($labels['city_select'])) : ?>
                <label for="twf-agencias-city-select"><?php echo esc_html($labels['city_select']); ?></label>
                <?php endif; ?>
                
                <select id="twf-agencias-city-select" class="twf-agencias-city-select">
                    <option value=""><?php echo esc_html($labels['city_placeholder']); ?></option>
                    <?php
                    if (!is_wp_error($terms) && !empty($terms)) {
                        foreach ($terms as $term) {
                            echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="twf-agencias-filter-column">
                <?php if (!empty($labels['district_select'])) : ?>
                <label for="twf-agencias-district-select"><?php echo esc_html($labels['district_select']); ?></label>
                <?php endif; ?>
                
                <select id="twf-agencias-district-select" class="twf-agencias-district-select" disabled>
                    <option value=""><?php echo esc_html($labels['district_placeholder']); ?></option>
                </select>
            </div>
        </div>
        
        <div class="twf-agencias-filter-row">
            <?php if (!empty($labels['search_input'])) : ?>
            <label for="twf-agencias-search-input"><?php echo esc_html($labels['search_input']); ?></label>
            <?php endif; ?>
            
            <div class="twf-agencias-search-wrapper">
                <input type="text" 
                       id="twf-agencias-search-input" 
                       class="twf-agencias-search-input" 
                       placeholder="<?php echo esc_attr($labels['search_placeholder']); ?>">
                <button type="button" id="twf-agencias-search-button" class="twf-agencias-search-button" aria-label="<?php echo esc_attr($labels['search_button']); ?>">
                    <?php 
                    if (!empty($search_button_icon)) {
                        echo $search_button_icon;
                    } else {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';
                    }
                    ?>
                </button>
            </div>
        </div>
    </div>
</div>