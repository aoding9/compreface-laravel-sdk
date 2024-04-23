<?php
namespace Aoding9\CompreFace\services;

use Aoding9\CompreFace\endpoints\VerificationEndpoints;

class VerificationService extends BaseService {
    public function __constructor($server, $port, $options, $key) {
        parent::__construct($server, $port, $options, $key);
        $this->base_url = 'api/v1/verification/verify';
    }

    public function verify($source_image_path, $target_image_path, $options) {
        // add extra parameter(s) name with true value if it is referenced in API documentation for particular endpoint
        // add_options_to_url() adds this parameter to url if user passes some value as option otherwise function ignores this parameter
        $required_url_parameters = [
            'limit'              => true,
            'det_prob_threshold' => true,
            'face_plugins'       => true,
            'status'             => true,
        ];

        $full_url = $this->get_full_url($this->base_url, $this->server, $this->port);
        // add parameters to basic url
        $url = $this->add_options_to_url($full_url, $this->options, $options, $required_url_parameters);

        $isSourceImageUrl = $this->isUrl($source_image_path);
        $isTargetImageUrl = $this->isUrl($target_image_path);

        $isSourceRelativePath = $this->isPathRelative($source_image_path);
        $isTargetRelativePath = $this->isPathRelative($target_image_path);

        $isSourceBase64 = $this->isBase64($source_image_path);
        $isTargetBase64 = $this->isBase64($target_image_path);

        if ($isSourceImageUrl) {
            if ($isTargetImageUrl) {
                return VerificationEndpoints::both_url_request($source_image_path, $target_image_path, $url, $this->key);
            } else if ($isTargetRelativePath) {
                return VerificationEndpoints::one_url_request($source_image_path, $isSourceImageUrl, $target_image_path, $url, $this->key);
            } else {
                return VerificationEndpoints::url_blob_request($source_image_path, $isSourceImageUrl, $target_image_path, $url, $this->key);
            }
        } else if ($isSourceRelativePath) {
            if ($isTargetImageUrl) {
                return VerificationEndpoints::one_url_request($source_image_path, $isSourceImageUrl, $target_image_path, $url, $this->key);
            } else if ($isTargetRelativePath) {
                return VerificationEndpoints::verify_face_request($source_image_path, $target_image_path, $url, $this->key);
            } else {
                return VerificationEndpoints::one_blob_request($source_image_path, false, $target_image_path, $url, $this->key);
            }
        } else if ($isSourceBase64) {
            return VerificationEndpoints::base64_request($source_image_path, $target_image_path, $url, $this->key);
        } else {
            if ($isTargetImageUrl) {
                return VerificationEndpoints::url_blob_request($source_image_path, $isSourceImageUrl, $target_image_path, $url, $this->key);
            } else if ($isTargetRelativePath) {
                return VerificationEndpoints::one_blob_request($source_image_path, true, $target_image_path, $url, $this->key);
            } else {
                return VerificationEndpoints::both_blob_request($source_image_path, $target_image_path, $url, $this->key);
            }
        }
    }
}
