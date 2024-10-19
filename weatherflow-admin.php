<?php
//SETTINGS

add_action('admin_menu', 'weatherflow_add_admin_menu');

function weatherflow_add_admin_menu()
{
    add_menu_page(
        'WeatherFlow Settings',
        'WeatherFlow',
        'manage_options',
        'weatherflow-settings',
        'weatherflow_settings_page',
        //base64 encoded SVG for admin menu icon
        'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDk2IDk2IiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiPgoJPHRpdGxlPndlYXRoZXItaWNvbnMtMjAtc3ZncmVwby1jb20tc3ZnPC90aXRsZT4KCTxwYXRoIGZpbGw9IiMwMDAwMDAiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0ibTQ0LjggODkuOXY1LjFjMCAxLjQtMS4yIDIuNi0yLjYgMi42LTEuNCAwLTIuNS0xLjItMi41LTIuNnYtNS4xYzAtMS41IDEuMS0yLjYgMi41LTIuNiAxLjQgMCAyLjYgMS4xIDIuNiAyLjZ6bS0xMC44LTc2LjVjLTEtMS0xLTIuNiAwLTMuNiAxLTEgMi43LTEgMy43IDBsMy42IDMuNmMxIDEgMSAyLjYgMCAzLjYtMC41IDAuNS0xLjIgMC44LTEuOCAwLjgtMC43IDAtMS4zLTAuMy0xLjgtMC44em0yMS01Ljd2LTUuMWMwLTEuNCAxLjEtMi42IDIuNi0yLjYgMS40IDAgMi41IDEuMiAyLjUgMi42djUuMWMwIDEuNC0xLjEgMi42LTIuNSAyLjYtMS41IDAtMi42LTEuMi0yLjYtMi42em0xOC45IDkuNGMtMS0xLTEtMi43IDAtMy43bDMuNi0zLjZjMS0xIDIuNy0xIDMuNyAwIDEgMSAxIDIuNiAwIDMuNmwtMy43IDMuN2MtMC41IDAuNS0xLjEgMC43LTEuOCAwLjctMC42IDAtMS4zLTAuMi0xLjgtMC43em0tNDAuNyA2NS4xdjUuMWMwIDEuNC0xLjIgMi42LTIuNiAyLjYtMS40IDAtMi42LTEuMi0yLjYtMi42di01LjFjMC0xLjQgMS4yLTIuNiAyLjYtMi42IDEuNCAwIDIuNiAxLjIgMi42IDIuNnptNTcuNy00OC44YzAgMS40LTEuMSAyLjYtMi41IDIuNmgtNS4yYy0xLjQgMC0yLjUtMS4yLTIuNS0yLjYgMC0xLjQgMS4xLTIuNiAyLjUtMi42aDUuMmMxLjQgMCAyLjUgMS4yIDIuNSAyLjZ6bS00Ni4yIDQxLjF2NS4xYzAgMS40LTEuMSAyLjYtMi41IDIuNi0xLjUgMC0yLjYtMS4yLTIuNi0yLjZ2LTUuMWMwLTEuNCAxLjEtMi42IDIuNi0yLjYgMS40IDAgMi41IDEuMiAyLjUgMi42em0zNC43LTIxLjhjMCA3LjctNi40IDE0LjEtMTQuMSAxNC4xaC05djUuMWMwIDEuNC0xLjIgMi42LTIuNiAyLjYtMS40IDAtMi42LTEuMi0yLjYtMi42di01LjFoLTE3Ljl2NS4xYzAgMS40LTEuMiAyLjYtMi42IDIuNi0xLjQgMC0yLjYtMS4yLTIuNi0yLjZ2LTUuMWgtOWMtNy43IDAtMTQuMS02LjQtMTQuMS0xNC4xIDAtNy44IDYuNC0xNC4yIDE0LjEtMTQuMiAwLjcgMCAxLjUgMC4xIDIuMiAwLjMgMi43LTkuMiAxMS4xLTE1LjcgMjEtMTUuN3EwLjMgMCAwLjYgMC4xYzMuMy00LjggOC45LTcuOCAxNC44LTcuOCA5LjkgMCAxNy45IDguMSAxNy45IDE4IDAgMi44LTAuNiA1LjUtMS44IDcuOSAzLjQgMi42IDUuNyA2LjcgNS43IDExLjR6bS0zMC43LTI4LjZjNi45IDIuMiAxMi4zIDcuNiAxNC40IDE0LjcgMC43LTAuMiAxLjUtMC4zIDIuMi0wLjNxMS45IDAuMSAzLjggMC42YzAuOC0xLjggMS4zLTMuNyAxLjMtNS43IDAtNy4xLTUuOC0xMi44LTEyLjgtMTIuOC0zLjQgMC02LjUgMS4zLTguOSAzLjV6bTI1LjUgMjguNmMwLTUtNC05LTguOS05LTAuNCAwLTEgMC4xLTEuNiAwLjJ2Mi4zYzAgMS41LTEuMSAyLjYtMi41IDIuNi0xLjUgMC0yLjYtMS4xLTIuNi0yLjZ2LTQuMWMtMS40LTgtOC4zLTEzLjgtMTYuNC0xMy44LTguMiAwLTE1LjEgNS44LTE2LjUgMTMuOHY0LjFjMCAxLjUtMS4xIDIuNi0yLjYgMi42LTEuNCAwLTIuNS0xLjEtMi41LTIuNnYtMi4zYy0wLjYtMC4xLTEuMi0wLjItMS42LTAuMi00LjkgMC04LjkgNC04LjkgOSAwIDQuOSA0IDguOSA4LjkgOC45aDQ2LjNjNC45IDAgOC45LTQgOC45LTguOXptLTE3LjkgMjkuNXY1LjFjMCAxLjQtMS4yIDIuNi0yLjYgMi42LTEuNCAwLTIuNi0xLjItMi42LTIuNnYtNS4xYzAtMS40IDEuMi0yLjYgMi42LTIuNiAxLjQgMCAyLjYgMS4yIDIuNiAyLjZ6Ii8+Cjwvc3ZnPgo=',
    );
}



