<?php
namespace Library;

class FileCacheLibrary
{
    public static function isCache(string $path, string $cacheName, int $cacheTime=5): bool
    {
        $cacheName = preg_replace("#[^a-zA-Z0-9\-_]#", "", $cacheName);
        $file = realpath($path).'/'.$cacheName.'-final.bin';

        if(!is_file($file)) return false;

        $currentTime = time();
        $fileTime = filemtime($file);

        return ($currentTime - $fileTime < $cacheTime);
    }

    public static function getHCache(string $path, string $cacheName, int $cacheTime=5): mixed
    {
        $cacheName = preg_replace("#[^a-zA-Z0-9\-_]#", "", $cacheName);
        $file = realpath($path).'/'.$cacheName.'-final.bin';

        if(!is_file($file)) return false;

        $currentTime = time();
        $fileTime = filemtime($file);

        if($currentTime - $fileTime >= $cacheTime) return false;

        $loadClass = new File();
        return unserialize($loadClass->load($file));
    }

    public static function setHCache(string $path, string $cacheName, $cacheValue): void
    {
        $cacheName = preg_replace("#[^a-zA-Z0-9\-_]#", "", $cacheName);
        $file = realpath($path).'/'.$cacheName.'-final.bin';

        $saveClass = new File();
        $saveClass->save($file, serialize($cacheValue));
    }
}
