<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcf348a4949ad307d249b2a21085e433d
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tools\\' => 6,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tools\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tools',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Tools\\Connexion' => __DIR__ . '/../..' . '/tools/Connexion.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf348a4949ad307d249b2a21085e433d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf348a4949ad307d249b2a21085e433d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcf348a4949ad307d249b2a21085e433d::$classMap;

        }, null, ClassLoader::class);
    }
}