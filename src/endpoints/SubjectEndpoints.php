<?php
namespace Aoding9\CompreFace\endpoints;

class SubjectEndpoints extends CommonEndpoints {
    public static function list($url, $api_key) {
        return static::http($api_key)
                     ->asJson()
                     ->get($url)
                     ->throw()
                     ->json();
    }

    public static function add($subject, $url, $api_key) {
        $data = compact('subject');
        return static::http($api_key)
                     ->asJson()
                     ->post($url, $data)
                     ->throw()
                     ->json();
    }

    public static function rename($subject, $url, $api_key) {
        $data = compact('subject');

        return static::http($api_key)
                     ->asJson()
                     ->put($url, $data)
                     ->throw()
                     ->json();
    }

    public static function deleteSubject($url, $api_key) {
        return static::http($api_key)
                     ->asJson()
                     ->delete($url)
                     ->throw()
                     ->json();
    }
}