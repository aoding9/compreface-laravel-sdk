<?php
namespace Aoding9\CompreFace\endpoints;

// Collection of common endpoints that used by almost all services

class RecognitionEndpoints extends CommonEndpoints {
    /**
     * View list of faces user tried
     * @param $url
     * @param $api_key
     * @return array|mixed
     */
    public static function list_request($url, $api_key) {
        return static::http($api_key)
                     ->get($url)
                     ->throw()
                     ->json();
    }

    /**
     * Delete image(s)
     * @param $url
     * @param $api_key
     * @return array|mixed
     */
    public static function delete_request($url, $api_key) {
        return static::http($api_key)
                     ->delete($url)
                     ->throw()
                     ->json();
    }

    /**
     * Delete multiple images
     * @param          $url
     * @param          $api_key
     * @param string[] $image_ids
     * @return array|mixed
     */
    public static function delete_multiple($url, $api_key, $image_ids) {
        return static::http($api_key)
                     ->post($url, $image_ids)
                     ->throw()
                     ->json();
    }
}


