<?php

namespace Aoding9\CompreFace\services;

use Aoding9\CompreFace\endpoints\CommonEndpoints;

class BaseService {
    public $server;
    public $port;
    public $options;
    public $key;
    public $base_url;

    public function __construct($server, $port, $options, $key) {
        $this->server = $server;
        $this->port = $port;
        $this->options = $options;
        $this->key = $key;
    }

    /**
     * Construct full url from given parameters
     * @returns string
     */
    public function get_full_url($base_url, $server, $port) {
        return "${server}:${port}/${base_url}";
    }

    /**
     * Check url
     * @param string $image_url
     * @returns boolean
     */
    public function isUrl($image_url) {
        if (gettype($image_url) !== 'string')
            return false;
        // regex to check passed parameter is url or relative path
        $urlRegEX = "/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w.-]+)+[\w\-._~:\/?#[\]@!$&'()*+,;=]+$/u";
        $isUrl = preg_match($urlRegEX, $image_url);
        return $isUrl > 0;
    }

    /**
     * Check whether string is base64
     * @param string $image_data
     * @returns
     */
    public function isBase64($image_data) {
        if (gettype($image_data) !== 'string')
            return false;
        $isBase64 = base64_encode(base64_decode($image_data))===$image_data;
        return $isBase64 > 0;
    }

    /**
     * Check whether string is a path or not
     * @param string $path
     * @returns
     */
    public function isPathRelative($path) {
        if (gettype($path) !== 'string')
            return false;
        return is_file($path);
    }

    /**
     * Add extra options to url
     * @param string $url
     * @param array  $globalOptions
     * @param array  $localOptions
     * @param array  $required_parameters
     * @returns string
     */
    public function add_options_to_url($url, $globalOptions, $localOptions, $required_parameters) {
        // todo:这里不确定是哪个覆盖哪个，先按sdk里面的全局覆盖局部选项
        $uniqueOptions = array_merge($localOptions, $globalOptions);
        // $uniqueOptions =array_merge($globalOptions,$localOptions);

        $isThereAnyOptions = array_keys($uniqueOptions);
        $isLimitOptionExist = false;

        // check whether any parameters passed
        if (count($isThereAnyOptions) > 0) {
            // check limit parameter passed, and it is allowed for particular endpoint (ex: it is not requrid for add())
            if (isset($uniqueOptions['limit']) && $uniqueOptions['limit'] >= 0 && isset($required_parameters['limit']) && $required_parameters['limit']) {
                $isLimitOptionExist = true;
                $url = "${url}?limit=${uniqueOptions['limit']}";
            }

            $separator = $isLimitOptionExist ? '&' : '?';

            // check det_prob_threshold parameter passed and is it allowed for particular endpoint
            if (isset($uniqueOptions['det_prob_threshold']) && $uniqueOptions['det_prob_threshold'] >= 0 && isset($required_parameters['det_prob_threshold'])) {
                $url = "${url}${separator}det_prob_threshold=${uniqueOptions['det_prob_threshold']}";
            }

            // check prediction_count passed and is it allowed for particular endpoint
            if (isset($uniqueOptions['prediction_count']) && $uniqueOptions['prediction_count'] >= 0 && isset($required_parameters['prediction_count'])) {
                $url = "${url}${separator}prediction_count=${uniqueOptions['prediction_count']}";
            }

            // check face_plugins passed and is it allowed for particular endpoint
            if (isset($uniqueOptions['face_plugins']) && $uniqueOptions['face_plugins'] && isset($required_parameters['face_plugins'])) {
                $url = "${url}${separator}face_plugins=${uniqueOptions['face_plugins']}";
            }

            // check status passed and is it allowed for particular endpoint
            if (isset($uniqueOptions['status']) && $uniqueOptions['status'] && isset($required_parameters['status'])) {
                $url = "${url}${separator}status=${uniqueOptions['status']}";
            }
        }

        return $url;
    }

    /**
     * 上传图片
     * @param $image_data
     * @param $url
     * @param $api_key
     * @return array|mixed
     * @throws \Illuminate\Http\Client\RequestException
     * @Date 2024/4/20 12:38
     */
    public function upload($image_data, $url, $api_key) {
        if ($this->isUrl($image_data)) {
            return CommonEndpoints::upload_url($image_data, $url, $api_key);
        } else if ($this->isBase64($image_data)) {
            return CommonEndpoints::upload_base64($image_data, $url, $api_key);
        } else if ($this->isPathRelative($image_data)) {
            return CommonEndpoints::upload_path($image_data, $url, $api_key);
        }
        return CommonEndpoints::upload_blob($image_data, $url, $api_key);
    }
}