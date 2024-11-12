<?php

namespace StoreSync\Meliconnect\Core\Helpers;


class MeliconMeli
{
    const VERSION = "1.0.0";
    const API_ROOT_URL = "https://api.mercadolibre.com";
    const OAUTH_URL = "/oauth/token";
    const AUTH_URL = [
        "MLA" => "https://auth.mercadolibre.com.ar", // Argentina
        "MLB" => "https://auth.mercadolivre.com.br", // Brasil
        "MCO" => "https://auth.mercadolibre.com.co", // Colombia
        "MCR" => "https://auth.mercadolibre.com.cr", // Costa Rica
        "MEC" => "https://auth.mercadolibre.com.ec", // Ecuador
        "MLC" => "https://auth.mercadolibre.cl",     // Chile
        "MLM" => "https://auth.mercadolibre.com.mx", // Mexico
        "MLU" => "https://auth.mercadolibre.com.uy", // Uruguay
        "MLV" => "https://auth.mercadolibre.com.ve", // Venezuela
        "MPA" => "https://auth.mercadolibre.com.pa", // Panama
        "MPE" => "https://auth.mercadolibre.com.pe", // Peru
        "MPT" => "https://auth.mercadolibre.com.pt", // Portugal
        "MRD" => "https://auth.mercadolibre.com.do"  // Dominicana
    ];

    const CURL_OPTS = [
        CURLOPT_USERAGENT => "MELI-PHP-SDK-2.0.0",
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 60
    ];

    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $access_token;
    private $refresh_token;

    /**
     * Constructor method. Set all variables to connect to Meli
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string|null $access_token
     * @param string|null $refresh_token
     */
    public function __construct($client_id, $client_secret, $access_token = null, $refresh_token = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
    }

    /**
     * Return a complete Meli login URL.
     *
     * @param string $redirect_uri
     * @param string $auth_url
     * @return string
     */
    public function getAuthUrl($redirect_uri, $auth_url)
    {
        $this->redirect_uri = $redirect_uri;
        $params = [
            "client_id" => $this->client_id,
            "response_type" => "code",
            "redirect_uri" => $redirect_uri
        ];
        return $auth_url . "/authorization?" . http_build_query($params);
    }

    /**
     * Execute a POST Request to authorize the application and obtain an AccessToken.
     *
     * @param string $code
     * @param string $redirect_uri
     * @return mixed
     */
    public function authorize($code, $redirect_uri = null)
    {
        if ($redirect_uri) {
            $this->redirect_uri = $redirect_uri;
        }

        $body = [
            "grant_type" => "authorization_code",
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "code" => $code,
            "redirect_uri" => $this->redirect_uri
        ];

        $opts = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($body)
        ];

