<?php
namespace Aoding9\CompreFace\services;

class DetectionService extends BaseService {
    public function __construct($server, $port, $options, $key) {
        parent::__construct($server, $port, $options, $key);
        $this->base_url = 'api/v1/detection/detect';
    }

    /**
     * Detect faces from given image
     * @returns
     */
    public function detect($image_path, $localOptions) {
        // add_options_to_url() adds this parameter to url if user passes some value as option otherwise function ignores this parameter
        $required_url_parameters = [
            'limit'              => true,
            'det_prob_threshold' => true,
            'face_plugins'       => true,
            'status'             => true,
        ];
        $full_url = $this->get_full_url($this->base_url, $this->server, $this->port);
        // add parameters to basic url
        $url = $this->add_options_to_url($full_url, $this->options, $localOptions, $required_url_parameters);

        return $this->upload($image_path, $url, $this->key);
    }
}