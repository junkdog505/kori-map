(function($) {
    'use strict';

    $(document).ready(function() {
        // Verificar si estamos en la página de agencia
        var isAgenciaPage = $('body').hasClass('post-type-agencia');
        
        // Inicializar los controladores de medios
        initMediaControls();
        
        // Inicializar comportamientos específicos de los metaboxes
        if (isAgenciaPage) {
            initMetaboxBehaviors();
        }
        
        // Inicializar editor de código para CSS personalizado
        if ($('#twf_agencias_custom_css').length && typeof wp.codeEditor !== 'undefined') {
            initCodeEditor();
        }
    });

    /**
     * Inicializar los controles de Media Uploader
     */
    function initMediaControls() {
        // Para cada botón de subida de ícono en la página
        $('.twf-agencias-upload-icon').each(function() {
            var $button = $(this);
            var mediaUploader = null;
            
            // Determinar qué contenedor y campo oculto usar basado en contexto
            var isCustomIcon = $button.closest('#twf_agencias_custom_icon').length > 0;
            var isConfig = $button.closest('.twf-agencias-form').length > 0;
            
            var $hiddenField, $previewContainer;
            
            if (isCustomIcon) {
                // Estamos en el metabox de ícono personalizado
                $hiddenField = $('#twf_agencias_custom_icon');
                $previewContainer = $('.twf-agencias-custom-icon-preview');
            } else if (isConfig) {
                // Estamos en la configuración general
                $hiddenField = $('#twf_agencias_pin_icon');
                $previewContainer = $('.twf-agencias-icon-preview');
            }
            
            // Solo continuar si encontramos el campo oculto y contenedor de vista previa
            if (!$hiddenField || !$previewContainer) return;
            
            // Manejar clic en el botón de subir
            $button.on('click', function(e) {
                e.preventDefault();
                
                // Si ya existe un media uploader, abrirlo
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                // Crear un nuevo media uploader
                mediaUploader = wp.media({
                    title: 'Seleccionar ícono',
                    button: {
                        text: 'Usar este ícono'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                // Cuando se selecciona una imagen
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    
                    // Actualizar el valor del campo oculto
                    $hiddenField.val(attachment.id);
                    
                    // Actualizar la vista previa
                    if ($previewContainer.find('img').length) {
                        $previewContainer.find('img').attr('src', attachment.url);
                    } else {
                        $previewContainer.html('<img src="' + attachment.url + '" alt="Ícono" />');
                    }
                    
                    // Mostrar el botón de eliminar
                    var $removeBtn = $button.siblings('.twf-agencias-remove-icon');
                    if ($removeBtn.length) {
                        $removeBtn.show();
                    } else {
                        // Si no existe un botón de eliminar, crear uno
                        $('<button type="button" class="button twf-agencias-remove-icon">Eliminar ícono</button>')
                            .insertAfter($button)
                            .on('click', handleRemoveIcon);
                    }
                });
                
                // Abrir el media uploader
                mediaUploader.open();
            });
            
            // Manejar clic en el botón de eliminar
            var $removeBtn = $button.siblings('.twf-agencias-remove-icon');
            if ($removeBtn.length) {
                $removeBtn.on('click', handleRemoveIcon);
            }
            
            function handleRemoveIcon(e) {
                e.preventDefault();
                
                // Limpiar el campo oculto
                $hiddenField.val('');
                
                // Actualizar la vista previa
                if (isCustomIcon) {
                    $previewContainer.html('<span>No hay icono personalizado</span>');
                } else {
                    $previewContainer.html('<span>No hay ícono seleccionado. Se usará el ícono predeterminado de Google Maps.</span>');
                }
                
                // Ocultar el botón de eliminar
                $(this).hide();
            }
        });
        
        // Manejo especial para el botón de ícono de búsqueda
        if ($('.twf-agencias-upload-search-icon').length) {
            var searchIconUploader = null;
            
            // Manejar clic en el botón de subir ícono de búsqueda
            $('.twf-agencias-upload-search-icon').on('click', function(e) {
                e.preventDefault();
                
                // Si ya existe un uploader, abrirlo
                if (searchIconUploader) {
                    searchIconUploader.open();
                    return;
                }
                
                // Crear un nuevo media uploader
                searchIconUploader = wp.media({
                    title: 'Seleccionar ícono de búsqueda',
                    button: {
                        text: 'Usar este ícono'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                // Cuando se selecciona una imagen
                searchIconUploader.on('select', function() {
                    var attachment = searchIconUploader.state().get('selection').first().toJSON();
                    
                    // Verificar si es un SVG
                    var fileExt = attachment.url.split('.').pop().toLowerCase();
                    
                    if (fileExt === 'svg') {
                        // Para archivos SVG, cargar el contenido
                        $.get(attachment.url, function(data) {
                            var svgContent = new XMLSerializer().serializeToString(data.documentElement);
                            $('#twf_agencias_search_button_icon').val(svgContent);
                            $('.twf-agencias-search-icon-preview .icon-display').html(svgContent);
                        });
                    } else {
                        // Para otros formatos, mostrar como imagen y crear SVG wrapper
                        var imgHtml = '<img src="' + attachment.url + '" alt="Ícono de búsqueda" width="24" height="24">';
                        var svgWrapper = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">' +
                                         '<image href="' + attachment.url + '" width="24" height="24" />' +
                                         '</svg>';
                        
                        $('.twf-agencias-search-icon-preview .icon-display').html(imgHtml);
                        $('#twf_agencias_search_button_icon').val(svgWrapper);
                    }
                    
                    // Mostrar el botón de eliminar
                    $('.twf-agencias-remove-search-icon').show();
                });
                
                // Abrir el media uploader
                searchIconUploader.open();
            });
            
            // Manejar clic en el botón de restaurar ícono predeterminado
            $('.twf-agencias-remove-search-icon').on('click', function(e) {
                e.preventDefault();
                
                // Intentar cargar el SVG predeterminado
                var defaultIconUrl = typeof twf_agencias_vars !== 'undefined' && twf_agencias_vars.default_icon_url
                    ? twf_agencias_vars.default_icon_url
                    : '';
                
                if (defaultIconUrl) {
                    $.get(defaultIconUrl, function(data) {
                        var svgContent = new XMLSerializer().serializeToString(data.documentElement);
                        $('#twf_agencias_search_button_icon').val(svgContent);
                        $('.twf-agencias-search-icon-preview .icon-display').html(svgContent);
                    }).fail(function() {
                        // Si falla, usar un SVG básico
                        var defaultSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';
                        $('#twf_agencias_search_button_icon').val(defaultSvg);
                        $('.twf-agencias-search-icon-preview .icon-display').html(defaultSvg);
                    });
                }
            });
        }
    }

    /**
     * Inicializar comportamientos específicos de los metaboxes
     */
    function initMetaboxBehaviors() {
        // Activación/desactivación de horarios
        $('.twf-agencias-schedule-active').on('change', function() {
            var isChecked = $(this).prop('checked');
            var $row = $(this).closest('tr');
            
            // Habilitar/deshabilitar los campos de horario
            $row.find('.twf-agencias-schedule-time').prop('disabled', !isChecked);
            
            // Actualizar clase para estilización
            $row.toggleClass('twf-day-active', isChecked);
            $row.toggleClass('twf-day-inactive', !isChecked);
        });
        
        // Comportamiento para los chips de servicios
        $('.twf-service-checkbox').on('change', function() {
            $(this).closest('.twf-agencias-service-chip').toggleClass('active', this.checked);
        });
        
        // También permitir hacer click en toda la chip
        $('.twf-agencias-service-chip').on('click', function(e) {
            // Si el click fue directo en el checkbox o en su label, no hacer nada adicional
            if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) {
                return;
            }
            
            // Encontrar el checkbox dentro de la chip y togglear su estado
            var $checkbox = $(this).find('input[type="checkbox"]');
            $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
        });
    }

    /**
     * Inicializar el editor de código para CSS
     */
    function initCodeEditor() {
        var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
        editorSettings.codemirror = _.extend(
            {},
            editorSettings.codemirror,
            {
                mode: 'css',
                lineNumbers: true,
                indentUnit: 4,
                tabSize: 4,
                autoCloseBrackets: true,
                matchBrackets: true,
                theme: 'default'
            }
        );
        
        wp.codeEditor.initialize($('#twf_agencias_custom_css'), editorSettings);
    }

})(jQuery);