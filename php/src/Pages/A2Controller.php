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
        $file_path = fixed_path(dirname(__DIR__, 3). "/index.html");

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

                if(!is_dir($tpath=dirname($file_path))){
                    mkdir($tpath);
                }

                if(!file_exists($file_path)){
                    file_put_contents($file_path, $content);
                }else if(file_get_contents($file_path) != $content) {
                    file_put_contents($file_path, $content);
                }

                header('Content-Type: text/html');
                return $content;
            }

        } catch (ClientException $e) {

            if(file_exists($file_path)){
                header('Content-Type: text/html');
                return file_get_contents($file_path);
            }
            
            return '<div  style="padding: 20px;background-color: #f44336;color: white;"><strong>Error</strong> Tocken invalid or server not response.</div>';
        }

        return abort(404);
    }

    /**
     * Get assets
     */
    public function static($dir, $file)
    {
        $path = "static/{$dir}/{$file}";
        $file_path = fixed_path(dirname(__DIR__, 3). "/{$path}");

        $min  = 'text/plain';
        if ($dir == 'js') {
            $min  = 'text/javascript';
        } else if ($dir == 'css') {
            $min = 'text/css';
        }

        if(file_exists($file_path)){
            header('Content-Type: ' . $min);
            return file_get_contents($path);
        }
        try {
            $client = new Client();
            $res = $client->request('GET', api_url($path), [
                'headers' => array(
                    'A2-TOKEN' => getenv('A2_TOKEN'),
                    'Content-Type: text/plain'
                )
            ]);
            if ($res->getStatusCode() == 200) {

                $content = (string)$res->getBody();
                file_log('cache'. $dir , $path);

                if(!is_dir($tpath=dirname($file_path))){
                    mkdir($tpath);
                }
                if(!is_dir($tpath=dirname($file_path, 2))){
                    mkdir($tpath);
                }
                
                file_put_contents($file_path, $content);
                
                header('Content-Type: ' . $min);
                return $content;
            }
        } catch (ClientException $e) {
            return "";
        }

        return abort(404);
    }

    /**
     * Clean cache files
     */
    public function clean()
    {
        $file_path = fixed_path(dirname(__DIR__, 3). "/static/*/*.*");
        $files = [];

        foreach (glob($file_path) as $path) {
            if( is_file($path) && unlink($path)){
                $files[] = $path;
                unlink($path);
            }
        }

        print_r($files);
        return "";
    }
}
