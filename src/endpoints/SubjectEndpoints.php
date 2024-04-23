<?php
namespace Aoding9\CompreFace\endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class SubjectEndpoints {
    public static function list($url, $api_key) {
        return Http::asJson()
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->get($url)
                   ->throw()
                   ->json();
    }

    public static function add($subject, $url, $api_key) {
        $data = compact('subject');
        return Http::asJson()
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url, $data)
                   ->throw()
                   ->json();
    }

    public static function rename($subject, $url, $api_key) {
        $data = compact('subject');

        return Http::asJson()
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->put($url, $data)
                   ->throw()
                   ->json();
    }

    public static function deleteSubject($url, $api_key) {
        return Http::asJson()
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->delete($url)
                   ->throw()
                   ->json();
    }

}