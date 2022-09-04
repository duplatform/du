<?php

use Jubayed\A2\Log;

/**
 * Get api server url
 * 
 * @param string $uri
 * @return string
 */
if (!function_exists('api_url')) {

    function api_url($uri)
    {
        if (!str_starts_with($uri, '/')) {
            $uri = "/" . $uri;
        }
        return getenv('API_SERVER') . $uri;
    }
}

/**
 * Fixed directory sparator as /
 * 
 * @param string $path
 * @return string
 */
if (!function_exists('fixed_path')) {

    function fixed_path(string $path, $separator = '/')
    {
        $path = str_replace(['//', '\\\\', '\\'], $separator, $path);

        return rtrim($path, '/');
    }
}

/**
 * Fixed directory sparator as /
 * 
 * @param string $path
 * @return string
 */
if (!function_exists('base_path')) {

    function base_path(string $path = '')
    {
        if ($path == '') {
            return fixed_path(getcwd());
        }

        $path = fixed_path(getcwd() . '/' . $path);

        ensureDirectoryExists(dirname($path));
        return $path;
    }
}

/**
 * Get file relative path
 * 
 * @param string $path
 * @return string
 */
if (!function_exists('relative_path')) {

    function relative_path($path = '')
    {
        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }

        $path = str_replace(fixed_path(dirname(getcwd())), '', fixed_path($path));

        return ltrim(rtrim($path, '/'), '/');
    }
}

/**
 * Search param 
 * 
 * @param string $key
 * 
 * @return mixed
 */
if (!function_exists('get_input')) {

    function get_input($key, $default = null)
    {
        if (isset($_GET[$key])) {
            $default = urldecode($_GET[$key]);

            if ($default == 'FALSE' | $default == 'false') {
                return false;
            } else if ($default == 'true' | $default == 'TRUE') {
                return true;
            } else if ($default == 'null' | $default == 'NULL') {
                return null;
            } else if (isInteger($default)) {
                return (int)$default;
            } else {
                return $default;
            }
        }

        return $default;
    }
}

/**
 * Check integer form string
 * 
 * @param string $str
 * @return bool
 */
if (!function_exists('isInteger')) {

    function isInteger($str)
    {
        foreach (str_split($str, 11) as $c) {
            if (!in_array($c, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'])) {
                return false;
            }
        }

        return true;
    }
}

/**
 * Ensure a directory exists.
 *
 * @param  string  $path
 * @param  int  $mode
 * @param  bool  $recursive
 * @return void
 */
function ensureDirectoryExists($path, $mode = 0755, $recursive = true)
{
    if (!is_dir($path)) {
        mkdir($path, $mode, $recursive);
    }
}

/**
 * Get api server url
 * 
 * @return html
 */
if (!function_exists('abort')) {

    function abort($code = 404)
    {
        header('Content-Type: text/html');
        http_response_code(404);

        $str = "";
        foreach (glob(getcwd() . "/_html/*.html") as $key => $filename) {

            $basename = basename($filename);
            $filesize = filesize($filename);
            $key = $key + 1;

            $str .= "\n <tr>" .
                "\n     <td><strong>[{$key}] </strong> <a href=\"//127.0.0.1:8011/{$basename}\">{$basename}</a></td>" .
                "\n     <td>{$filesize}</td>" .
                "\n </tr>";
        }

        return require __DIR__ . '/../404.php';
    }
}

/**
 * Get a2 absolate path
 * 
 * @param string $path
 * @return string
 */
if (!function_exists('a2_path')) {

    function a2_path($path)
    {
        $base_path = getcwd() . DIRECTORY_SEPARATOR . '.duplatform' . DIRECTORY_SEPARATOR;
        $path = $base_path . str_replace('/', DIRECTORY_SEPARATOR, $path);

        // ensure directories
        if (!is_dir($dir = dirname($path))) {
            mkdir($dir, 0755, true);
        }

        return $path;
    }
}

/**
 * Set log file 
 * 
 * @param string $type
 * @param string $path
 * @return string
 */
if (!function_exists('log')) {

    function log($type, $path)
    {
        return Log::info($type, $path);
    }
}

/**
 * Set log file 
 * 
 * @param string $type
 * @param string $path
 * @return string
 */
if (!function_exists('file_log')) {

    function file_log($type, $path)
    {
        return Log::info($type, $path);
    }
}


/**
 * Project name
 * 
 * @return string
 */
if (!function_exists('project_dir')) {

    function project_dir()
    {
        $project_dir = explode(DIRECTORY_SEPARATOR, getcwd());
        return end($project_dir);
    }
}


