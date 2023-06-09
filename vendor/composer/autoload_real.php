<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInite0b1a2ad7a928c42e0fddc5549e6c814
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInite0b1a2ad7a928c42e0fddc5549e6c814', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInite0b1a2ad7a928c42e0fddc5549e6c814', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInite0b1a2ad7a928c42e0fddc5549e6c814::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
