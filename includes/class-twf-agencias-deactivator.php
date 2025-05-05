<?php
/**
 * Se ejecuta durante la desactivación del plugin
 */

class TWF_Agencias_Deactivator {

    /**
     * Se ejecuta durante la desactivación del plugin
     */
    public static function deactivate() {
        // Limpiar las reglas de reescritura
        flush_rewrite_rules();
    }
}