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
        return `${server}:${port}/${base_url}`;
    }

    /**
     * Check url
     * @param string $image_url
     * @returns boolean
     */
    public function isUrl($image_url) {
        // regex to check passed parameter is url or relative path
        $urlRegEX = "%^(?:http(s)?://)?[\w.-]+(?:\.[\w.-]+)+[\w\-._~:/?#[\]@!\$&'()*+,;=]+$%";
        $isUrl = preg_match($urlRegEX, $image_url);
        return $isUrl > 0;
    }

    /**
     * Check whether string is base64
     * @param string $image_data
     * @returns
     */
    public function isBase64($image_data) {
        $base64regex = "%^([A-Za-z0-9+/]{4})*([A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==)?$%";
        $isBase64 = preg_match($base64regex, $image_data);

        return $isBase64 > 0;
    }

    /**
     * Check whether string is relative path or not
     * @param string $path
     * @returns
     */
    public function isPathRelative($path) {
        if (gettype($path) !== 'string')
            return false;
        $isAbsolute = preg_match("/^([A-Za-z]:|\.)/", $path);

        return $isAbsolute > 0;
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
        // merge options passed by localy and globally NOTE: global options will override local on if same value passed from both of them
        $uniqueOptions = [...$localOptions, ...$globalOptions];
        $isThereAnyOptions = array_keys($uniqueOptions);
        $isLimitOptionExist = false;

        // check whether any parameters passed
        if (count($isThereAnyOptions) > 0) {
            // check limit parameter passed, and it is allowed for particular endpoint (ex: it is not requrid for add())
            if ($uniqueOptions['limit'] >= 0 && $required_parameters['limit']) {
                $isLimitOptionExist = true;
                $url = `${url}?limit=${uniqueOptions['limit']}`;
            }

            // check det_prob_threshold parameter passed and is it allowed for particular endpoint
            if ($uniqueOptions['det_prob_threshold'] >= 0 && $required_parameters['det_prob_threshold']) {
                $url = `${url}${isLimitOptionExist ? '&' : '?'}det_prob_threshold=${uniqueOptions['det_prob_threshold']}`;
            }

            // check prediction_count passed and is it allowed for particular endpoint
            if ($uniqueOptions['prediction_count'] >= 0 && $required_parameters['prediction_count']) {
                $url = `${url}${isLimitOptionExist ? '&' : '?'}prediction_count=${uniqueOptions['prediction_count']}`;
            }

            // check face_plugins passed and is it allowed for particular endpoint
            if ($uniqueOptions['face_plugins'] && $required_parameters['face_plugins']) {
                $url = `${url}${isLimitOptionExist ? '&' : '?'}face_plugins=${uniqueOptions['face_plugins']}`;
            }

            // check status passed and is it allowed for particular endpoint
            if ($uniqueOptions['status'] && $required_parameters['status']) {
                $url = `${url}${isLimitOptionExist ? '&' : '?'}status=${uniqueOptions['status']}`;
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
        //     const{ isUrl, isPathRelative, isBase64 } = common_functions;
        // const { upload_blob, upload_path, upload_url, upload_base64 } = common_endpoints;

        $imageFromUrl = $this->isUrl($image_data);
        $imageFromPath = $this->isPathRelative($image_data);
        $imageFromBase64 = $this->isBase64($image_data);

        if ($imageFromUrl) {
            return CommonEndpoints::upload_url($image_data, $url, $api_key);
        } else if ($imageFromBase64) {
            return CommonEndpoints::upload_base64($image_data, $url, $api_key);
        } else if ($imageFromPath) {
            return CommonEndpoints::upload_path($image_data, $url, $api_key);
        }
        return CommonEndpoints::upload_blob($image_data, $url, $api_key);
    }


}