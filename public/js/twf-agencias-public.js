(function($) {
    'use strict';

    // Variables globales
    var map, markers = [], infoWindow, geocoder, autocomplete;
    var agencies = []; // Guardar todas las agencias
    var notification = null; // Para mensajes de notificación
    
    $(document).ready(function() {
        // Inicializar solo si existe el contenedor del mapa
        if ($('#twf-agencias-map').length || $('.twf-agencias-filters-container').length) {

            // Cargar el script de Google Maps si hay API key
            if (twf_agencias_vars.api_key) {
                loadGoogleMapsScript();
            } else {
                displayApiKeyError();
            }
            
            // Inicializar eventos
            initEvents();

            initCards();
            
            // Crear contenedor de notificación
            createNotification();
        }
    });
    
    /**
     * Crea el contenedor de notificación
     */
    function createNotification() {
        notification = $('<div class="twf-agencias-notification"></div>');
        notification.css({
            'position': 'fixed',
            'top': '20px',
            'left': '50%',
            'transform': 'translateX(-50%)',
            'background-color': 'rgba(0, 0, 0, 0.7)',
            'color': 'white',
            'padding': '10px 20px',
            'border-radius': '4px',
            'z-index': '9999',
            'display': 'none',
            'font-size': '14px'
        });
        $('body').append(notification);
    }
    
    /**
     * Muestra una notificación temporal
     */
    function showNotification(message, duration) {
        if (!notification) return;
        
        notification.text(message);
        notification.fadeIn(300);
        
        setTimeout(function() {
            notification.fadeOut(300);
        }, duration || 3000);
    }
    
    /**
     * Carga el script de Google Maps
     */
    function loadGoogleMapsScript() {
        var script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + twf_agencias_vars.api_key + '&libraries=places&callback=initMap';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
        
        // Callback global para inicializar el mapa
        window.initMap = function() {
            initializeMap();
            loadAgencies();
        };
    }
    
    /**
     * Muestra un error si no hay API key
     */
    function displayApiKeyError() {
        $('#twf-agencias-map').html('<div class="twf-agencias-error">Error: No se ha configurado la API Key de Google Maps. Por favor, configúrala en la administración del plugin.</div>');
    }
    
    /**
     * Inicializa el mapa
     */
    function initializeMap() {
        var mapContainer = $('#twf-agencias-map');
        var defaultZoom = parseInt(mapContainer.closest('.twf-agencias-map-container').data('zoom')) || 12;
        
        // Opciones del mapa
        var mapOptions = {
            center: { lat: -16.3988667, lng: -71.5369607 }, // Arequipa, Perú por defecto
            zoom: 6,
            mapTypeControl: true,
            fullscreenControl: true,
            streetViewControl: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        
        // Crear el mapa
        map = new google.maps.Map(document.getElementById('twf-agencias-map'), mapOptions);
        
        // Crear la ventana de información
        infoWindow = new google.maps.InfoWindow({
            maxWidth: 350
        });
        
        // Inicializar geocoder
        geocoder = new google.maps.Geocoder();
        
        // Inicializar autocompletado
        initAutocomplete();
    }
    
    /**
     * Inicializa el autocompletado para el campo de búsqueda
     */
    function initAutocomplete() {
        var searchInput = document.getElementById('twf-agencias-search-input');
        
        // Crear lista de sugerencias
        var suggestionsContainer = document.createElement('div');
        suggestionsContainer.className = 'twf-agencias-suggestions';
        searchInput.parentNode.appendChild(suggestionsContainer);
        
        // Añadir evento input al campo de búsqueda
        searchInput.addEventListener('input', function() {
            var searchTerm = this.value.toLowerCase();
            suggestionsContainer.innerHTML = '';
            
            if (searchTerm.length < 2) {
                suggestionsContainer.style.display = 'none';
                return;
            }
            
            // Filtrar agencias según término de búsqueda
            var matches = agencies.filter(function(agency) {
                var title = agency.title.rendered || agency.title || '';
                var direccion = '';
                
                if (agency.meta_datos && agency.meta_datos.direccion) {
                    direccion = agency.meta_datos.direccion;
                } else if (agency.direccion) {
                    direccion = agency.direccion;
                }
                
                return title.toLowerCase().includes(searchTerm) || 
                       direccion.toLowerCase().includes(searchTerm);
            });
            
            // Mostrar sugerencias
            if (matches.length > 0) {
                suggestionsContainer.style.display = 'block';
                
                matches.slice(0, 5).forEach(function(agency) {
                    var suggestionItem = document.createElement('div');
                    suggestionItem.className = 'twf-agencias-suggestion-item';
                    suggestionItem.textContent = agency.title.rendered || agency.title;
                    
                    suggestionItem.addEventListener('click', function() {
                        searchInput.value = this.textContent;
                        suggestionsContainer.style.display = 'none';
                        
                        // Mostrar solo esta agencia en el mapa
                        displaySingleAgency(agency);
                    });
                    
                    suggestionsContainer.appendChild(suggestionItem);
                });
            } else {
                suggestionsContainer.style.display = 'none';
            }
        });
        
        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (event.target !== searchInput && !suggestionsContainer.contains(event.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    }
    
    /**
     * Muestra una sola agencia en el mapa
     */
    function displaySingleAgency(agency) {
        // Limpiar marcadores
        clearMarkers();
        
        // Crear marcador para esta agencia
        var bounds = new google.maps.LatLngBounds();
        
        var lat, lng;
            
        if (agency.meta_datos && agency.meta_datos.latitud && agency.meta_datos.longitud) {
            lat = parseFloat(agency.meta_datos.latitud);
            lng = parseFloat(agency.meta_datos.longitud);
        } else if (agency.latitud && agency.longitud) {
            lat = parseFloat(agency.latitud);
            lng = parseFloat(agency.longitud);
        }
        
        if (!isNaN(lat) && !isNaN(lng)) {
            createMarker(agency, lat, lng, bounds);
            
            // Centrar mapa en esta agencia
            map.fitBounds(bounds);
            map.setZoom(16);
            
            // Abrir infoWindow
            if (markers.length > 0) {
                infoWindow.setContent(agency.infoWindow);
                infoWindow.open(map, markers[0]);
            }
        } else if (agency.direccion || (agency.meta_datos && agency.meta_datos.direccion)) {
            var direccion = agency.direccion || agency.meta_datos.direccion;
            
            geocoder.geocode({'address': direccion}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    var position = results[0].geometry.location;
                    createMarker(agency, position.lat(), position.lng(), bounds);
                    
                    // Centrar mapa
                    map.fitBounds(bounds);
                    map.setZoom(16);
                    
                    // Abrir infoWindow
                    if (markers.length > 0) {
                        infoWindow.setContent(agency.infoWindow);
                        infoWindow.open(map, markers[0]);
                    }
                }
            });
        }
    }
    
    /**
     * Carga las agencias
     */
    function loadAgencies() {
        // Filtros iniciales
        var container = $('.twf-agencias-map-container');
        var filters = {
            ubicacion: container.data('ubicacion') || '',
            servicios: container.data('servicios') || ''
        };
        
        // Hacer la solicitud AJAX
        $.ajax({
            url: twf_agencias_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'twf_agencias_get_agencies',
                nonce: twf_agencias_vars.nonce,
                filters: filters
            },
            success: function(response) {
                if (response.success && response.data) {
                    // Guardar todas las agencias para usar en autocompletado
                    agencies = response.data;
                    displayAgencies(response.data);
                }
            },
            error: function(xhr, status, error) {
                showNotification('Error al cargar las agencias. Por favor, intenta nuevamente.', 3000);
            }
        });
    }
    
    /**
     * Muestra las agencias en el mapa
     */
    function displayAgencies(agencies) {
        // Limpiar marcadores existentes
        clearMarkers();
        
        // Si no hay agencias, mostrar mensaje
        if (!agencies || agencies.length === 0) {
            showNotification('No se encontraron agencias con los filtros seleccionados.', 3000);
            return;
        }
        
        // Obtener los límites del mapa para centrar
        var bounds = new google.maps.LatLngBounds();
        var pendingGeocode = 0;
        var markersCreated = 0;
        
        // Crear marcadores para cada agencia
        $.each(agencies, function(index, agency) {
            // Obtener latitud y longitud
            var lat, lng;
            
            if (agency.meta_datos && agency.meta_datos.latitud && agency.meta_datos.longitud) {
                lat = parseFloat(agency.meta_datos.latitud);
                lng = parseFloat(agency.meta_datos.longitud);
            } else if (agency.latitud && agency.longitud) {
                lat = parseFloat(agency.latitud);
                lng = parseFloat(agency.longitud);
            }
            
            // Si tenemos coordenadas válidas, crear marcador
            if (!isNaN(lat) && !isNaN(lng)) {
                createMarker(agency, lat, lng, bounds);
                markersCreated++;
            } 
            // Si no tenemos coordenadas, intentar geocodificar la dirección
            else if (agency.direccion) {
                pendingGeocode++;
                geocoder.geocode({'address': agency.direccion}, function(results, status) {
                    pendingGeocode--;
                    
                    if (status === google.maps.GeocoderStatus.OK) {
                        var position = results[0].geometry.location;
                        createMarker(agency, position.lat(), position.lng(), bounds);
                        markersCreated++;
                        
                        // Si no quedan geocodificaciones pendientes, ajustar el mapa
                        if (pendingGeocode === 0 && markers.length > 0) {
                            map.fitBounds(bounds);
                        }
                    }
                });
            } else if (agency.meta_datos && agency.meta_datos.direccion) {
                pendingGeocode++;
                geocoder.geocode({'address': agency.meta_datos.direccion}, function(results, status) {
                    pendingGeocode--;
                    
                    if (status === google.maps.GeocoderStatus.OK) {
                        var position = results[0].geometry.location;
                        createMarker(agency, position.lat(), position.lng(), bounds);
                        markersCreated++;
                        
                        // Si no quedan geocodificaciones pendientes, ajustar el mapa
                        if (pendingGeocode === 0 && markers.length > 0) {
                            map.fitBounds(bounds);
                        }
                    }
                });
            }
        });
        
        // Si no hay geocodificaciones pendientes y hay marcadores, ajustar el mapa
        if (pendingGeocode === 0 && markers.length > 0) {
            map.fitBounds(bounds);
        } else if (markersCreated === 0 && pendingGeocode === 0) {
            showNotification('No se pudieron ubicar las agencias en el mapa. Verifica las coordenadas.', 3000);
        }
    }
    
    /**
     * Crea un marcador para una agencia
     */
    function createMarker(agency, lat, lng, bounds) {
        var position = new google.maps.LatLng(lat, lng);
        
        // Obtener título
        var title = agency.title ? (agency.title.rendered || agency.title) : "Agencia";
        
        // Si hay icono personalizado, usarlo
        var iconUrl = '';
        if (agency.meta_datos && agency.meta_datos.custom_icon) {
            iconUrl = agency.meta_datos.custom_icon;
        } else if (agency.icon) {
            iconUrl = agency.icon;
        }
        
        if (iconUrl) {
            // Crear un marcador temporal con icono predeterminado
            var tempMarker = new google.maps.Marker({
                position: position,
                map: map,
                title: title
            });
            
            // Cargar la imagen para obtener sus dimensiones originales
            var img = new Image();
            img.onload = function() {
                // Calcular nueva escala manteniendo la relación de aspecto
                var origWidth = this.width;
                var origHeight = this.height;
                var scaleFactor = 30 / Math.max(origWidth, origHeight); // Usar tamaño más pequeño (30px)
                
                // Eliminar marcador temporal del mapa
                tempMarker.setMap(null);
                
                // Eliminar del array si existe
                var tempIndex = markers.indexOf(tempMarker);
                if (tempIndex > -1) {
                    markers.splice(tempIndex, 1);
                }
                
                // Crear el marcador definitivo con el icono redimensionado
                var newMarker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: title,
                    icon: {
                        url: iconUrl,
                        scaledSize: new google.maps.Size(
                            Math.round(origWidth * scaleFactor), 
                            Math.round(origHeight * scaleFactor)
                        )
                    }
                });
                
                // Añadir evento de clic al marcador
                newMarker.addListener('click', function() {
                    infoWindow.setContent(agency.infoWindow);
                    infoWindow.open(map, newMarker);
                });
                
                // Añadir el marcador definitivo al array
                markers.push(newMarker);
            };
            img.src = iconUrl;
            
            // Añadir evento de clic al marcador temporal
            tempMarker.addListener('click', function() {
                infoWindow.setContent(agency.infoWindow);
                infoWindow.open(map, tempMarker);
            });
            
            // Añadir el marcador temporal al array
            markers.push(tempMarker);
        } else {
            // Crear el marcador sin icono personalizado
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: title
            });
            
            // Añadir evento de clic al marcador
            marker.addListener('click', function() {
                infoWindow.setContent(agency.infoWindow);
                infoWindow.open(map, marker);
            });
            
            // Añadir el marcador al array
            markers.push(marker);
        }
        
        // Extender los límites
        bounds.extend(position);
    }
    
    /**
     * Limpia los marcadores del mapa
     */
    function clearMarkers() {
        $.each(markers, function(index, marker) {
            marker.setMap(null);
        });
        
        markers = [];
    }
    
    /**
     * Inicializa los eventos
     */
    function initEvents() {
        // Gestión de la relación Ciudad-Distrito
        var $citySelect = $('#twf-agencias-city-select');
        var $districtSelect = $('#twf-agencias-district-select');
        
        // Evento de cambio en el selector de ciudad
        $citySelect.on('change', function() {
            var citySlug = $(this).val();
            
            // Si hay ciudad seleccionada, actualizar mapa
            if (citySlug) {
                filterAgenciesByCity(citySlug);
            } else {
                // Si no hay ciudad seleccionada, mostrar todas las agencias
                resetDistrictSelect();
                displayAgencies(agencies);
            }
            
            // Si no hay ciudad seleccionada, desactivar y limpiar distritos
            if (!citySlug) {
                resetDistrictSelect();
                return;
            }
            
            // Mostrar cargando en el selector de distritos
            $districtSelect.prop('disabled', true)
                .html('<option value="">Cargando distritos...</option>');
            
            // Hacer la solicitud AJAX para obtener los distritos de esta ciudad
            $.ajax({
                url: twf_agencias_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'twf_agencias_get_districts',
                    nonce: twf_agencias_vars.nonce,
                    city_id: citySlug
                },
                success: function(response) {
                    if (response.success && response.data) {
                        updateDistrictSelect(response.data);
                    } else {
                        resetDistrictSelect();
                    }
                },
                error: function() {
                    resetDistrictSelect();
                }
            });
        });
        
        // Evento de cambio en el selector de distrito
        $districtSelect.on('change', function() {
            var districtSlug = $(this).val();
            if (districtSlug) {
                filterAgenciesByDistrict(districtSlug);
            } else {
                // Si no hay distrito seleccionado pero sí ciudad, filtrar por ciudad
                var citySlug = $citySelect.val();
                if (citySlug) {
                    filterAgenciesByCity(citySlug);
                } else {
                    // Si no hay ni ciudad ni distrito, mostrar todas
                    displayAgencies(agencies);
                }
            }
        });
        
        // Función para actualizar el selector de distritos con nuevos datos
        function updateDistrictSelect(districts) {
            // Limpiar y preparar el select
            $districtSelect.empty();
            $districtSelect.append('<option value="">' + twf_agencias_vars.labels.district_placeholder + '</option>');
            
            // Añadir los distritos
            if (districts.length > 0) {
                $.each(districts, function(index, district) {
                    $districtSelect.append('<option value="' + district.slug + '">' + district.name + '</option>');
                });
                $districtSelect.prop('disabled', false);
            } else {
                // Si no hay distritos, dejarlo deshabilitado
                $districtSelect.prop('disabled', true);
            }
        }
        
        // Función para resetear el selector de distritos
        function resetDistrictSelect() {
            $districtSelect.empty()
                .append('<option value="">' + twf_agencias_vars.labels.district_placeholder + '</option>')
                .prop('disabled', true);
        }
        
        // Inicializar el selector de distritos si hay una ciudad seleccionada al cargar
        if ($citySelect.val()) {
            $citySelect.trigger('change');
        }
        
        // Evento de búsqueda
        $('#twf-agencias-search-button').on('click', function() {
            searchAgencies();
        });
        
        // Búsqueda al presionar Enter en el campo de búsqueda
        $('#twf-agencias-search-input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchAgencies();
            }
        });
    }

    /**
     * Inicializa la funcionalidad de las tarjetas
     */
    function initCards() {
        var $cardsContainer = $('.twf-agencias-cards-container');
        
        if ($cardsContainer.length) {
            // Cargar las agencias iniciales
            loadAgenciesForCards();
            
            // Manejar cambios en los filtros
            $(document).on('twf_agencias_filtered', function(event, data) {
                loadAgenciesForCards(data.filters);
            });
            
            // Manejar la paginación
            $(document).on('click', '.twf-agencias-pagination-item', function() {
                if (!$(this).hasClass('active')) {
                    var page = $(this).data('page');
                    $cardsContainer.attr('data-page', page);
                    loadAgenciesForCards();
                }
            });
            
            // Manejar flechas de paginación
            $(document).on('click', '.twf-agencias-pagination-arrow', function() {
                if (!$(this).hasClass('disabled')) {
                    var currentPage = parseInt($cardsContainer.attr('data-page'));
                    var totalPages = parseInt($cardsContainer.attr('data-total-pages'));
                    var page;
                    
                    if ($(this).hasClass('prev')) {
                        page = Math.max(1, currentPage - 1);
                    } else {
                        page = Math.min(totalPages, currentPage + 1);
                    }
                    
                    if (page !== currentPage) {
                        $cardsContainer.attr('data-page', page);
                        loadAgenciesForCards();
                    }
                }
            });
        }
    }

    /**
     * Carga las agencias para las tarjetas
     */
    function loadAgenciesForCards(customFilters) {
        var $cardsContainer = $('.twf-agencias-cards-container');
        
        if (!$cardsContainer.length) return;
        
        var page = parseInt($cardsContainer.attr('data-page')) || 1;
        var itemsPerPage = parseInt($cardsContainer.attr('data-items-per-page')) || 4;
        
        // Filtros iniciales
        var filters = customFilters || {
            ubicacion: $cardsContainer.data('ubicacion') || '',
            servicios: $cardsContainer.data('servicios') || '',
            page: page,
            items_per_page: itemsPerPage
        };
        
        // Hacer la solicitud AJAX
        $.ajax({
            url: twf_agencias_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'twf_agencias_get_agencies',
                nonce: twf_agencias_vars.nonce,
                filters: filters
            },
            beforeSend: function() {
                $cardsContainer.addClass('loading');
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderCards(response.data, page, itemsPerPage);
                } else {
                    $cardsContainer.find('.twf-agencias-cards-grid').html('<div class="twf-agencias-no-results">No se encontraron agencias con los filtros seleccionados.</div>');
                    $cardsContainer.find('.twf-agencias-pagination').empty();
                }
            },
            error: function() {
                showNotification('Error al cargar las agencias. Por favor, intenta nuevamente.', 3000);
            },
            complete: function() {
                $cardsContainer.removeClass('loading');
            }
        });
    }

    /**
     * Renderiza las tarjetas de agencias
     */
    function renderCards(agencies, page, itemsPerPage) {
        var $cardsContainer = $('.twf-agencias-cards-container');
        var $cardsGrid = $cardsContainer.find('.twf-agencias-cards-grid');
        var $pagination = $cardsContainer.find('.twf-agencias-pagination');
        
        // Forzar items_per_page a 4 (1 elemento por fila, 4 filas)
        itemsPerPage = 4;
        
        // Limpiar contenedores
        $cardsGrid.empty();
        $pagination.empty();
        
        // Si no hay agencias, mostrar mensaje
        if (!agencies || agencies.length === 0) {
            $cardsGrid.html('<div class="twf-agencias-no-results">No se encontraron agencias.</div>');
            return;
        }
        
        // Calcular total de páginas
        var totalAgencies = agencies.length;
        var totalPages = Math.ceil(totalAgencies / itemsPerPage);
        
        // Guardar el total de páginas en el contenedor
        $cardsContainer.attr('data-total-pages', totalPages);
        
        // Calcular índices para la página actual
        var startIndex = (page - 1) * itemsPerPage;
        var endIndex = Math.min(startIndex + itemsPerPage, totalAgencies);
        
        // Obtener las agencias para la página actual
        var pageAgencies = agencies.slice(startIndex, endIndex);
        
        // Crear las tarjetas
        $.each(pageAgencies, function(index, agency) {
            var card = createAgencyCard(agency);
            $cardsGrid.append(card);
        });
        
        // Crear la paginación
        createPagination(page, totalPages, $pagination);
    }

    /**
     * Crea una tarjeta de agencia
     */
    function createAgencyCard(agency) {
        var title = agency.title ? (agency.title.rendered || agency.title) : "Agencia";
        var featuredImage = agency.featured_image || '';
        
        if (!featuredImage) {
            featuredImage = twf_agencias_vars.plugin_url + 'public/images/placeholder.jpg';
        }
        
        // Obtener categoría (ciudad)
        var category = "Oficina";
        if (agency.terms && agency.terms.ubicacion) {
            // Primero buscar el término que tiene parent = 0 (ciudad/término padre)
            var cityTerm = agency.terms.ubicacion.find(function(term) {
                return term.parent === 0;
            });
            
            if (cityTerm) {
                category += " " + cityTerm.name;
            } else {
                // Si no se encuentra un término padre directamente, buscar el padre del término actual
                var districtTerm = agency.terms.ubicacion[0]; // Tomar el primer término disponible
                
                if (districtTerm && districtTerm.parent !== 0) {
                    // Necesitamos buscar el nombre del término padre usando su ID
                    var parentId = districtTerm.parent;
                    
                    // Esta información debería estar disponible en twf_agencias_vars.location_terms si la pasamos desde el PHP
                    if (twf_agencias_vars.location_terms && twf_agencias_vars.location_terms[parentId]) {
                        category += " " + twf_agencias_vars.location_terms[parentId].name;
                    }
                }
            }
        }
        
        var address = '';
        var phone = '';
        var anexo = '';
        var email = '';
        var services = [];

        console.log('Servicios para agencia ' + agency.id + ':', services);

        
        if (agency.meta_datos) {
            address = agency.meta_datos.direccion || '';
            phone = agency.meta_datos.telefono || '';
            anexo = agency.meta_datos.anexo || '';
            email = agency.meta_datos.email || '';
            services = agency.meta_datos.services || [];
        }
        
        var servicesHtml = '';
        if (services && services.length > 0) {
            var allServices = twf_agencias_vars.services || {};
            
            servicesHtml = '<div class="twf-agencias-card-services">';
            servicesHtml += '<div class="twf-agencias-card-services-title">Operaciones que puede realizar</div>';
            servicesHtml += '<ul class="twf-agencias-card-services-list">';
            
            $.each(services, function(index, serviceId) {
                var serviceName = allServices[serviceId] ? allServices[serviceId].label : serviceId;
                servicesHtml += '<li class="twf-agencias-card-services-item">' + serviceName + '</li>';
            });
            
            servicesHtml += '</ul>';
            servicesHtml += '</div>';
        }
        
        var html = '<div class="twf-agencias-card" data-id="' + agency.id + '">';
        html += '<div class="twf-agencias-card-image" style="background-image: url(' + featuredImage + ');"></div>';
        html += '<div class="twf-agencias-card-content">';
        html += '<div class="twf-agencias-card-category">' + category + '</div>';
        html += '<h3 class="twf-agencias-card-title">' + title + '</h3>';
        html += '<div class="twf-agencias-card-section">';
        html += '<div class="twf-agencias-card-section-title">Dirección</div>';
        html += '<div class="twf-agencias-card-address">' + address + '</div>';
        html += '</div>';
        html += '<div class="twf-agencias-card-section">';
        html += '<div class="twf-agencias-card-section-title">Número de Contacto</div>';
        html += '<div class="twf-agencias-card-contact">';
        html += '<div class="twf-agencias-card-contact-icon">';
        html += '<img src="' + twf_agencias_vars.plugin_url + 'public/images/customer-support.svg" alt="Contacto" />';
        html += '</div>';
        html += '<div class="twf-agencias-card-contact-info">';
        html += phone;
        if (anexo) {
            html += ' <span>Anexo ' + anexo + '</span>';
        }
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += servicesHtml;
        html += '<a href="https://maps.google.com/?q=' + address + '" target="_blank" class="twf-agencias-card-button">Cómo llegar</a>';
        html += '</div>';
        html += '</div>';
        
        return html;
    }

    /**
     * Crea la paginación
     */
    function createPagination(currentPage, totalPages, container) {
        if (totalPages <= 1) return;
        
        // Flecha anterior
        var prevDisabled = currentPage === 1 ? ' disabled' : '';
        var pagination = '<div class="twf-agencias-pagination-arrow prev' + prevDisabled + '"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>';
        
        // Determinar qué páginas mostrar
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, startPage + 4);
        
        // Ajustar startPage si endPage está en el límite
        if (endPage === totalPages) {
            startPage = Math.max(1, endPage - 4);
        }
        
        // Generar botones de página
        for (var i = startPage; i <= endPage; i++) {
            var activeClass = i === currentPage ? ' active' : '';
            pagination += '<div class="twf-agencias-pagination-item' + activeClass + '" data-page="' + i + '">' + i + '</div>';
        }
        
        // Flecha siguiente
        var nextDisabled = currentPage === totalPages ? ' disabled' : '';
        pagination += '<div class="twf-agencias-pagination-arrow next' + nextDisabled + '"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>';
        
        container.html(pagination);
    }

    /**
     * Dispara un evento personalizado cuando se filtran las agencias
     */
    function triggerFilteredEvent(filters) {
        $(document).trigger('twf_agencias_filtered', {
            filters: filters
        });
    }

    /**
     * Filtra agencias por ciudad
     */
    function filterAgenciesByCity(citySlug) {
        // Filtros
        var filters = {
            city: citySlug
        };

        triggerFilteredEvent(filters);
        
        // Hacer la solicitud AJAX
        $.ajax({
            url: twf_agencias_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'twf_agencias_search_agencies',
                nonce: twf_agencias_vars.nonce,
                filters: filters
            },
            success: function(response) {
                if (response.success) {
                    if (response.data && response.data.length > 0) {
                        displayAgencies(response.data);
                        
                        // Añadir un pequeño padding al bounds para mejorar visualización
                        if (map && markers.length > 0) {
                            setTimeout(function() {
                                var bounds = new google.maps.LatLngBounds();
                                for (var i = 0; i < markers.length; i++) {
                                    bounds.extend(markers[i].getPosition());
                                }
                                
                                map.fitBounds(bounds);
                                
                                // Si solo hay un marcador o están muy juntos, 
                                // ajusta el zoom para ver mejor el contexto
                                var listener = google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                                    if (map.getZoom() > 16) {
                                        map.setZoom(16);
                                    } else if (map.getZoom() < 11) {
                                        map.setZoom(11);  // Mínimo zoom para ciudades
                                    }
                                });
                            }, 100);
                        }
                    } else {
                        showNotification('No se encontraron agencias en esta ciudad.', 3000);
                    }
                }
            },
            error: function() {
                showNotification('Error al filtrar agencias.', 3000);
            }
        });
    }
    
    /**
     * Filtra agencias por distrito
     */
    function filterAgenciesByDistrict(districtSlug) {
        // Filtros
        var filters = {
            district: districtSlug
        };

        triggerFilteredEvent(filters);
        
        // Hacer la solicitud AJAX
        $.ajax({
            url: twf_agencias_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'twf_agencias_search_agencies',
                nonce: twf_agencias_vars.nonce,
                filters: filters
            },
            success: function(response) {
                if (response.success) {
                    if (response.data && response.data.length > 0) {
                        displayAgencies(response.data);
                        
                        // Ajustar el mapa para mostrar todos los pines
                        if (map && markers.length > 0) {
                            setTimeout(function() {
                                var bounds = new google.maps.LatLngBounds();
                                for (var i = 0; i < markers.length; i++) {
                                    bounds.extend(markers[i].getPosition());
                                }
                                
                                map.fitBounds(bounds);
                                
                                // Si solo hay un marcador o están muy juntos,
                                // ajusta el zoom para ver mejor el contexto
                                var listener = google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                                    if (map.getZoom() > 16) {
                                        map.setZoom(16);
                                    } else if (map.getZoom() < 13) {
                                        map.setZoom(13);  // Mínimo zoom para distritos
                                    }
                                });
                            }, 100);
                        }
                    } else {
                        showNotification('No se encontraron agencias en este distrito.', 3000);
                    }
                }
            },
            error: function() {
                showNotification('Error al filtrar agencias.', 3000);
            }
        });
    }
    
    /**
     * Realiza la búsqueda de agencias
     */
    function searchAgencies() {
        var searchTerm = $('#twf-agencias-search-input').val();
        
        if (!searchTerm) {
            // Si no hay término de búsqueda, mostrar todas
            displayAgencies(agencies);
            // Disparar evento de filtrado sin filtros
            triggerFilteredEvent({});
            return;
        }
        
        
        var filters = {
            search: searchTerm,
            city: $('#twf-agencias-city-select').val(),
            district: $('#twf-agencias-district-select').val()
        };
        
        // Disparar evento de filtrado
        triggerFilteredEvent(filters);

        // Hacer la solicitud AJAX
        $.ajax({
            url: twf_agencias_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'twf_agencias_search_agencies',
                nonce: twf_agencias_vars.nonce,
                filters: filters
            },
            success: function(response) {
                if (response.success) {
                    if (response.data && response.data.length > 0) {
                        displayAgencies(response.data);
                    } else {
                        showNotification('No se encontraron agencias que coincidan con la búsqueda.', 3000);
                    }
                }
            },
            error: function() {
                showNotification('Error al buscar agencias.', 3000);
            }
        });
    }

})(jQuery);