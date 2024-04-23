<?php
namespace Aoding9\CompreFace\endpoints;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class VerificationEndpoints {
    /**
     * Verify face(s) from given image
     */
    public static function verify_face_request($source_image_path, $target_image_path, $url, $api_key) {
        return Http::attach([
                                ['source_image', file_get_contents($source_image_path)],
                                ['target_image', file_get_contents($target_image_path)],
                            ])
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
    }

    /**
     * Verify face(s) from given image urls
     */
    public static function both_url_request($source_image_url, $target_image_url, $url, $api_key, $file_name = 'example') {
        /** @var Response[] $res */
        $res = Http::pool(function(Pool $pool) use ($source_image_url, $target_image_url) {
            $pool->get($source_image_url)->throw();
            $pool->get($target_image_url)->throw();
        });
        // $source_image_extention = preg_split('/', $res[0]->header('content-type'))[1];
        // $target_image_extention = preg_split('/', $res[1]->header('content-type'))[1];
        return Http::attach([
                                ['source_image', $res[0]->body()],
                                ['target_image', $res[1]->body()],
                            ])
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
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

        $files[] = [$path_is_relative[0], $path_is_relative[1]];

        $res = Http::get($path_is_url[1])->throw();

        $files[] = [$path_is_url[0], $res->body()];

        return Http::attach($files)
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();
    }

    /**
     * Verify face(s) from given blob data
     */
    public static function url_blob_request($source_image_path, $isSourceImageUrl, $target_image_path, $url, $api_key){
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

        $res = Http::get($path_is_url[1])->throw();

        $files[] = [$path_is_url[0], $res->body()];

        return Http::attach($files)
                   ->withHeaders([
                                     "x-api-key" => $api_key,
                                 ])
                   ->post($url)
                   ->throw()
                   ->json();



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
                            ['source_image',$source_image_blob],
                            ['target_image',$target_image_blob],
                        ])
               ->withHeaders([
                                 "x-api-key" => $api_key,
                             ])
               ->post($url)
               ->throw()
               ->json();

    }

   public static function one_blob_request($source_image_path, $isSourceImageBlob, $target_image_path, $url, $api_key){

        $path_is_blob = [];
        $path_is_relative = [];

        if ($isSourceImageBlob) {
            $path_is_blob[0] = "source_image";
            $path_is_blob[1] = $source_image_path;

            $path_is_relative[0] = "target_image";
            $path_is_relative[1] = $target_image_path;
        } else {
            $path_is_blob = "target_image";
            $path_is_blob[1] = $target_image_path;

            $path_is_relative = "source_image";
            $path_is_relative[1] = $source_image_path;
        }
       $files[] = [$path_is_relative[0], $path_is_relative[1]];


       $files[] = [$path_is_blob[0], $path_is_blob[1]];

       return Http::attach($files)
                  ->withHeaders([
                                    "x-api-key" => $api_key,
                                ])
                  ->post($url)
                  ->throw()
                  ->json();

    }

    /**
     * Verify face(s) from given base64
     */
   public static function base64_request($source_image_path, $target_image_path, $url, $api_key){
       $data = ['source_image'=>$source_image_path,'target_image'=>$target_image_path];
       return Http::asJson()
                  ->withHeaders([
                                    "x-api-key" => $api_key,
                                ])
                  ->post($url, $data)
                  ->throw()
                  ->json();

    }


}
