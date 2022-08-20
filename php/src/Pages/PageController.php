<?php

namespace Jubayed\A2\Pages;

use Symfony\Component\Filesystem\Filesystem;

class PageController
{
    /**
     * Display a listing of the resource.
     *
     */
    public function display()
    {
        $path = $_SERVER['REQUEST_URI'];
        $filesystem = new Filesystem;

        if($path == '/' || $path == '' || $path == null ){
            $path = '/index.html';
        }
        $path = base_path("_html". $path);
        
        if(!$filesystem->exists(base_path('_html')) || !$filesystem->exists($path)){
            return abort(404);
        }

        $mime = '';
        if(str_ends_with($path, '.html')){
            $mime = 'text/html';
        }elseif (str_ends_with($path, '.js')) {
            $mime = 'text/javascript';
        } elseif (str_ends_with($path, '.css')) {
            $mime = 'text/css';
        } else {
            $mime = mime_content_type($path);
        }


        header('Content-Type: '. $mime);
        return file_get_contents($path);
    }
}
