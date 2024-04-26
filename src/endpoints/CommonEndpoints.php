<?php
namespace Aoding9\CompreFace\endpoints;

// Collection of common endpoints that used by almost all services
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        return Http::attach('file', $blobData)
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
        $res = Http::attach('file', $file = fopen($image_path, 'r'), self::getFileName($image_path))
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
        fclose($file);
        return $res;
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
        // 将获取到的图片数据写入临时文件
        $tempFile = static::writeTempFile($image_url);

        $res = Http::attach('file', $file = fopen($tempFile, 'r'), static::getFileName($tempFile))
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
        fclose($file);
        unlink($tempFile);
        return $res;
    }

    public static function getFileName($path) {
        $extension = (new File($path))->extension();
        return 'tmp' . '_' . time() . '_' . Str::random(10) . '.' . $extension;
    }

    public static function writeTempFile($url, $isBase64 = false) {
        $imageData = $isBase64 ? $url : Http::get($url)->throw()->body();
        $tempFile = tempnam(sys_get_temp_dir(), 'image');
        file_put_contents($tempFile, $imageData);
        return $tempFile;
    }
}
