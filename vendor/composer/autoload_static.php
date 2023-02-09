<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf6e4be9979cb8df5f813e7e4d7a4a84b
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\composer\\' => 15,
            'think\\' => 6,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'think\\' => 
        array (
            0 => __DIR__ . '/../..' . '/thinkphp/library/think',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf6e4be9979cb8df5f813e7e4d7a4a84b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf6e4be9979cb8df5f813e7e4d7a4a84b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf6e4be9979cb8df5f813e7e4d7a4a84b::$classMap;

        }, null, ClassLoader::class);
    }
}
