<?php
namespace Aoding9\CompreFace\endpoints;

// Collection of common endpoints that used by almost all services
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RecognitionEndpoints {
    /**
     * View list of faces user tried
     * @param $url
     * @param $api_key
     * @return array|mixed
     * @throws RequestException
     */
    public static function list_request($url, $api_key) {
        return Http::withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->get($url)
                   ->throw()
                   ->json();
    }

    /**
     * Delete image(s)
     * @param $url
     * @param $api_key
     * @return array|mixed
     * @throws RequestException
     */
    public static function delete_request($url, $api_key) {
        return Http::withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
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
     * @throws RequestException
     */
    public static function delete_multiple($url, $api_key, $image_ids) {
        return Http::withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url, $image_ids)
                   ->throw()
                   ->json();
    }
}


