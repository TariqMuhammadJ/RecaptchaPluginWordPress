<?php
/*
Plugin Name: Adding Recaptcha to Forms
Description: A plugin to add Recaptcha to the Login Page in WordPress
Version: 1.0
*/

if (!defined('MY_SECRET_KEY')) {
    define('MY_SECRET_KEY', 'my-super-secret-32-byte-key-value'); 
}


require_once plugin_dir_path(__FILE__) . 'inc/class-encryptor.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-initialize.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-handling.php';


global $encryptor;
$encryptor = new Encryptor(MY_SECRET_KEY);




add_action('admin_menu', 'my_plugin_menu');
add_action('admin_init', 'my_plugin_settings_init');


function my_plugin_menu() {
    add_menu_page('Recaptcha Settings', 'Recaptcha Settings', 'manage_options', 'recaptcha-forms', 'my_plugin_settings_page');
}

function my_plugin_settings_init() {
    register_setting('recaptcha_options_group', 'recaptcha_options');

    add_settings_section('recaptcha_settings', 'Google Recaptcha', null, 'recaptcha-forms');

    add_settings_field('site_key', 'Site Key', 'render_site_key', 'recaptcha-forms', 'recaptcha_settings');
    add_settings_field('secret_key', 'Secret Key', 'render_secret_key', 'recaptcha-forms', 'recaptcha_settings');
}

function render_site_key() {
    $options = get_option('recaptcha_options');
    echo '<input type="text" name="recaptcha_options[site_key]" value="' . esc_attr($options['site_key'] ?? '') . '">';
}

function render_secret_key() {
    $options = get_option('recaptcha_options');
    echo '<input type="text" name="recaptcha_options[secret_key]" value="' . esc_attr($options['secret_key']) . '">';
}

function my_plugin_settings_page() {
?>
    <h1>Recaptcha Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields('recaptcha_options_group'); ?>
        <?php do_settings_sections('recaptcha-forms'); ?>
        <?php submit_button(); ?>
    </form>
<?php
}



if (class_exists('InitializeRecaptcha')) {
    $value = InitializeRecaptcha::getInstance();
    $value->loadRecaptchaScript();
    
}

// ✅ Start Form Handling
if (class_exists('HandleForm')) {
    new HandleForm();
}
