<?php
 
namespace Freya\Request;

class Environment extends \Freya\Helpers\Set
{
    public function __construct($settings = null)
    {
        if (! $settings) {
            $env = array();

            $env['REQUEST_METHOD']  = $_SERVER['REQUEST_METHOD'];       // Request method
            $env['REMOTE_ADDR']     = $_SERVER['REMOTE_ADDR'];          // IP Address
            $env['QUERY_STRING']    = $_SERVER['QUERY_STRING'];         // Query string
            $env['SERVER_NAME']     = $_SERVER['SERVER_NAME'];          // Name of the server
            $env['SERVER_PORT']     = $_SERVER['SERVER_PORT'];          // Server port number
            $env['SCRIPT_NAME']     = $_SERVER['SCRIPT_NAME'];

            // Server parameters
            $requestUri     = $_SERVER['REQUEST_URI'];  // The requested resource, including any query string
            $scriptName     = $_SERVER['SCRIPT_NAME'];  // Path to the executing script
            $queryString    = $_SERVER['QUERY_STRING']; // The supplied query string, if any

            // Physical path
            if (strpos($requestUri, $scriptName) !== false) {
                $physicalPath = $scriptName; // <-- Without rewriting
            } else {
                $physicalPath = str_replace('\\', '', dirname($scriptName)); // <-- With rewriting
            }
            //$env['SCRIPT_NAME'] = rtrim($physicalPath, '/'); // <-- Remove trailing slashes

            // Virtual path
            $env['PATH_INFO'] = substr_replace($requestUri, '', 0, strlen($physicalPath)); // <-- Remove physical path
            $env['PATH_INFO'] = str_replace('?' . $queryString, '', $env['PATH_INFO']); // <-- Remove query string
            $env['PATH_INFO'] = '/' . ltrim($env['PATH_INFO'], '/'); // <-- Ensure leading slash

            //HTTP request headers (retains HTTP_ prefix to match $_SERVER)
            $headers = \Freya\Http\Headers::extract($_SERVER);
            foreach ($headers as $key => $value) {
                $env[$key] = $value;
            }

            //Is the application running under HTTPS or HTTP protocol?
            $env['freya.url_scheme'] = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https';

            //Input stream (readable one time only; not available for multipart/form-data requests)
            $rawInput = @file_get_contents('php://input');
            if (!$rawInput) {
                $rawInput = '';
            }
            $env['freya.input'] = $rawInput;

            //Error stream
            $env['freya.errors'] = @fopen('php://stderr', 'w');

            $this->replace($env);
        } else {
            /*
             * Allow us to create a mock environment
             * by suplying our own settings.
             */
            $this->replace($settings);
        }
    }
}
