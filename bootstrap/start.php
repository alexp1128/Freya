<?php

define('FREYA_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Register The Freya Auto Loader
|--------------------------------------------------------------------------
| We register an auto-loader "behind" the Composer loader that can load
| model classes on the fly, even if the autoload files have not been
| regenerated for the application. We'll add it to the stack here.
|
*/

Illuminate\Support\ClassLoader::register();

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Create our application
|--------------------------------------------------------------------------
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/
    
$app = new \Freya\Freya();
        
/*
|--------------------------------------------------------------------------
| Set PHP's error reporting
|--------------------------------------------------------------------------
| Here we need to make sure that PHP is set to report all errors
| regardless of level. Freya's custom error handling will determine what
| gets logged or reported to the screen.
|
*/
    
error_reporting(-1);
        
/*
|--------------------------------------------------------------------------
| Set application base path
|--------------------------------------------------------------------------
| This will return the portion of our application directory above the
| '/app' folder. All of our other path variables will be determined from
| this base path variable.
|
*/
        
$basePath = strstr(__DIR__, "/app", true);

/*
--------------------------------------------------------------------------
| Configure Freya's PSR-0 autoloader
|--------------------------------------------------------------------------
| Freya implements a PSR-0 compliant autoload class. Here we will include
| our ClassLoader.php file and configure it to search through our Freya
| directory as well as our '/vendor' folder for 3rd party middleware.
|
*/

include(