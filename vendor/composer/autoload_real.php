<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit08ae02987c9dbeac8005b3dcab89e4d0
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

        spl_autoload_register(array('ComposerAutoloaderInit08ae02987c9dbeac8005b3dcab89e4d0', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit08ae02987c9dbeac8005b3dcab89e4d0', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit08ae02987c9dbeac8005b3dcab89e4d0::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
