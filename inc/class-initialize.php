<?php

if (!class_exists('initializeRecaptcha')) {
    class initializeRecaptcha {
        // Hold the class instance
        private static $instance = null;

        // Private constructor to prevent multiple instances
        private function __construct() {
            
        }

        // Prevent cloning
        private function __clone() { }

        // Prevent unserialization
        private function __wakeup() { }

        // Public static method to get the instance
        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new initializeRecaptcha();
            }
            return self::$instance;
            
        }

        // Example method
        public function loadRecaptchaScript() {
            add_action('login_enqueue_scripts',[$this, 'load_scripts']);
            add_action('login_form', [$this, 'add_to_login']);
        }

        public function load_scripts(){
             wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', []);
        }

        public function add_to_login(){
        $value = get_option('recaptcha_options');
        $site_key = !empty($value['site_key']) ? $value['site_key'] : '';

        if ($site_key) {
            echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($site_key) . '"></div>';
        } else {
            wp_die('The site key you used is not valid');
        }
}

    }
}



// Usage:



?>