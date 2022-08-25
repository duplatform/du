<?php

namespace Jubayed\A2\Pages;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class A2Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {        
        try {
            $client = new Client();
            $res = $client->request('GET', api_url('static/php/index.php'), ['headers' => array(
                'A2-TECHNOLOGY' => getenv('A2_TECHNOLOGY'),
                'A2-TOKEN' => getenv('A2_TOKEN'),
                'A2-APPNAME' => getenv('APP_NAME'),
                'Content-Type: text/plain'
            )]);
            if ($res->getStatusCode() == 200) {
                header('Content-Type: text/html');
                return $res->getBody();
            }

        } catch (ClientException $e) {
            echo '<div  style="padding: 20px;background-color: #f44336;color: white;"><strong>Error</strong> Tocken invalid or server not response.</div>';
            exit();
        }

        return abort(404);
    }

    /**
     * Get assets
     */
    public function static($dir, $file)
    {
        $min  = 'text/plain';
        if ($dir == 'js') {
            $min  = 'text/javascript';
        } else if ($dir == 'css') {
            $min = 'text/css';
        }

        try {
            $client = new Client();
            $res = $client->request('GET', api_url("static/{$dir}/{$file}"), [
                'headers' => array(
                    'A2-TOKEN' => getenv('A2_TOKEN'),
                    'Content-Type: text/plain'
                )
            ]);
            if ($res->getStatusCode() == 200) {
                header('Content-Type: ' . $min);
                return $res->getBody();
            }
        } catch (ClientException $e) {
            exit();
        }

        return abort(404);
    }
}