function weatherflow_settings_page()    {
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

        if (isset($_POST['weatherflow_display_options_temp'])) {
            $display_options['temp'] = true;
        } else {
            $display_options['temp'] = false;
        }
        
        if (isset($_POST['weatherflow_display_options_clouds'])) {
            $display_options['clouds'] = true;
        } else {
            $display_options['clouds'] = false;
        }
        
        if (isset($_POST['weatherflow_display_options_desc'])) {
            $display_options['desc'] = true;
        } else {
            $display_options['desc'] = false;
        }

        if (isset($_POST['weatherflow_background_colour'])) {
            update_option('weatherflow_background_colour', sanitize_text_field($_POST['weatherflow_background_colour']));
        }

        if (isset($_POST['weatherflow_background_text_colour'])) {
            update_option('weatherflow_background_text_colour', sanitize_text_field($_POST['weatherflow_background_text_colour'] ));
        }

        if (isset($_POST['weatherflow_card_colour'])) {
            update_option('weatherflow_card_colour', sanitize_text_field($_POST['weatherflow_card_colour'] ));
        }

        if (isset($_POST['weatherflow_card_text_colour'])) {
            update_option('weatherflow_card_text_colour', sanitize_text_field($_POST['weatherflow_card_text_colour'] ));
        }
        
        // Save the updated display options
        update_option('weatherflow_display_options', $display_options);
        

        echo '<div class="updated"><p>Settings saved successfully!</p></div>';
    }

    //Retrieving options to display in settings
    $api_key = get_option('weatherflow_api_key', '');
    $latitude = get_option('weatherflow_latitude', '');
    $longitude = get_option('weatherflow_longitude', '');
    $location_name = get_option('weatherflow_location_name', '');
    $hour_limit = get_option('weatherflow_hour_limit', 12);
    $display_options = get_option('weatherflow_display_options', [
        'temp' => true,
        'clouds' => true,
        'desc' => true,
    ]);
    $background_colour = get_option('weatherflow_background_colour','#333333');
    $title_text_colour = get_option('weatherflow_background_text_colour','#FFFFFF');
    $card_colour = get_option('weatherflow_card_colour','#FFFFFF');
    $card_text_colour = get_option('weatherflow_card_text_colour','#333333');


