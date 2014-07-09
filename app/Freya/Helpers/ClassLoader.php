<?php

/**
 * @class			ClassLoader
 * @namespace		Freya\Helpers
 * @description
 * 
 * A wrapper class to an autoload function following the PSR-0 standard.
 */

namespace Freya\Helpers;

class ClassLoader
{
    private $prefixes           = array();
    private $fallbacks          = array();
    private $fileExtensions     = array('.php');
    private $namespaceSeparator = '\\';
    private $useIncludePath     = false;

    public function __construct(array $prefixes = null)
    {
        $this->prefixes = $prefixes;
    }
    
    public function addPrefix($prefix, $baseDir)
    {
        $this->prefixes[$prefix] = $baseDir;
    }
    
    public function addFallback($dir)
    {
        $this->fallbacks[] = $dir;
    }
    
    public function addExtension($fileExtension)
    {
        $this->fileExtensions[] = $fileExtension;
    }
    
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    public function setNamespaceSeparator($separator)
    {
        $this->namespaceSeparator = $separator;
    }
    
    public function getUseIncludePath()
    {
        return $this->useIncludePath;
    }
    
    public function setUseIncludePath($useIncludePath)
    {
        $this->useIncludePath = $useIncludePath;
    }

    /**
     * Load classes and register the class loader on the autoload stack
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }
    
    public function loadClass($class)
    {
        if ($path = $this->findClass($class)) {
            require $path;
        }
    }

    public function findClass($class)
    {
        if ($lastNsPos = strripos($class, $this->namespaceSeparator)) {
            $classPath = str_replace(
                $this->namespaceSeparator,
                DIRECTORY_SEPARATOR,
                substr($class, 0, $lastNsPos)
            ) . DIRECTORY_SEPARATOR;
            $className = substr($class, $lastNsPos + 1);
        } else {
            $classPath = null;
            $className = $class;
        }

        $classPath .= str_replace('_', DIRECTORY_SEPARATOR, $className);
        
        foreach ($this->prefixes as $prefix => $baseDir) {
            if ($class === strstr($class, $prefix)) {
                $classPath = ltrim(substr($classPath, strlen($prefix)), '\\/');
                
                foreach ($this->fileExtensions as $ext) {
                    $includePath = $baseDir . DIRECTORY_SEPARATOR . $classPath . $ext;
                    if (file_exists($includePath)) {
                        return $includePath;
                    }
                }
            }
        }
        
        foreach ($this->fallbacks as $fallback) {
            foreach ($this->fileExtensions as $ext) {
                if (file_exists($fallback . DIRECTORY_SEPARATOR . $classPath . $ext)) {
                    return $fallback . DIRECTORY_SEPARATOR . $classPath . $ext;
                }
            }
        }
        
        return false;
    }
}
