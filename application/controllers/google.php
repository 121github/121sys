<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Google extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
        define('CREDENTIALS_PATH', '.credentials/calendar-php-quickstart.json');
        define('CLIENT_SECRET_PATH','client_secret.json');
        // If modifying these scopes, delete your previously saved credentials
        // at ~/.credentials/calendar-php-quickstart.json
        define('SCOPES', implode(' ', array(
                Google_Service_Calendar::CALENDAR_READONLY,
                'https://www.googleapis.com/auth/userinfo.email'
            )
        ));

        $this->client = new Google_Client();
        $this->client->setApplicationName(APPLICATION_NAME);
        $this->client->setScopes(SCOPES);
        $this->client->setAuthConfigFile(CLIENT_SECRET_PATH);
        $this->client->setAccessType('offline');

//        $this->client = new Google_Client();
//        $this->client->setAccessType('offline');
//        $this->client->setApplicationName('121 System');
//        $this->client->setClientId('941364401612-9rq1ik275hud323ehflvgrnhjndif460.apps.googleusercontent.com');
//        $this->client->setClientSecret('UoIn585J2RvdPPafhFtEX6_5');
//        $this->client->setScopes(array("https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/userinfo.email"));
//        $this->client->setDeveloperKey('AIzaSyBimUzlpAP3mDdBVMlQLiG9K76rUU1ifKM');

        $scriptUri = base_url() . 'google/authcode';
        $this->client->setRedirectUri($scriptUri);
    }

    public function authenticate()
    {
        $auth_url = $this->client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    }

    public function authcode()
    {
        if (!isset($_GET['code'])) {
            $this->authenticate();
        } else {
            $this->client->authenticate($_GET['code']);
            $token = json_decode($this->client->getAccessToken(), true);
            //$_SESSION['api']['google']['token'] = $token;
            $data = array(
                "api_name" => "google",
                "access_token" => $token['access_token'],
                "token_type" => $token['token_type'],
                "expires_in" => $token['expires_in'],
                "id_token" => $token['id_token'],
                "created" => $token['created'],
                "user_id" => $_SESSION['user_id'],
                "date_added" => date('Y-m-d H:i:s')
            );
            $this->db->where(array("user_id" => $_SESSION['user_id'], "api_name" => "google"));
            $this->db->insert_update("apis", $data);

            $redirect_uri = base_url() . 'booking/google';
            header('Location: ' . $redirect_uri);
        }

        echo json_encode(array("success" => true));
    }


    public function logout()
    {
        //if (isset($_SESSION['api']['google'])) {
        //    unset($_SESSION['api']['google']);
        //}
        $this->db->where(array('user_id' => $_SESSION['user_id'], 'api_name' => 'google'));
        $this->db->delete('apis');
        $redirect_uri = $_SERVER['HTTP_REFERER'];
        header('Location: ' . $redirect_uri);
    }
}

