(function($) {
    'use strict';

    // Variables globales
    var map, markers = [], infoWindow, geocoder, autocomplete;
    var agencies = []; // Guardar todas las agencias
    
    $(document).ready(function() {
        // Inicializar solo si existe el contenedor del mapa
        if ($('#twf-agencias-map').length) {
            // Cargar el script de Google Maps si hay API key
            if (twf_agencias_vars.api_key) {
                loadGoogleMapsScript();
            } else {
                displayApiKeyError();
            }
            
            // Inicializar eventos
            initEvents();
        }
    });
    
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
            zoom: defaultZoom,
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
                console.log("Respuesta recibida:", response);
                if (response.success && response.data) {
                    // Guardar todas las agencias para usar en autocompletado
                    agencies = response.data;
                    displayAgencies(response.data);
                } else {
                    console.error("Error en la respuesta:", response);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX:", status, error);
                alert('Error al cargar las agencias. Por favor, intenta nuevamente.');
            }
        });
    }
    
    /**
     * Muestra las agencias en el mapa
     */
    function displayAgencies(agencies) {
        console.log("Mostrando agencias:", agencies);
        
        // Limpiar marcadores existentes
        clearMarkers();
        
        // Si no hay agencias, mostrar mensaje
        if (!agencies || agencies.length === 0) {
            $('#twf-agencias-results').html('<p>No se encontraron agencias con los filtros seleccionados.</p>');
            return;
        }
        
        // Obtener los límites del mapa para centrar
        var bounds = new google.maps.LatLngBounds();
        var pendingGeocode = 0;
        
        // Crear marcadores para cada agencia
        $.each(agencies, function(index, agency) {
            console.log("Procesando agencia:", agency);
            
            // Obtener latitud y longitud
            var lat, lng;
            
            if (agency.meta_datos && agency.meta_datos.latitud && agency.meta_datos.longitud) {
                lat = parseFloat(agency.meta_datos.latitud);
                lng = parseFloat(agency.meta_datos.longitud);
            } else if (agency.latitud && agency.longitud) {
                lat = parseFloat(agency.latitud);
                lng = parseFloat(agency.longitud);
            }
            
            console.log("Coordenadas:", lat, lng);
            
            // Si tenemos coordenadas válidas, crear marcador
            if (!isNaN(lat) && !isNaN(lng)) {
                createMarker(agency, lat, lng, bounds);
            } 
            // Si no tenemos coordenadas, intentar geocodificar la dirección
            else if (agency.direccion) {
                pendingGeocode++;
                geocoder.geocode({'address': agency.direccion}, function(results, status) {
                    pendingGeocode--;
                    
                    if (status === google.maps.GeocoderStatus.OK) {
                        var position = results[0].geometry.location;
                        createMarker(agency, position.lat(), position.lng(), bounds);
                        
                        // Si no quedan geocodificaciones pendientes, ajustar el mapa
                        if (pendingGeocode === 0 && markers.length > 0) {
                            map.fitBounds(bounds);
                        }
                    } else {
                        console.error("Error geocodificando:", status);
                    }
                });
            } else if (agency.meta_datos && agency.meta_datos.direccion) {
                pendingGeocode++;
                geocoder.geocode({'address': agency.meta_datos.direccion}, function(results, status) {
                    pendingGeocode--;
                    
                    if (status === google.maps.GeocoderStatus.OK) {
                        var position = results[0].geometry.location;
                        createMarker(agency, position.lat(), position.lng(), bounds);
                        
                        // Si no quedan geocodificaciones pendientes, ajustar el mapa
                        if (pendingGeocode === 0 && markers.length > 0) {
                            map.fitBounds(bounds);
                        }
                    } else {
                        console.error("Error geocodificando:", status);
                    }
                });
            } else {
                console.warn("No hay coordenadas ni dirección para geolocalizar:", agency.title);
            }
        });
        
        // Si no hay geocodificaciones pendientes y hay marcadores, ajustar el mapa
        if (pendingGeocode === 0 && markers.length > 0) {
            map.fitBounds(bounds);
        } else if (markers.length === 0 && pendingGeocode === 0) {
            $('#twf-agencias-results').html('<p>No se pudieron ubicar las agencias en el mapa. Por favor, añade coordenadas o direcciones.</p>');
        }
    }
    
    /**
     * Crea un marcador para una agencia
     */
    function createMarker(agency, lat, lng, bounds) {
        var position = new google.maps.LatLng(lat, lng);
        
        // Obtener título
        var title = agency.title ? (agency.title.rendered || agency.title) : "Agencia";
        
        // Opciones del marcador
        var markerOptions = {
            position: position,
            map: map,
            title: title
        };
        
        // Si hay icono personalizado, usarlo
        var iconUrl = '';
        if (agency.meta_datos && agency.meta_datos.custom_icon) {
            iconUrl = agency.meta_datos.custom_icon;
        } else if (agency.icon) {
            iconUrl = agency.icon;
        }
        
        if (iconUrl) {
            markerOptions.icon = {
                url: iconUrl,
                scaledSize: new google.maps.Size(40, 40)
            };
        }
        
        // Crear el marcador
        var marker = new google.maps.Marker(markerOptions);
        
        // Añadir evento de clic al marcador
        marker.addListener('click', function() {
            infoWindow.setContent(agency.infoWindow);
            infoWindow.open(map, marker);
        });
        
        // Añadir el marcador al array
        markers.push(marker);
        
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
     * Filtra agencias por ciudad
     */
    function filterAgenciesByCity(citySlug) {
        // Filtros
        var filters = {
            city: citySlug
        };
        
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
                    displayAgencies(response.data);
                }
            },
            error: function() {
                alert('Error al filtrar agencias. Por favor, intenta nuevamente.');
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
                    displayAgencies(response.data);
                }
            },
            error: function() {
                alert('Error al filtrar agencias. Por favor, intenta nuevamente.');
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
            return;
        }
        
        var filters = {
            search: searchTerm,
            city: $('#twf-agencias-city-select').val(),
            district: $('#twf-agencias-district-select').val()
        };
        
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
                    displayAgencies(response.data);
                }
            },
            error: function() {
                alert('Error al buscar agencias. Por favor, intenta nuevamente.');
            }
        });
    }

})(jQuery);