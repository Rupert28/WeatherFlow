<?php
/*
 * Plugin Name:       WeatherFlow
 * Description:       View detailed hourly weather forecasts.
 * Version:           1.1.0
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

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'weatherflow-admin.php';
}

add_shortcode('WeatherFlow', 'weatherflow_shortcode');

function weatherflow_enqueue_styles()
{
    wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',);
    wp_enqueue_style('weatherflow-styles', plugin_dir_url(__FILE__) . 'assets/css/weatherflow.css');


    wp_enqueue_style('owl-carousel', plugin_dir_url(__FILE__) . 'assets/owl-carousel/owl.carousel.css');
    wp_enqueue_script('owl-carousel-js', plugin_dir_url(__FILE__) . 'assets/owl-carousel/owl.carousel.js', array('jquery'), null, true);


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

//Dynamic styling options configured by admin, injected as CSS variables here
function weatherflow_dynamic_styles() {
    $background_colour = get_option('weatherflow_background_colour');
    $background_text_colour = get_option('weatherflow_background_text_colour');
    $card_colour = get_option('weatherflow_card_colour');
    $card_text_colour = get_option('weatherflow_card_text_colour');
    echo '<style>
            :root { 
                --weatherflow_background_colour: ' . esc_attr(($background_colour) . ';
                --weatherflow_background_text_colour: ' . esc_attr($background_text_colour) . ';
                --weatherflow_card_colour: ' . esc_attr($card_colour) . ';
                --weatherflow_card_text_colour: ' . esc_attr($card_text_colour) . ';
            }
        </style>');
}
add_action('wp_head', 'weatherflow_dynamic_styles');

//Main function for constructing and displaying plugin widget
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

    $display_options = get_option("weatherflow_display_options");


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
            <p><strong>" . esc_html($hour_time) . "</strong></p>";

            if ($display_options['temp'] == "1") {
                $hourly_forecast .= '<p>'. esc_html($hour_temp) . '°C</p>';
            }
            if ($display_options['clouds'] == "1") {
                $hourly_forecast .= '<p>'. esc_html($hour_clouds) . '%</p>';
            }
            if ($display_options['desc'] == "1") {
                $hourly_forecast .= '<p>'. esc_html($hour_description) . '</p>';
            }

            $hourly_forecast .= "
        </div>
    </div>";
        }
    }

    if ($display_options['temp'] == 1) {
        $current_temp = "<p>" . $current_temp . "°C</p>";
    } else {
        $current_temp = "";
    }
    if ($display_options['clouds'] == 1) {
        $current_clouds = "<p>" . $current_clouds . "%</p>";
    } else {
        $current_clouds = "";
    }
    if ($display_options['desc'] == 1) {
        $current_description = "<p>" . $current_description . "</p>";
    } else {
        $current_description = "";
    }



    //Whitespace and newlines in normal HTML formatting messes up carousel display
    return "<div class='weatherflow-wrapper'><div class='weatherflow-weather'><strong class='weatherflow-heading'>" . esc_html($hour_limit) . " Hour Forecast</strong>" .
        ($location_name ? "<br><strong class='weatherflow-location'>📍 " . esc_html($location_name) . "</strong>" : "") .
        "<div class='weatherflow-flexbox'><div class='weatherflow-slide weatherflow-current-conditions'><div class='weatherflow-info'>" .
        "<img src='" .  esc_url($current_icon_url) . "' alt='Weather Icon' class='weatherflow-icon' /><p><strong>Now</strong></p>" .
        wp_kses_post($current_temp) .
        wp_kses_post($current_clouds) .
        wp_kses_post($current_description) .
        "</div>" .
        "</div><div class='owl-carousel owl-theme'>{$hourly_forecast}" .
        "</div></div>" .
        "</div></div>";
}