//Admin page settings form
?>
    <div class="weatherflow-admin-wrap">
        <h1>🌦 WeatherFlow Settings</h1>
        <strong>This plugin is free to use! <a target="_blank" href="https://buymeacoffee.com/rupertmorgan">Show the developer some love🍺</a></strong>
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
                        <tr>
                            <th colspan="2">Information Display Options</th>
                        </tr>
                        
                        <tr>
                            <td>
                            <input type="checkbox" id="weatherflow_display_options_temp" name="weatherflow_display_options_temp" value="1" <?php checked($display_options['temp'], true); ?> />

                            <label for="weatherflow_display_options_temp">Temperature</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <input type="checkbox" id="weatherflow_display_options_clouds" name="weatherflow_display_options_clouds" value="1" <?php checked($display_options['clouds'], true); ?> />

                            <label for="weatherflow_display_options_clouds">Clouds (%)</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <input type="checkbox" id="weatherflow_display_options_desc" name="weatherflow_display_options_desc" value="1" <?php checked($display_options['desc'], true); ?> />

                            <label for="weatherflow_display_options_desc">Description</label>
                            </td>
                        </tr>
                        <tr>
                            <th>Colour Options</th>
                        </tr>
                        <tr>
                            <td>
                            <label for="weatherflow_background_colour">Background Colour</label>
                            </td>
                            <td>
                                <input type="text" id="weatherflow_background_colour" name="weatherflow_background_colour" value="<?php echo esc_attr($background_colour); ?>" class="weatherflow-colour-field" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <label for="weatherflow_background_text_colour">Title Text Colour</label>
                            <br>
                            <em>This should contrast with the background colour.</em>
                            </td>
                            <td>
                                <input type="text" id="weatherflow_background_text_colour" name="weatherflow_background_text_colour" value="<?php echo esc_attr($title_text_colour); ?>" class="weatherflow-colour-field" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <label for="weatherflow_card_colour">Weather Card Background Colour</label>
                            </td>
                            <td>
                                <input type="text" id="weatherflow_card_colour" name="weatherflow_card_colour" value="<?php echo esc_attr($card_colour); ?>" class="weatherflow-colour-field" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <label for="weatherflow_card_text_colour">Weather Card Text Colour</label>
                            <br>
                            <em>This should contrast with the card background colour.</em>
                            </td>
                            <td>
                                <input type="text" id="weatherflow_card_text_colour" name="weatherflow_card_text_colour" value="<?php echo esc_attr($card_text_colour); ?>" class="weatherflow-colour-field" />
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Settings'); ?>
            </div>

            </form>
            <div class="weatherflow-admin-help">
                <p class="weatherflow-info">
                <strong class="weatherflow-support-link">For help with WeatherFlow, or to suggest a feature, create a topic on the support site <a href="https://wordpress.org/support/plugin/weatherflow/">here.</a><br>Support for more forecast metrics and intervals (daily, weekly) is on the way!</strong>
                <strong>Get Started:</strong><br>
                <ol>
                    <li><strong>Get an API Key.</strong> This plugin uses OpenWeatherMap's One Call API 3.0. It is a paid service, however the first 1000 calls a day are free. You can also set a limit so that you never exceed the free number of calls and therefore aren't charged. Go to <a target="_blank" href="https://openweathermap.org/api/one-call-3">https://openweathermap.org/api/one-call-3</a> and follow the instructions under 'How to start'. Paste your API Key in here once you've got it.</li>
                    <li><strong>Add your location.</strong> Future updates will likely bring a location search box, but for now please use a service like <a target="_blank" href="https://www.latlong.net/">https://www.latlong.net/</a> for now.</li>
                    <li><strong>Add a display name.</strong> This will be shown to visitors on your website.</li>
                    <li><strong>Choose the forecast length.</strong> OpenWeatherMap provides hourly forecasts between 1 and 48 hours.</li>
                    <li><strong>Add the widget to your site.</strong> Insert a <em>Shortcode</em> block where you want weatherflow to be, and enter <code>WeatherFlow</code>.</li>
                </ol>
                <strong>Helpful info:</strong>
                <ul>
                    <li>
                        If you're getting errors with your API key just after signing up with OpenWeatherMap, you may need to wait a while for your key to be activated on their end.
                    </li>
                    <li>A list of common API errors can be found <a target="_blank" href="https://openweathermap.org/api/one-call-3#popularerrors">here</a>.</li>
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
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    wp_enqueue_script('weatherflow-admin-js', plugin_dir_url(__FILE__) . 'assets/js/weatherflow-admin.js');
}

add_action('admin_enqueue_scripts', 'weatherflow_enqueue_admin_styles');

//Adds settings link to WeatherFlow in the plugins page
function weatherflow_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=weatherflow-settings') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'weatherflow_add_settings_link');

?>