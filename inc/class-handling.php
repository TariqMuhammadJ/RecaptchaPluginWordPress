<?php


// ✅ Load Encryptor class — adjust path if needed
require_once __DIR__ . '/class-encryptor.php';

class HandleForm {


    private $secret_key;

    public function __construct() {


        add_filter('authenticate', [$this, 'authenticate_user'], 20, 3);
        add_filter('pre_update_option_recaptcha_options', [$this,'validate_keys'], 10, 2);
    }

    public function validate_keys($new_value, $old_value) {
        global $encryptor;
        if ($old_value === false) {
            if (!empty($new_value['secret_key'])) {
                $new_value['secret_key'] = $encryptor->encrypt($new_value['secret_key']);
            }
        } else {
            if (isset($old_value['secret_key'], $new_value['secret_key']) &&
                $old_value['secret_key'] === $new_value['secret_key']) {

                $new_value['secret_key'] = $old_value['secret_key'];
            
            } else {
                if (!empty($new_value['secret_key'])) {
                    $new_value['secret_key'] = $encryptor->encrypt($new_value['secret_key']);
                }
            }
        }
        return $new_value;
    }

    public function verifyRecaptcha($secret, $response) {
        $remote = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $secret,
                'response' => $response,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ],
        ]);

        $body = json_decode(wp_remote_retrieve_body($remote), true);
        error_log($body);
        return isset($body['success']) && $body['success'];
    }

    public function authenticate_user($user, $username, $password) {
        global $encryptor;
        $options = get_option('recaptcha_options');

        if (!empty($options['secret_key'])) {
            if (isset($_POST['g-recaptcha-response'])) {
                $secret = $encryptor->decrypt($options['secret_key']);
                if ($this->verifyRecaptcha($secret, $_POST['g-recaptcha-response'])) {
                    return wp_authenticate_username_password(null, $username, $password);
                }
                return new WP_Error('recaptcha_failed', __('Recaptcha verification failed.'));
            }
            return new WP_Error('recaptcha_missing', __('Recaptcha response missing.'));
        }

        return wp_authenticate_username_password(null, $username, $password);
    }
}


