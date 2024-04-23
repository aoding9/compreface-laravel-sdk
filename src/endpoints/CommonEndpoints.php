<?php
namespace Aoding9\CompreFace\endpoints;

// Collection of common endpoints that used by almost all services
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class CommonEndpoints {

    /**
     * @param $base64string
     * @param $url
     * @param $api_key
     * @return array|mixed
     * @throws RequestException
     */
    public static function upload_base64($base64string, $url, $api_key) {
        $data = [
            'file' => $base64string,
        ];

        return Http::asJson()
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url, $data)
                   ->throw()
                   ->json();
    }

    /**
     * @param object $blobData
     * @param        $url
     * @param        $api_key
     * @return array|mixed
     */
    public static function upload_blob($blobData, $url, $api_key) {

        return Http::attach(['file',$blobData])
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
    }

    /**
     * Upload from local machine
     * @param $image_path
     * @param $url
     * @param $api_key
     * @return array|mixed
     * @throws RequestException
     */
    public static function upload_path($image_path, $url, $api_key) {
        return Http::attach('file', file_get_contents($image_path))
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
    }

    /**
     * Add image (from url) with subject
     * @param        $image_url
     * @param        $url
     * @param        $api_key
     * @param string $file_name
     * @return array|mixed
     * @throws RequestException
     */
    public static function upload_url($image_url, $url, $api_key) {
        $res = Http::get($image_url)->throw();
        // $image_extention = preg_split('/', $res->header('content-type'))[1];
        return Http::attach('file', $res->body())
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                     // todo:确定是否需要 "Content-Length": bodyFormData.getLengthSync(),
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
    }
}
