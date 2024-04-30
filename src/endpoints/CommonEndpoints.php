<?php
namespace Aoding9\CompreFace\endpoints;

// Collection of common endpoints that used by almost all services
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CommonEndpoints {

    /**
     * @param $api_key
     * @return \Illuminate\Http\Client\PendingRequest
     * @Date 2024/4/30 10:18
     */
    public static function http($api_key) {
        return Http::withHeaders([
                                     "x-api-key" => $api_key,
                                 ]);
    }

    /**
     * @param $base64string
     * @param $url
     * @param $api_key
     * @return array|mixed
     */
    public static function upload_base64($base64string, $url, $api_key) {
        $data = [
            'file' => $base64string,
        ];
        return static::http($api_key)
                     ->asJson()
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
        return static::http($api_key)
                     ->attach('file', $blobData)
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
     */
    public static function upload_path($image_path, $url, $api_key) {
        try {
            $res = static::http($api_key)
                         ->attach('file', $file = fopen($image_path, 'r'), self::getFileName($image_path))
                         ->post($url)
                         ->throw()
                         ->json();
        } finally {
            fclose($file);
        }

        return $res;
    }

    /**
     * Add image (from url) with subject
     * @param        $image_url
     * @param        $url
     * @param        $api_key
     * @return array|mixed
     */
    public static function upload_url($image_url, $url, $api_key) {
        // 将获取到的图片数据写入临时文件
        $tempFile = static::writeTempFile($image_url);
        $file = false;
        try {
            $res = static::http($api_key)
                         ->attach('file', $file = fopen($tempFile, 'r'), static::getFileName($tempFile))
                         ->post($url)
                         ->throw()
                         ->json();
        } finally {
            $file && fclose($file);
            unlink($tempFile);
        }

        return $res;
    }

    public static function getFileName($path) {
        $extension = (new File($path))->extension();
        return 'tmp' . '_' . time() . '_' . Str::random(10) . '.' . $extension;
    }

    public static function writeTempFile($url, $isBase64 = false) {
        try {
            $imageData = $isBase64 ? $url : Http::get($url)->throw()->body();
        } catch (\Exception $e) {
            throw new \Aoding9\CompreFace\Exceptions\Exception('图片数据获取失败：' . $url);
        }
        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'image');
            file_put_contents($tempFile, $imageData);
        } finally {
            $tempFile && unlink($tempFile);
        }
        return $tempFile;
    }

}
