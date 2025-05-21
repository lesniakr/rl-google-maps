<?php
/*
 * Plugin Name: RL Google Maps
 * Plugin URI: https://rafallesniak.com/
 * Description: Add Google Maps to your website using a shortcode.
 * Version: 1.0.0
 * Author: Rafał Leśniak
 * Author URI: https://rafallesniak.com/
 */

// Admin menu and settings page
function rl_google_maps_register_settings() {
    register_setting('rl_google_maps_options', 'rl_google_maps_api_key');
    register_setting('rl_google_maps_options', 'rl_google_maps_marker');
    register_setting('rl_google_maps_options', 'rl_google_maps_lat');
    register_setting('rl_google_maps_options', 'rl_google_maps_lng');
    register_setting('rl_google_maps_options', 'rl_google_maps_zoom');
}
add_action('admin_init', 'rl_google_maps_register_settings');

function rl_google_maps_admin_menu() {
    add_options_page(
        'RL Google Maps Settings',
        'RL Google Maps',
        'manage_options',
        'rl-google-maps',
        'rl_google_maps_settings_page'
    );
}
add_action('admin_menu', 'rl_google_maps_admin_menu');

function rl_google_maps_settings_page() {
    ?>
    <div class="wrap">
        <h1>RL Google Maps – Global Defaults</h1>
        <form method="post" action="options.php">
            <?php settings_fields('rl_google_maps_options'); ?>
            <?php do_settings_sections('rl_google_maps_options'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Google Maps API Key</th>
                    <td>
                        <input type="text" name="rl_google_maps_api_key" value="<?php echo esc_attr(get_option('rl_google_maps_api_key', '')); ?>" size="40" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Default Marker URL</th>
                    <td>
                        <input type="text" name="rl_google_maps_marker" value="<?php echo esc_attr(get_option('rl_google_maps_marker', '')); ?>" size="40" />
                        <p class="description">Leave empty for plugin default.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Default Latitude</th>
                    <td>
                        <input type="text" name="rl_google_maps_lat" value="<?php echo esc_attr(get_option('rl_google_maps_lat', '52.231998990860056')); ?>" size="20" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Default Longitude</th>
                    <td>
                        <input type="text" name="rl_google_maps_lng" value="<?php echo esc_attr(get_option('rl_google_maps_lng', '21.00603791534297')); ?>" size="20" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Default Zoom</th>
                    <td>
                        <input type="number" name="rl_google_maps_zoom" value="<?php echo esc_attr(get_option('rl_google_maps_zoom', '14')); ?>" min="1" max="21" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Shortcode with global defaults support
function google_maps_shortcode($atts) {
    $plugin_url = plugin_dir_url(__FILE__);

    // Load global defaults from options
    $global_defaults = array(
        'api_key'      => get_option('rl_google_maps_api_key', ''),
        'lat'          => get_option('rl_google_maps_lat', '52.231998990860056'),
        'lng'          => get_option('rl_google_maps_lng', '21.00603791534297'),
        'zoom'         => get_option('rl_google_maps_zoom', '14'),
        'marker'       => get_option('rl_google_maps_marker', ''),
        'marker_width' => '48',
        'marker_height'=> '48',
        'phone'        => '+48 000 000 000',
        'address'      => 'plac Defilad 1, 00-901 Warszawa',
        'title'        => 'Company Name',
        'email'        => 'contact@domain.com',
    );

    // Merge shortcode attributes with global defaults
    $atts = shortcode_atts($global_defaults, $atts, 'rl_google_map');

    // If marker is empty (not set in shortcode or admin), use plugin's default SVG marker
    if (empty($atts['marker'])) {
        $atts['marker'] = $plugin_url . 'assets/img/default-marker.svg';
    }

    // Remove spaces and "-" from the phone number
    $cleaned_phone = str_replace(array(' ', '-'), '', $atts['phone']);

    // Generate a unique map ID
    $map_id = 'google-map-' . uniqid();

    // Check if API key is provided
    if (empty($atts['api_key'])) {
        return '<div style="color: red; text-align: center; padding: 20px 0;"><strong>Error:</strong> You must provide a Google Maps API key in the admin settings or using the <code>api_key</code> attribute in the shortcode.</div>';
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