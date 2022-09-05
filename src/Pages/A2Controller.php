<?php

namespace Jubayed\A2\Pages;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class A2Controller
{
    public $index_path;
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $this->index_path = fixed_path(__DIR__ . "/../../static/index.html");

        try {
            $client = new Client();
            $res = $client->request('GET', api_url('static/php/index.php'), ['headers' => array(
                'A2-TECHNOLOGY' => getenv('A2_TECHNOLOGY'),
                'A2-TOKEN' => getenv('A2_TOKEN'),
                'A2-APPNAME' => getenv('APP_NAME'),
                'Content-Type: text/plain'
            )]);
            if ($res->getStatusCode() == 200) {
                $content = (string)$res->getBody();

                if (!file_exists($this->index_path)) {
                    file_put_contents($this->index_path, $content);
                } else if (file_get_contents($this->index_path) != $content) {
                    file_put_contents($this->index_path, $content);
                }
            }
        } catch (ClientException $e) {
        } catch (ConnectException $e) {}

        if (!file_exists($this->index_path)) {
            return abort(404);
        }

        $content =  file_get_contents($this->index_path);
        $added = "<head><script  type='text/javascript'>window.localStorage.setItem('A2-TECHNOLOGY', '" . getenv('A2_TECHNOLOGY') . "');\nwindow.localStorage.setItem('A2-TOKEN', '" . getenv('A2_TOKEN') . "');\nwindow.localStorage.setItem('A2-APPNAME', '" . getenv('APP_NAME') . "');</script>";

        return str_replace('<head>', $added, $content);
    }

    /**
     * Get assets
     */
    public function static($dir, $file)
    {
        $file_path = __DIR__ . "/../../static/{$dir}/{$file}";

        $min  = 'text/plain';
        if ($dir == 'js') {
            $min  = 'text/javascript';
        } else if ($dir == 'css') {
            $min = 'text/css';
        }

        header('Content-Type: ' . $min);

        if (file_exists($file_path)) {
            return file_get_contents($file_path);
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

                $content = (string)$res->getBody();
                file_put_contents($file_path, "{$content}");
                return $content;
                
            }
        } catch (ClientException $e) {
           
        } catch (ConnectException $e) {
        }

        return abort(404);
    }

    /**
     * Clean cache files
     */
    public function clean()
    {
        $file_path = fixed_path(dirname(__DIR__, 2) . "/static/*/*.*");
        $files = [];

        foreach (glob($file_path) as $path) {
            if (file_exists($path) && unlink($path)) {
                $files[] = $path;
            }
        }

        echo "<pre>";
        print_r($files);
        echo "</pre>";

        return "";
    }
}