        return $this->execute(self::OAUTH_URL, $opts);
    }

    /**
     * Execute a POST Request to create a new AccessToken using a refresh_token
     *
     * @return mixed
     */
    public function refreshAccessToken()
    {
        if ($this->refresh_token) {
            $body = [
                "grant_type" => "refresh_token",
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "refresh_token" => $this->refresh_token
            ];

            $opts = [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($body)
            ];

            return $this->execute(self::OAUTH_URL, $opts);
        } else {
            return [
                'error' => 'Offline-Access is not allowed.',
                'httpCode' => null
            ];
        }
    }

    /**
     * Execute a GET Request
     *
     * @param string $path
     * @param array|null $params
     * @param boolean $assoc
     * @return mixed
     */
    public function get($path, $params = null, $assoc = false)
    {
        return $this->execute($path, [], $params, $assoc);
    }

    /**
     * Execute a POST Request
     *
     * @param string $path
     * @param array|null $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = [])
    {
        $opts = [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body)
        ];

        return $this->execute($path, $opts, $params);
    }

    /**
     * Execute a PUT Request
     *
     * @param string $path
     * @param array|null $body
     * @param array $params
     * @return mixed
     */
    public function put($path, $body = null, $params = [])
    {
        $opts = [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json; Accept: application/json; charset=UTF-8;'],
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($body)
        ];

        return $this->execute($path, $opts, $params);
    }

    /**
     * Execute a DELETE Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function delete($path, $params)
    {
        $opts = [
            CURLOPT_CUSTOMREQUEST => "DELETE"
        ];

        return $this->execute($path, $opts, $params);
    }

    /**
     * Execute an OPTIONS Request
     *
     * @param string $path
     * @param array|null $params
     * @return mixed
     */
    public function options($path, $params = null)
    {
        $opts = [
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        ];

        return $this->execute($path, $opts, $params);
    }

    /**
     * Execute all requests and return the JSON body and headers
     *
     * @param string $path
     * @param array $opts
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    private function execute($path, $opts = [], $params = [], $assoc = false)
    {
        $uri = $this->make_path($path, $params);

        $ch = curl_init($uri);
        curl_setopt_array($ch, self::CURL_OPTS);
        if (!empty($opts)) {
            curl_setopt_array($ch, $opts);
        }

        $return["body"] = json_decode(curl_exec($ch), $assoc);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        /* if (isset($return["body"]->message) && $return["body"]->message == 'Invalid token') {
            $meliconnect_connection = new MeliconnectConnection();
            $meliconnect_connection->syncHubUsersData();
        } */

        return $return;
    }

    /**
     * Construct a URL to make a request
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    private function make_path($path, $params = [])
    {
        if (!preg_match("/^\//", $path)) {
            $path = '/' . $path;
        }

        $uri = self::API_ROOT_URL . $path;
        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $uri;
    }

    /**
     * Execute a GET request with a custom header
     *
     * @param string $url
     * @param string $access_token
     * @param boolean $assoc
     * @return mixed
     */
    public static function getWithHeader($url, $access_token, $assoc = false)
    {
        // Preparar los headers con el token de acceso
        $headers = [
            'Authorization' => 'Bearer ' . $access_token
        ];

        // Usar wp_remote_get para hacer la solicitud GET
        $response = wp_remote_get('https://api.mercadolibre.com/' . $url, [
            'headers' => $headers,
            'timeout' => 10,  // Tiempo máximo de espera en segundos
            'redirection' => 10,  // Máximo de redirecciones
            'httpversion' => '1.1',  // Versión HTTP
            'blocking' => true,  // Bloqueante hasta obtener respuesta
        ]);

        // Manejo de errores
        if (is_wp_error($response)) {
            error_log('Error HTTP: ' . $response->get_error_message());  // Guardar el error en el log
            return null;  // Retornar null en caso de error
        }

        // Obtener el código HTTP de la respuesta
        $httpCode = wp_remote_retrieve_response_code($response);

        // Verificar si el código HTTP no es 200
        if ($httpCode !== 200) {
            error_log("Error HTTP: " . $httpCode);
        }

        // Decodificar el cuerpo de la respuesta
        $body = wp_remote_retrieve_body($response);
        $return["body"] = json_decode($body, $assoc);
        $return["httpCode"] = $httpCode;

        // Verificar si hubo errores al decodificar el JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Error JSON: ' . json_last_error_msg());  // Guardar el error de JSON en error_log
        }

        return $return;
    }


    public static function getMercadoLibreListingData($meliListingId, $access_token)
    {

        $response = [
            'data' => [],
            'description' => [],
        ];

        if (empty($meliListingId) || empty($access_token)) {
            return $response;
        }

        //Get meli item data
        $items = self::getWithHeader('items/' . $meliListingId . '?include_attributes=all', $access_token);

        if (!isset($items['body'])) {
            return [];
        }

        $response['data'] = $items['body'];

        //Get meli item description
        $description = self::getWithHeader('items/' . $meliListingId . '/description', $access_token);

        if (isset($description['body']->plain_text)) {
            $response['description'] = $description['body']->plain_text;
        }

        return $response;
    }


    public static function getMeliImageData($ml_image_id)
    {
        $image_data = MeliconMeli::simpleGet('https://api.mercadolibre.com/pictures/' . $ml_image_id);

        // Verificar si se devolvió un error
        if (is_wp_error($image_data)) {
            // Manejar el error (puedes registrar el error, devolver null, etc.)
            error_log('Error al obtener datos de la imagen de MercadoLibre: ' . $image_data->get_error_message());
            return null;
        }

        // Verificar si los datos son válidos
        if (isset($image_data['id']) && !empty($image_data['id'])) {
            return $image_data;
        }

        return null;
    }

    public static function uploadPictureToMeli($attachment_id, $access_token)
    {
        // Obtener la URL de la imagen de tamaño completo
        $attachment_path = get_attached_file($attachment_id); // Ruta física del archivo original

        if (isset($attachment_path) && !empty($attachment_path)) {

            // Crear un archivo para la carga usando el formato requerido por WordPress
            $file_array = array(
                'file' => new \CURLFile($attachment_path)
            );

            // Establecer la URL de la API de MercadoLibre con el token de acceso
            $url = 'https://api.mercadolibre.com/pictures/items/upload?access_token=' . $access_token;

            // Realizar la solicitud POST usando wp_remote_post
            $response = wp_remote_post($url, array(
                'method'    => 'POST',
                'body'      => $file_array,
                'headers'   => array(
                    'Content-Type' => 'multipart/form-data',
                ),
            ));

            // Verificar si la respuesta contiene un error
            if (is_wp_error($response)) {
                return null; // Retornar null en caso de error
            }

            // Obtener el cuerpo de la respuesta en formato JSON
            $body = wp_remote_retrieve_body($response);
            $json = json_decode($body);

            // Verificar si ocurrió un error en la respuesta de la API
            if (isset($json->error)) {
                return null;
            }

            // Guardar el 'picture_id' devuelto por MercadoLibre como un meta dato del adjunto
            update_post_meta($attachment_id, 'melicon_meli_image_id', $json->id);

            // Retornar el 'picture_id' para confirmar la carga exitosa
            return $json->id;
        }

        // Retornar null si el archivo no se encontró o no es válido
        return null;
    }


    public static function simpleGet($url, $headers = [])
    {
        // Verificamos si la URL está vacía
        if (empty($url)) {
            return new \WP_Error('missing_url', 'La URL no puede estar vacía.');
        }

        // Hacemos la solicitud GET
        $response = wp_remote_get($url, [
            'headers' => $headers
        ]);

        // Verificamos si hubo un error en la solicitud
        if (is_wp_error($response)) {
            return $response;  // Devolvemos el error para manejarlo más tarde
        }

        // Obtenemos el código de respuesta HTTP
        $status_code = wp_remote_retrieve_response_code($response);

        // Si el código no es 200, retornamos un error
        if ($status_code !== 200) {
            return new \WP_Error('http_error', 'Error en la solicitud. Código de estado: ' . $status_code . '. Cuerpo de la respuesta: ' . wp_remote_retrieve_body($response));
        }

        // Extraemos el cuerpo de la respuesta
        $body = wp_remote_retrieve_body($response);

        // Decodificamos si es JSON
        $data = json_decode($body, true);

        // Si no hay datos válidos (es decir, no es JSON), devolvemos el cuerpo sin procesar
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $body;
        }

        return $data;
    }
}
