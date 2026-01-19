<?php
/*
 * Plugin Name: RL Google Maps
 * Plugin URI: https://rafallesniak.com/
 * Description: Add Google Maps to your website using a shortcode.
 * Version: 1.0.0
 * Author: Rafał Leśniak
 * Author URI: https://rafallesniak.com/
 * Text Domain: rl-google-maps
 * Domain Path: /languages
 */

// Define default values as constants
define('RL_GM_DEFAULT_LAT', '52.231998990860056');
define('RL_GM_DEFAULT_LNG', '21.00603791534297');
define('RL_GM_DEFAULT_ZOOM', '14');
define('RL_GM_DEFAULT_HEIGHT', '400');
define('RL_GM_DEFAULT_MOBILE_HEIGHT', '300');

// Admin menu and settings page
function rl_google_maps_register_settings() {
    register_setting('rl_google_maps_options', 'rl_google_maps_api_key', array(
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('rl_google_maps_options', 'rl_google_maps_marker', array(
        'sanitize_callback' => 'esc_url_raw'
    ));
    register_setting('rl_google_maps_options', 'rl_google_maps_lat', array(
        'sanitize_callback' => 'floatval'
    ));
    register_setting('rl_google_maps_options', 'rl_google_maps_lng', array(
        'sanitize_callback' => 'floatval'
    ));
    register_setting('rl_google_maps_options', 'rl_google_maps_zoom', array(
        'sanitize_callback' => 'intval'
    ));
    register_setting('rl_google_maps_options', 'rl_google_maps_height', array(
        'sanitize_callback' => 'intval'
    ));
    register_setting('rl_google_maps_options', 'rl_google_maps_mobile_height', array(
        'sanitize_callback' => 'intval'
    ));
    register_setting('rl_google_maps_options', 'rl_google_maps_styles', array(
        'sanitize_callback' => 'rl_google_maps_sanitize_json'
    ));
}
add_action('admin_init', 'rl_google_maps_register_settings');

function rl_google_maps_admin_menu() {
    add_options_page(
        esc_html__('RL Google Maps Settings', 'rl-google-maps'),
        esc_html__('RL Google Maps', 'rl-google-maps'),
        'manage_options',
        'rl-google-maps',
        'rl_google_maps_settings_page'
    );
}
add_action('admin_menu', 'rl_google_maps_admin_menu');

// Sanitize JSON input
function rl_google_maps_sanitize_json($input) {
    if (empty($input)) {
        return '';
    }
    
    // Decode and then re-encode to ensure valid JSON
    $decoded = json_decode($input);
    if (json_last_error() !== JSON_ERROR_NONE) {
        add_settings_error(
            'rl_google_maps_options',
            'json_error',
            esc_html__('Invalid JSON format for Map Styles. Default styles will be used.', 'rl-google-maps'),
            'error'
        );
        return '';
    }
    
    return json_encode($decoded);
}

function rl_google_maps_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('RL Google Maps – Global Defaults', 'rl-google-maps'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('rl_google_maps_options'); ?>
            <?php do_settings_sections('rl_google_maps_options'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Google Maps API Key', 'rl-google-maps'); ?></th>
                    <td>
                        <input type="text" name="rl_google_maps_api_key" value="<?php echo esc_attr(get_option('rl_google_maps_api_key', '')); ?>" size="40" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Default Marker URL', 'rl-google-maps'); ?></th>
                    <td>
                        <input type="text" name="rl_google_maps_marker" value="<?php echo esc_attr(get_option('rl_google_maps_marker', '')); ?>" size="40" />
                        <p class="description"><?php esc_html_e('Leave empty for plugin default.', 'rl-google-maps'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Default Latitude', 'rl-google-maps'); ?></th>
                    <td>
                        <input type="text" name="rl_google_maps_lat" value="<?php echo esc_attr(get_option('rl_google_maps_lat', RL_GM_DEFAULT_LAT)); ?>" size="20" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Default Longitude', 'rl-google-maps'); ?></th>
                    <td>
                        <input type="text" name="rl_google_maps_lng" value="<?php echo esc_attr(get_option('rl_google_maps_lng', RL_GM_DEFAULT_LNG)); ?>" size="20" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Default Zoom', 'rl-google-maps'); ?></th>
                    <td>
                        <input type="number" name="rl_google_maps_zoom" value="<?php echo esc_attr(get_option('rl_google_maps_zoom', RL_GM_DEFAULT_ZOOM)); ?>" min="1" max="21" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Default Map Height', 'rl-google-maps'); ?></th>
                    <td>
                        <input type="number" name="rl_google_maps_height" value="<?php echo esc_attr(get_option('rl_google_maps_height', RL_GM_DEFAULT_HEIGHT)); ?>" min="100" max="1000" />
                        <p class="description"><?php esc_html_e('Height in pixels (px) for desktop.', 'rl-google-maps'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Mobile Map Height', 'rl-google-maps'); ?></th>
                    <td>
                        <input type="number" name="rl_google_maps_mobile_height" value="<?php echo esc_attr(get_option('rl_google_maps_mobile_height', RL_GM_DEFAULT_MOBILE_HEIGHT)); ?>" min="100" max="1000" />
                        <p class="description"><?php esc_html_e('Height in pixels (px) for mobile devices (screen width less than 768px).', 'rl-google-maps'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Custom Map Styles', 'rl-google-maps'); ?></th>
                    <td>
                        <textarea name="rl_google_maps_styles" rows="10" cols="80" class="large-text code"><?php echo esc_textarea(get_option('rl_google_maps_styles', '')); ?></textarea>
                        <p class="description">
                            <?php esc_html_e('Paste your custom map styles in JSON format. Leave empty to use the default style.', 'rl-google-maps'); ?>
                            <a href="https://mapstyle.withgoogle.com/" target="_blank"><?php esc_html_e('Get styles from Google Map Style Wizard', 'rl-google-maps'); ?></a>
                        </p>
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
        'api_key'       => get_option('rl_google_maps_api_key', ''),
        'lat'           => get_option('rl_google_maps_lat', RL_GM_DEFAULT_LAT),
        'lng'           => get_option('rl_google_maps_lng', RL_GM_DEFAULT_LNG),
        'zoom'          => get_option('rl_google_maps_zoom', RL_GM_DEFAULT_ZOOM),
        'marker'        => get_option('rl_google_maps_marker', ''),
        'marker_width'  => '48',
        'marker_height' => '48',
        'height'        => get_option('rl_google_maps_height', RL_GM_DEFAULT_HEIGHT),
        'mobile_height' => get_option('rl_google_maps_mobile_height', RL_GM_DEFAULT_MOBILE_HEIGHT),
        'phone'         => '',
        'address'       => '',
        'title'         => '',
        'email'         => '',
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
        return '<div style="color: red; text-align: center; padding: 20px 0;"><strong>' . 
            esc_html__('Error:', 'rl-google-maps') . '</strong> ' . 
            esc_html__('You must provide a Google Maps API key in the admin settings or using the api_key attribute in the shortcode.', 'rl-google-maps') . 
            '</div>';
    }

    // Register the Google Maps API script with dynamic key
    $api_url = 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($atts['api_key']) . '&callback=initGoogleMaps';

    // Get custom map styles
    $custom_styles = get_option('rl_google_maps_styles', '');
    
    // Add custom CSS for the InfoWindow
    $custom_css = "
    <style>
        /* Custom InfoWindow styling */
        .rl-gm-info-window {
            font-family: 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            max-width: 300px;
        }
        .rl-gm-info-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-top: 0;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .rl-gm-info-content {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }
        .rl-gm-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .rl-gm-info-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 24px;
            height: 24px;
            margin-right: 10px;
            color: #555;
        }
        .rl-gm-info-text {
            flex: 1;
        }
        .rl-gm-info-link {
            text-decoration: none;
            color: inherit;
            transition: color 0.2s ease;
        }
        .rl-gm-info-link:hover {
            color: inherit;
            text-decoration: underline;
        }
        
        /* Override Google's default InfoWindow styles */
        .gm-style .gm-style-iw-c {
            padding: 12px !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
        }
        .gm-style .gm-style-iw-d {
            overflow: hidden !important;
            padding: 0 !important;
        }
        .gm-style .gm-style-iw-t::after {
            background: linear-gradient(45deg, rgba(255,255,255,1) 50%, rgba(255,255,255,0) 51%, rgba(255,255,255,0) 100%) !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
        }
        /* InfoWindow header with close button */
        .gm-style .gm-style-iw-chr {
            display: flex !important;
            justify-content: flex-end !important;
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
            width: auto !important;
            height: auto !important;
        }
        .gm-style .gm-style-iw-ch {
            display: none !important;
        }
        /* Close button styling */
        .gm-style .gm-style-iw-c button.gm-ui-hover-effect {
            width: 32px !important;
            height: 32px !important;
            opacity: 0.6 !important;
        }
        .gm-style .gm-style-iw-c button.gm-ui-hover-effect span {
            margin: 4px !important;
        }
        .gm-style .gm-style-iw-c button.gm-ui-hover-effect:hover {
            opacity: 1 !important;
        }
    </style>
    ";

    // Initialize the map and data in JavaScript
    $script = "
    <script type='text/javascript'>
    window.initGoogleMaps = function() {
        const mapOptions = {
            center: { lat: {$atts['lat']}, lng: {$atts['lng']} },
            zoom: {$atts['zoom']},
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: true
        };
        
        " . (!empty($custom_styles) ? "mapOptions.styles = $custom_styles;" : "") . "
        
        const map = new google.maps.Map(document.getElementById('$map_id'), mapOptions);

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
        " . (!empty($atts['title']) || !empty($atts['phone']) || !empty($atts['email']) || !empty($atts['address']) ? "
        const infoWindowContent = `
            <div class=\"rl-gm-info-window\">
                " . (!empty($atts['title']) ? "<h4 class=\"rl-gm-info-title\">{$atts['title']}</h4>" : "") . "
                <div class=\"rl-gm-info-content\">
                    " . (!empty($atts['phone']) ? "
                    <div class=\"rl-gm-info-item\">
                        <div class=\"rl-gm-info-icon\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2\"/></svg>
                        </div>
                        <div class=\"rl-gm-info-text\">
                            <a href=\"tel:{$cleaned_phone}\" class=\"rl-gm-info-link\">{$atts['phone']}</a>
                        </div>
                    </div>
                    " : "") . "
                    " . (!empty($atts['email']) ? "
                    <div class=\"rl-gm-info-item\">
                        <div class=\"rl-gm-info-icon\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10\"/><path d=\"M3 7l9 6l9 -6\"/></svg>
                        </div>
                        <div class=\"rl-gm-info-text\">
                            <a href=\"mailto:{$atts['email']}\" class=\"rl-gm-info-link\">{$atts['email']}</a>
                        </div>
                    </div>
                    " : "") . "
                    " . (!empty($atts['address']) ? "
                    <div class=\"rl-gm-info-item\">
                        <div class=\"rl-gm-info-icon\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0\"/><path d=\"M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z\"/></svg>
                        </div>
                        <div class=\"rl-gm-info-text\">
                            {$atts['address']}
                        </div>
                    </div>
                    " : "") . "
                </div>
            </div>
        `;

        const infoWindow = new google.maps.InfoWindow({
            content: infoWindowContent,
            maxWidth: 320,
            pixelOffset: new google.maps.Size(0, -5)
        });

        // Open the InfoWindow when the marker is clicked
        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });
        " : "") . "
    };
    </script>
    <script src=\"$api_url\" async defer></script>
    ";

    // Add responsive height styling
    $responsive_style = "
    <style>
        #$map_id {
            width: 100%;
            height: " . esc_attr($atts['height']) . "px;
            border-radius: 12px;
        }
        
        @media only screen and (max-width: 767px) {
            #$map_id {
                height: " . esc_attr($atts['mobile_height']) . "px;
            }
        }
    </style>
    ";

    // Return the HTML of the map
    return $custom_css . $responsive_style . '<div id="' . $map_id . '"></div>' . $script;
}

// Load plugin text domain for translations
function rl_google_maps_load_textdomain() {
    load_plugin_textdomain('rl-google-maps', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'rl_google_maps_load_textdomain');

add_shortcode('rl_google_map', 'google_maps_shortcode');