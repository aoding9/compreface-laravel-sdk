<?php
namespace Aoding9\CompreFace\endpoints;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class VerificationEndpoints extends CommonEndpoints {
    /**
     * Verify face(s) from given image
     */
    public static function verify_face_request($source_image_path, $target_image_path, $url, $api_key) {
        $res = Http::attach([
                                ['source_image', $source_image_path = fopen($source_image_path, 'r')],
                                ['target_image', $target_image_path = fopen($target_image_path, 'r')],
                            ])
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
        fclose($source_image_path);
        fclose($target_image_path);
        return $res;
    }

    /**
     * Verify face(s) from given image urls
     */
    public static function both_url_request($source_image_url, $target_image_url, $url, $api_key, $file_name = 'example') {
        // 将获取到的图片数据写入临时文件
        $tempFile0 = static::writeTempFile($source_image_url);
        $tempFile1 = static::writeTempFile($target_image_url);
        $res = Http::attach([
                                ['source_image', $file0 = fopen($tempFile0, 'r'), static::getFileName($tempFile0)],
                                ['target_image', $file1 = fopen($tempFile1, 'r'), static::getFileName($tempFile1)],
                            ])
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();

        fclose($file0);
        fclose($file1);
        unlink($tempFile0);
        unlink($tempFile1);
        return $res;
    }

    /**
     * Verify face(s) from given image urls
     */
    public static function one_url_request($source_image_path, $isSourceImageUrl, $target_image_path, $url, $api_key) {
        $path_is_url = [];
        $path_is_relative = [];

        if ($isSourceImageUrl) {
            $path_is_url[0] = "source_image";
            $path_is_url[1] = $source_image_path;

            $path_is_relative[0] = "target_image";
            $path_is_relative[1] = $target_image_path;
        } else {
            $path_is_url[0] = "target_image";
            $path_is_url[1] = $target_image_path;

            $path_is_relative[0] = "source_image";
            $path_is_relative[1] = $source_image_path;
        }

        $files[] = [$path_is_relative[0], $file0 = fopen($path_is_relative[1], 'r'), self::getFileName($path_is_relative[1])];

        $tempFile = static::writeTempFile($path_is_url[1]);

        $files[] = [$path_is_url[0], $file = fopen($tempFile, 'r'), static::getFileName($tempFile)];

        $res = Http::attach($files)
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();

        fclose($file0);
        fclose($file);
        unlink($tempFile);
        return $res;
    }

    /**
     * Verify face(s) from given blob data
     */
    public static function url_blob_request($source_image_path, $isSourceImageUrl, $target_image_path, $url, $api_key) {
        $path_is_url = [];
        $path_is_blob = [];

        if ($isSourceImageUrl) {
            $path_is_url[0] = "source_image";
            $path_is_url[1] = $source_image_path;

            $path_is_blob[0] = "target_image";
            $path_is_blob[1] = $target_image_path;
        } else {
            $path_is_url[0] = "target_image";
            $path_is_url[1] = $target_image_path;

            $path_is_blob[0] = "source_image";
            $path_is_blob[1] = $source_image_path;
        }

        $files[] = [$path_is_blob[0], $path_is_blob[1]];

        $tempFile = static::writeTempFile($path_is_url[1]);

        $files[] = [$path_is_url[0], $file = fopen($tempFile, 'r'), static::getFileName($tempFile)];

        $res = Http::attach($files)
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

    /**
     * Both source and target images are blob
     * @param {Blob} source_image_blob
     * @param {Blob} target_image_blob
     * @param {String} url
     * @param {String} api_key
     */
    public static function both_blob_request($source_image_blob, $target_image_blob, $url, $api_key) {
        return Http::attach([
                                ['source_image', $source_image_blob],
                                ['target_image', $target_image_blob],
                            ])
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
    }

    public static function one_blob_request($source_image_path, $isSourceImageBlob, $target_image_path, $url, $api_key) {
        $path_is_blob = [];
        $path_is_relative = [];

        if ($isSourceImageBlob) {
            $path_is_blob[0] = "source_image";
            $path_is_blob[1] = $source_image_path;

            $path_is_relative[0] = "target_image";
            $path_is_relative[1] = $target_image_path;
        } else {
            $path_is_blob[0] = "target_image";
            $path_is_blob[1] = $target_image_path;

            $path_is_relative[0] = "source_image";
            $path_is_relative[1] = $source_image_path;
        }
        $files[] = [$path_is_relative[0], $file = fopen($path_is_relative[1], 'r'), self::getFileName($path_is_relative[1])];

        $files[] = [$path_is_blob[0], $path_is_blob[1]];

        $res = Http::attach($files)
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
        fclose($file);
        return $res;
    }

    public static function blob_base64_request($source_image_path, $isSourceImageBlob, $target_image_path, $url, $api_key) {
        // $data = ['source_image' => $source_image_path, 'target_image' => $target_image_path];

        if ($isSourceImageBlob) {
            $path_is_blob[0] = "source_image";
            $path_is_blob[1] = $source_image_path;

            $path_is_base64[0] = "target_image";
            $path_is_base64[1] = $target_image_path;
        } else {
            $path_is_blob[0] = "target_image";
            $path_is_blob[1] = $target_image_path;

            $path_is_base64[0] = "source_image";
            $path_is_base64[1] = $source_image_path;
        }

        $files[] = [$path_is_blob[0], $path_is_blob[1]];

        // 解码Base64数据，保存为临时图片，然后读取为文件流
        $image_data = base64_decode($path_is_base64[1]);
        $tempFile = static::writeTempFile($image_data,true);
        $files[] = [$path_is_base64[0], $file = fopen($tempFile, 'r'), static::getFileName($tempFile)];

        $res = Http::attach($files)
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

    /**
     * Verify face(s) from given base64
     */
    public static function base64_request($source_image_path, $target_image_path, $url, $api_key) {
        $data = ['source_image' => $source_image_path, 'target_image' => $target_image_path];
        // dd($data);
        return Http::asJson()
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url, $data)
                   ->throw()
                   ->json();
    }
}
