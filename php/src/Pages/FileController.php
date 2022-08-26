<?php

namespace Jubayed\A2\Pages;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class FileController
{
    /**
     * Store and delete files
     *
     */
    public function save()
    {
        $filesystem = new Filesystem;
        $message = "File successfull saved.";

        // $request->validate([
        //     'files.*.id'      => 'required|integer',
        //     'files.*.type'      => 'required|string|in:create,replace,delete',
        //     'files.*.path'      => 'required|string',
        //     'files.*.content'   => 'required|string',
        // ]);

        $files = json_decode(file_get_contents('php://input'), true);
        if (isset($files['files']) == false) {
            return abort(404);
        }

        $files = $files['files'];

        foreach ($files as $data) {
            $path = base_path($data['path']);

            if ($data['type'] == 'delete' && $filesystem->exists($path)) {
                $filesystem->remove($path);
                $message = "File successfull deleted.";
                file_log('delete', relative_path($path) . "[" . __LINE__ . "]");
            } elseif ($data['type'] == 'create') {
                if($filesystem->exists($path)){
                    $message = "File allready exists. [".relative_path($path) ."]";
                    file_log('fail_exists', relative_path($path) . "[" . __LINE__ . "]");
                }else{
                    ensureDirectoryExists(dirname($path));
                    $filesystem->dumpFile($path, $data['content']);
                    $message = "File successfull created.";
                    file_log('create', relative_path($path) . "[" . __LINE__ . "]");
                }
            } elseif ($data['type'] == 'replace') {
                ensureDirectoryExists(dirname($path));
                $filesystem->dumpFile($path, $data['content']);
                $message = "File successfull replace.";
                file_log('replace', relative_path($path) . "[" . __LINE__ . "]");
            } else {
                $message = "File oparation error.";
                file_log('fail_oparation', relative_path($path) . "[" . __LINE__ . "]");
            }
        }

        return json_encode([
            'message' => $message,
            'project_dir' => project_dir(),
        ]);
    }

    /**
     * Get stasific file content
     *
     */
    public function getContent()
    {
        $filesystem = new Filesystem;
        $path = base_path(get_input('path', ''));

        if (!$filesystem->exists($path)) {
            return abort(404);
        }
        header('Content-type: application/json');
        return json_encode([
            "path"          => relative_path($path),
            "absolute_path" => $path,
            "content"       => file_get_contents($path),
            'project_dir' => project_dir(),
        ]);
    }

    /**
     * Get sasific dirctory file list
     *
     * @param  string  $path
     */
    public function finder()
    {
        $finder = new Finder();

        $base_path = get_input('path');
        $example = "http://127.0.0.1:8011/finder?path=_html/my&ext=html&dirOnly=true&depth=1";
        header('Content-type: application/json');

        if ($base_path  == "" || $base_path  == "/") {
            return json_encode([
                'path' => $base_path,
                'list' => '',
                'data' => [],
                'project_dir' => project_dir(),
                'example' => $example,
            ]);
        }

        if (!is_dir(base_path($base_path))) {
            return json_encode([
                'path' => $base_path,
                'list' => '',
                'data' => [],
                'project_dir' => project_dir(),
                'example' => $example,
            ]);
        }

        // start
        $data = [];

        $finder->ignoreUnreadableDirs()->in(base_path($base_path));

        if ((int)get_input("depth", 0) == 0) {
            $finder->depth('== 0');
        } else if ($depth = (int)get_input("depth", 0) > 0) {
            $finder->depth("<= {$depth}");
        }

        //
        $list = 'files';
        if (get_input('dirOnly')) {
            // look for directories only; ignore files
            $finder->directories();
            $list = 'directories';
        } else {
            // look for files only; ignore directories
            $finder->files();
            if ($name = get_input('name')) {
                if (\str_contains($name, ".")) {
                    $finder->name($name);
                } else {
                    $finder->name("*.{$name}");
                }
            }
        }

        // check if there are any search results
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $data[] = "{$base_path}/" . $file->getRelativePathname();
            }
        }

        return json_encode([
            'path' => $base_path,
            'list' => $list,
            'data' => $data,
            'project_dir' => project_dir(),
            'example' => $example,
        ]);
    }

    /**
     * File download by url
     * 
     */
    public function downloadFile()
    {        
        $urls = [];
        $file = a2_path('workflows/downloadable-urls.yaml');
        $response = json_decode(file_get_contents('php://input'), true);
        
        if(!file_exists($file)){
            file_put_contents($file, '');
        }

        if(isset($response['urls'])){
            $urls = $response['urls'];
            file_put_contents($file, Yaml::dump($urls));
        }

        if(isset($response['sync'])){
            $urls = Yaml::parseFile($file );

        }

        $client = new Client();
        foreach ($urls as $key => $url) {
            $res = $client->request('GET', $urls[$key], ['headers' => array(
                'A2-TECHNOLOGY' => getenv('A2_TECHNOLOGY'),
                'A2-TOKEN' => getenv('A2_TOKEN'),
                'Content-Type: text/plain'
            )]);
            if ($res->getStatusCode() == 200) {

                $path = \explode('?path=', $urls[$key])[1];
                if(!file_exists($path=base_path($path))){
                    \file_put_contents($path, $res->getBody());
                }
                unset($urls[$key]);
            }
        }
        // save data
        if(count($urls)){
            file_put_contents($file, Yaml::dump($urls));
        }else {
            file_put_contents($file, '');
        }
    }
}
