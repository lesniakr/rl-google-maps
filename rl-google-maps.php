<?php
/*
 * Plugin Name: RL Google Maps
 * Plugin URI: https://rafallesniak.com/
 * Description: Add Google Maps to your website using a shortcode.
 * Version: 1.0.0
 * Author: Rafał Leśniak
 * Author URI: https://rafallesniak.com/
 */

function google_maps_shortcode($atts) {
    $plugin_url = plugin_dir_url(__FILE__);
    // Default values
    $atts = shortcode_atts(
        array(
            'api_key' => '',
            'lat' => '52.231998990860056',
            'lng' => '21.00603791534297',
            'zoom' => '14',
            'marker' => $plugin_url . 'assets/img/default-marker.svg',
            'marker_width' => '48',
            'marker_height' => '48',
            'phone' => '+48 000 000 000',
            'address' => 'plac Defilad 1, 00-901 Warszawa',
            'title' => 'Company Name',
            'email' => 'contact@domain.com',
        ),
        $atts,
        'rl_google_map'
    );

    // Remove spaces and "-" from the phone number
    $cleaned_phone = str_replace(array(' ', '-'), '', $atts['phone']);

    // Generate a unique map ID
    $map_id = 'google-map-' . uniqid();

    // Check if API key is provided
    if (empty($atts['api_key'])) {
        return '<div style="color: red; text-align: center; padding: 20px 0;"><strong>Error:</strong> You must provide a Google Maps API key using the <code>api_key</code> attribute in the shortcode.</div>';
    }

    // Register the Google Maps API script with dynamic key
    $api_url = 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($atts['api_key']) . '&callback=initGoogleMaps';

    // Font Awesome CDN
    $fa_script = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';

    // Initialize the map and data in JavaScript
    $script = "
    <script type='text/javascript'>
    window.initGoogleMaps = function() {
        const map = new google.maps.Map(document.getElementById('$map_id'), {
            center: { lat: {$atts['lat']}, lng: {$atts['lng']} },
            zoom: {$atts['zoom']},
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: true,
            styles: [
                {
                    'featureType': 'water',
                    'elementType': 'geometry',
                    'stylers': [
                        { 'color': '#e9e9e9' },
                        { 'lightness': 17 }
                    ]
                },
                {
                    'featureType': 'landscape',
                    'elementType': 'geometry',
                    'stylers': [
                        { 'color': '#f5f5f5' },
                        { 'lightness': 20 }
                    ]
                },
                {
                    'featureType': 'road.highway',
                    'elementType': 'geometry.fill',
                    'stylers': [
                        { 'color': '#ffffff' },
                        { 'lightness': 17 }
                    ]
                },
                {
                    'featureType': 'road.highway',
                    'elementType': 'geometry.stroke',
                    'stylers': [
                        { 'color': '#ffffff' },
                        { 'lightness': 29 },
                        { 'weight': 0.2 }
                    ]
                },
                {
                    'featureType': 'road.arterial',
                    'elementType': 'geometry',
                    'stylers': [
                        { 'color': '#ffffff' },
                        { 'lightness': 18 }
                    ]
                },
                {
                    'featureType': 'road.local',
                    'elementType': 'geometry',
                    'stylers': [
                        { 'color': '#ffffff' },
                        { 'lightness': 16 }
                    ]
                },
                {
                    'featureType': 'poi',
                    'elementType': 'geometry',
                    'stylers': [
                        { 'color': '#f5f5f5' },
                        { 'lightness': 21 }
                    ]
                },
                {
                    'featureType': 'poi.park',
                    'elementType': 'geometry',
                    'stylers': [
                        { 'color': '#dedede' },
                        { 'lightness': 21 }
                    ]
                },
                {
                    'elementType': 'labels.text.stroke',
                    'stylers': [
                        { 'visibility': 'on' },
                        { 'color': '#ffffff' },
                        { 'lightness': 16 }
                    ]
                },
                {
                    'elementType': 'labels.text.fill',
                    'stylers': [
                        { 'saturation': 36 },
                        { 'color': '#333333' },
                        { 'lightness': 40 }
                    ]
                },
                {
                    'elementType': 'labels.icon',
                    'stylers': [
                        { 'visibility': 'off' }
                    ]
                },
                {
                    'featureType': 'transit',
                    'elementType': 'geometry',
                    'stylers': [
                        { 'color': '#f2f2f2' },
                        { 'lightness': 19 }
                    ]
                },
                {
                    'featureType': 'administrative',
                    'elementType': 'geometry.fill',
                    'stylers': [
                        { 'color': '#fefefe' },
                        { 'lightness': 20 }
                    ]
                },
                {
                    'featureType': 'administrative',
                    'elementType': 'geometry.stroke',
                    'stylers': [
                        { 'color': '#fefefe' },
                        { 'lightness': 17 },
                        { 'weight': 1.2 }
                    ]
                }
            ]
        });

        const marker = new google.maps.Marker({
            position: { lat: {$atts['lat']}, lng: {$atts['lng']} },
            map: map,
            icon: {
                url: '{$atts['marker']}',
                scaledSize: new google.maps.Size({$atts['marker_width']}, {$atts['marker_height']})
            }
        });

        marker.setAnimation(google.maps.Animation.BOUNCE);

        setTimeout(function() {
            marker.setAnimation(null); // Stops the animation after 3 seconds
        }, 3000);

        const infoWindowContent = `
            <div>
                <h4>{$atts['title']}</h4>
                <p style='margin-bottom: 4px;'>
                    <i class='fa-solid fa-phone' style='margin-right:6px;'></i>
                    <a href='tel:{$cleaned_phone}'>{$atts['phone']}</a>
                </p>
                <p style='margin-bottom: 4px;'>
                    <i class='fa-solid fa-envelope' style='margin-right:6px;'></i>
                    <a href='mailto:{$atts['email']}'>{$atts['email']}</a>
                </p>
                <p style='margin-bottom: 4px;'>
                    <i class='fa-solid fa-location-dot' style='margin-right:6px;'></i>
                    {$atts['address']}
                </p>
            </div>
        `;

        const infoWindow = new google.maps.InfoWindow({
            content: infoWindowContent
        });

        // Open the InfoWindow when the marker is clicked
        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });
    };
    </script>
    <script src=\"$api_url\" async defer></script>
    ";

    // Return the HTML of the map
    return $fa_script . '<div id="' . $map_id . '" style="width: 100%; height: 400px;"></div>' . $script;
}

add_shortcode('rl_google_map', 'google_maps_shortcode');
