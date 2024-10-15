<?php
/*
 * Plugin Name:       WeatherFlow
 * Description:       View detailed hourly weather forecasts.
 * Version:           1.0.0
 * Requires at least: 4.0
 * Requires PHP:      5.6
 * Author:            Rupert Morgan
 * Author URI:        https://github.com/Rupert28
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_shortcode('WeatherFlow', 'weatherflow_shortcode');

function weatherflow_enqueue_styles()
{
    wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',);
    wp_enqueue_style('weatherflow-styles', plugin_dir_url(__FILE__) . 'assets/css/weatherflow.css');


    wp_enqueue_style('owl-carousel', plugin_dir_url(__FILE__) . 'assets/owl-carousel/assets/owl.carousel.min.css');
    wp_enqueue_style('owl-theme', plugin_dir_url(__FILE__) . 'assets/owl-carousel/assets/owl.theme.default.min.css');
    wp_enqueue_script('owl-carousel-js', plugin_dir_url(__FILE__) . 'assets/owl-carousel/owl.carousel.min.js', array('jquery'), null, true);


    wp_enqueue_script('weatherflow-carousel', plugins_url('assets/js/carousel-init.js', __FILE__), array('jquery', 'owl-carousel-js'), null, true);
}

add_action('wp_enqueue_scripts', 'weatherflow_enqueue_styles');


function weatherflow_shortcode($atts)
{

    $api_key = get_option('weatherflow_api_key');
    $latitude = get_option('weatherflow_latitude');
    $longitude = get_option('weatherflow_longitude');
    $location_name = get_option('weatherflow_location_name');
    $hour_limit = get_option('weatherflow_hour_limit');

    if (empty($api_key) || empty($latitude) || empty($longitude)) {
        if (current_user_can('manage_options')) {
            return "
            <div class='weatherflow-error'>
                <strong>WeatherFlow Error:</strong> Please configure WeatherFlow in the settings page.
                <br><em>This message is only visible to site admins and hidden from the public</em>
            </div>
            ";
        } else {
            return "";
        }
    }

    $weather_data = weatherflow_fetch_weather_data($api_key, $latitude, $longitude);
    if ($weather_data) {
        return weatherflow_format_weather_data($weather_data['data'], $location_name, $hour_limit);
    } else {
        return 'Unable to retrieve weather data.';
    }
}

function weatherflow_fetch_weather_data($api_key, $latitude, $longitude)
{
    $api_url = "https://api.openweathermap.org/data/3.0/onecall?lat={$latitude}&lon={$longitude}&appid={$api_key}&units=metric";
    $response = wp_remote_get($api_url);

    // Check if there's a WP error
    if (is_wp_error($response)) {
        return [
            'error' => true,
            'raw_response' => $response,
            'data' => null
        ];
    }

    $body = wp_remote_retrieve_body($response);
    $weather_data = json_decode($body, true);

    // Check for API errors in the response data
    if (isset($weather_data['cod']) && $weather_data['cod'] !== 200) {
        return [
            'error' => true,
            'raw_response' => $weather_data, // Return the raw response for debugging
            'data' => null
        ];
    }
    return [
        'error' => false,
        'raw_response' => null,
        'data' => $weather_data
    ];
}

function weatherflow_format_weather_data($weather_data, $location_name, $hour_limit)
{
    if (isset($weather_data['current'])) {
        $current_temp = round($weather_data['current']['temp']);
        $current_description = ucfirst($weather_data['current']['weather'][0]['description']);
        $current_clouds = $weather_data['current']['clouds'];
        $current_icon_id = $weather_data['current']['weather'][0]['icon'];
        $current_icon_url = plugins_url("assets/icons/{$current_icon_id}.png",  __FILE__);
        $hourly_forecast = '';
    } else {
        if (current_user_can('manage_options')) {
            $error_message = isset($weather_data['raw_response']['message']) ? $weather_data['raw_response']['message'] : 'Unknown error occurred.';
            return "
            <div class='weatherflow-error'>
                <strong>WeatherFlow Error: Unable to retrieve weather data. Please check your API settings.</strong>
                <br><em>This message is only visible to site admins and hidden from the public</em>
                <br><strong>Error response from API: </strong>{$error_message}
            </div>
            ";
        } else {
            return "";
        }
    }



    if (empty($hour_limit)) {
        $hour_limit = 12;
    }

    // Loop through hourly forecasts (show only the next 12 hours)
    for ($i = 0; $i < $hour_limit; $i++) {
        if (isset($weather_data['hourly'][$i])) { // Check if data exists
            $hour_time = wp_date('g A', $weather_data['hourly'][$i]['dt']);

            $hour_temp = round($weather_data['hourly'][$i]['temp']);
            $hour_description = ucfirst($weather_data['hourly'][$i]['weather'][0]['description']);
            $hour_clouds = $weather_data['hourly'][$i]['clouds'];
            $hour_icon_id = $weather_data['hourly'][$i]['weather'][0]['icon'];
            $hour_icon_url = plugins_url("assets/icons/{$hour_icon_id}.png", __FILE__);

            $hourly_forecast .= "
    <div class='weatherflow-slide'>
        <div class='weatherflow-info'>
            <img src='" . esc_url($hour_icon_url) . "' alt='Weather Icon' class='weatherflow-icon' />
            <p><strong>" . esc_html($hour_time) . "</strong></p>
            <p>" . esc_html($hour_temp) . "°C</p>
            <p>" . esc_html($hour_clouds) . "% clouds</p>
            <p>" . esc_html($hour_description) . "</p>
        </div>
    </div>";
        }
    }



    //Whitespace and newlines in normal HTML formatting messes up carousel display
    return "<div class='weatherflow-wrapper'><div class='weatherflow'><p><strong class='weatherflow-heading'>" . esc_html($hour_limit) . " Hour Forecast</strong></p>" .
        ($location_name ? "<p><strong class='weatherflow-location'>📍 " . esc_html($location_name) . "</strong></p>" : "") .
        "<div class='weatherflow-flexbox'><div class='weatherflow-slide weatherflow-current-conditions'><div class='weatherflow-info'>" .
        "<img src='" .  esc_url($current_icon_url) . "' alt='Weather Icon' class='weatherflow-icon' /><p><strong>Now</strong></p><p>" . esc_html($current_temp) . "°C</p><p>" . esc_html($current_clouds) . "% clouds</p><p>" . esc_html($current_description) . "</p>" .
        "</div>" .
        "</div><div class='owl-carousel owl-theme'>{$hourly_forecast}" .
        "</div></div>" .
        "</div></div>";
}


//SETTINGS

add_action('admin_menu', 'weatherflow_add_admin_menu');

function weatherflow_add_admin_menu()
{
    add_menu_page(
        'WeatherFlow Settings',
        'WeatherFlow',
        'manage_options',
        'weatherflow-settings',
        'weatherflow_settings_page'
    );
}

function weatherflow_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['weatherflow_nonce']) && check_admin_referer('weatherflow_save_settings', 'weatherflow_nonce')) {

        // Sanitize and update options
        if (isset($_POST['weatherflow_api_key'])) {
            update_option('weatherflow_api_key', sanitize_text_field($_POST['weatherflow_api_key']));
        }

        if (isset($_POST['weatherflow_latitude'])) {
            update_option('weatherflow_latitude', sanitize_text_field($_POST['weatherflow_latitude']));
        }

        if (isset($_POST['weatherflow_longitude'])) {
            update_option('weatherflow_longitude', sanitize_text_field($_POST['weatherflow_longitude']));
        }

        if (isset($_POST['weatherflow_location_name'])) {
            update_option('weatherflow_location_name', sanitize_text_field($_POST['weatherflow_location_name']));
        }

        if (isset($_POST['weatherflow_hour_limit'])) {
            update_option('weatherflow_hour_limit', sanitize_text_field($_POST['weatherflow_hour_limit']));
        }

        echo '<div class="updated"><p>Settings saved successfully!</p></div>';
    }

    $api_key = get_option('weatherflow_api_key', '');
    $latitude = get_option('weatherflow_latitude', '');
    $longitude = get_option('weatherflow_longitude', '');
    $location_name = get_option('weatherflow_location_name', '');
    $hour_limit = get_option('weatherflow_hour_limit', 12);

?>
    <div class="weatherflow-admin-wrap">
        <h1>WeatherFlow Settings</h1>
        <div class="weatherflow-admin-flexbox">
            <div class="weatherflow-admin-form">
                <form method="POST">
                    <?php wp_nonce_field('weatherflow_save_settings', 'weatherflow_nonce'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="weatherflow_api_key">API Key</label>
                            </th>
                            <td>
                                <input type="text" id="weatherflow_api_key" name="weatherflow_api_key"
                                    value="<?php echo esc_attr($api_key); ?>" required />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="weatherflow_latitude">Latitude</label>
                            </th>
                            <td>
                                <input type="text" id="weatherflow_latitude" name="weatherflow_latitude"
                                    value="<?php echo esc_attr($latitude); ?>" required />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="weatherflow_longitude">Longitude</label>
                            </th>
                            <td>
                                <input type="text" id="weatherflow_longitude" name="weatherflow_longitude"
                                    value="<?php echo esc_attr($longitude); ?>" required />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="weatherflow_location_name">Location Display Name</label>
                            </th>
                            <td>
                                <input type="text" id="weatherflow_location_name" name="weatherflow_location_name"
                                    value="<?php echo esc_attr($location_name); ?>" />
                                <br>
                                <em>Note: this doesn't affect the location for the weather, it just changes the display on the widget. Leave blank for no location display.</em>
                            </td>
                        </tr>
                        <th scope="row">
                            <label for="weatherflow_hour_limit">Forecast Length</label>
                        </th>
                        <td>
                            <input type="number" id="weatherflow_hour_limit" name="weatherflow_hour_limit"
                                value="<?php echo esc_attr($hour_limit); ?>" min="1" max="48" />
                            <br>
                            <em>48 hours maximum.</em>
                        </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Settings'); ?>
            </div>

            </form>
            <div class="weatherflow-admin-help">
                <p class="weatherflow-info"><strong>Thanks for using WeatherFlow! Here's some help getting started:</strong><br>
                <ol>
                    <li><strong>Get an API Key.</strong> This plugin uses OpenWeatherMap's One Call API 3.0. It is a paid service, however the first 1000 calls a day are free. You can also set a limit so that you never exceed the free number of calls and therefore aren't charged. Go to <a href="https://openweathermap.org/api/one-call-3">https://openweathermap.org/api/one-call-3</a> and follow the instructions under 'How to start'. Paste your API Key in here once you've got it.</li>
                    <li><strong>Add your location.</strong> Future updates will likely bring a location search box, but for now please use a service like <a href="https://www.latlong.net/">https://www.latlong.net/</a> for now.</li>
                    <li><strong>Add a display name.</strong> This will be shown to visitors on your website.</li>
                    <li><strong>Choose the forecast length.</strong> OpenWeatherMap provides hourly forecasts between 1 and 48 hours.</li>
                    <li><strong>Add the widget to your site.</strong> Insert a <em>Shortcode</em> block where you want weatherflow to be, and enter <code>WeatherFlow</code>.</li>
                </ol>
                <strong>Helpful info:</strong>
                <ul>
                    <li>
                        If you're getting errors with your API key just after signing up with OpenWeatherMap, you may need to wait a while for your key to be activated on their end.
                    </li>
                    <li>A list of common API errors can be found <a href="https://openweathermap.org/api/one-call-3#popularerrors">here</a>.</li>
                    <li>Make sure your WordPress site timezone is correctly set in <code>Settings>General</code>, otherwise times may display incorrectly.</li>
                    <li>Future updates may introduce caching of weather data, reducing API call numbers. For now, you can set a limit in OpenWeatherMap's 'Billing' screen.</li>
                </ul>
                </p>
            </div>
        </div>
    </div>
<?php
}

// ENQUEUE CSS FOR ADMIN
function weatherflow_enqueue_admin_styles()
{
    wp_enqueue_style('weatherflow-admin-styles', plugin_dir_url(__FILE__) . 'assets/css/weatherflow-admin.css');
}

add_action('admin_enqueue_scripts', 'weatherflow_enqueue_admin_styles');


?>