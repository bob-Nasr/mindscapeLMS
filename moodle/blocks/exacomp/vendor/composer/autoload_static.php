<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6e8509137f55e9c1d129d7c0d0cb2345
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'setasign\\Fpdi\\' => 14,
        ),
        'Z' => 
        array (
            'Zend\\Stdlib\\' => 12,
        ),
        'S' => 
        array (
            'Super\\' => 6,
        ),
        'P' => 
        array (
            'PhpOffice\\PhpWord\\' => 18,
            'PhpOffice\\' => 10,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'setasign\\Fpdi\\' => 
        array (
            0 => __DIR__ . '/..' . '/setasign/fpdi/src',
        ),
        'Zend\\Stdlib\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-stdlib/src',
        ),
        'Super\\' => 
        array (
            0 => __DIR__ . '/..' . '/super',
        ),
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/PhpWord',
        ),
        'PhpOffice\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'FPDF' => __DIR__ . '/..' . '/setasign/fpdf/fpdf.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6e8509137f55e9c1d129d7c0d0cb2345::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6e8509137f55e9c1d129d7c0d0cb2345::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6e8509137f55e9c1d129d7c0d0cb2345::$classMap;

        }, null, ClassLoader::class);
    }
}