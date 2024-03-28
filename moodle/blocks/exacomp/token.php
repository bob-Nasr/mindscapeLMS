<?php

define('AJAX_SCRIPT', true);
define('REQUIRE_CORRECT_ACCESS', true);
define('NO_MOODLE_COOKIES', true);

require __DIR__ . '/inc.php';

function block_exacomp_load_service($service) {
    extract($GLOBALS);

    ob_start();
    try {
        $_POST['service'] = $service;
        require __DIR__ . '/../../login/token.php';
    } catch (moodle_exception $e) {
        if ($e->errorcode == 'servicenotavailable') {
            return null;
        }

        global $A2FA_ERROR;
        if ($A2FA_ERROR) {
            echo json_encode([
                'error' => $A2FA_ERROR,
                'errorcode' => 'a2farequired',
            ], JSON_PRETTY_PRINT);
            exit;
        }

        throw $e;
    }
    $ret = ob_get_clean();

    $data = json_decode($ret);
    if ($data && $data->token) {
        return $data->token;
    } else {
        return null;
    }
}

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');
echo $OUTPUT->header();

required_param('app', PARAM_TEXT);
required_param('app_version', PARAM_TEXT);

if (optional_param('testconnection', false, PARAM_BOOL)) {
    echo json_encode([
        'moodleName' => $DB->get_field('course', 'fullname', ['id' => 1]),
    ], JSON_PRETTY_PRINT);
    exit;
}

$exa_tokens = [];

$services = optional_param('services', '', PARAM_TEXT);
$services = array_keys(
    ['moodle_mobile_app' => 1, 'exacompservices' => 1] // default services
    + ($services ? array_flip(explode(',', $services)) : []));

foreach ($services as $service) {
    $token = block_exacomp_load_service($service);
    $exa_tokens[] = [
        'service' => $service,
        'token' => $token,
    ];
}

// get login data
$data = \block_exacomp\externallib\externallib::login();
// add tokens
$data['tokens'] = $exa_tokens;

// clean output
$data = external_api::clean_returnvalue(\block_exacomp\externallib\externallib::login_returns(), $data);

echo json_encode($data, JSON_PRETTY_PRINT);
