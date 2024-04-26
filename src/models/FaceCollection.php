<?php
/**
 * @User yangyang
 * @Date 2024/4/22 11:59
 */
namespace Aoding9\CompreFace\models;

use Aoding9\CompreFace\endpoints\RecognitionEndpoints;
use Aoding9\CompreFace\services\BaseService;

class FaceCollection {
    protected $service;
    protected $url;
    protected $key;

    public function __construct(BaseService $service, $url, $key) {
        $this->service = $service;
        $this->url = $url;
        $this->key = $key;
    }

    /**
     * View the list of images in face collection
     * @returns {Promise}
     */
    public function  list() {
        return RecognitionEndpoints::list_request($this->url, $this->key);
    }

    /**
     * Add image (with subject) to face collection
     */
    public function add($image_path, $subject, $options) {
        // add_options_to_url() adds this parameter to url if user passes some value as option otherwise function ignores this parameter
        $required_url_parameters = [
            'det_prob_threshold' => true,
        ];

        // add parameters to basic url
        $url = $this->service->get_full_url($this->service->base_url, $this->service->server, $this->service->port);
        $url = "${url}?subject=${subject}";
        $url = $this->service->add_options_to_url($url, $this->service->options, $options, $required_url_parameters);

        return $this->service->upload($image_path, $url, $this->service->key);
    }

    /**
     * Verify face from image
     */
    public function verify($image_path, $image_id, $options) {
        // add_options_to_url() adds this parameter to url if user passes some value as option otherwise function ignores this parameter
        $required_url_parameters = [
            'limit'              => true,
            'det_prob_threshold' => true,
            'face_plugins'       => true,
            'status'             => true,
        ];

        // add parameters to basic url
        $url = $this->url;
        $url = "${$url}/${image_id}/verify";
        $url = $this->service->add_options_to_url($url, $this->service->options, $options, $required_url_parameters);

        return $this->service->upload($image_path, $url, $this->service->key);
    }

    /**
     * Delete image by id
     */
    public function delete($image_id) {
        $url = $this->url;

        $url = "${url}/${image_id}";

        return RecognitionEndpoints::delete_request($url, $this->service->key);
    }

    /**
     * Delete multiple images
     */
    public function delete_multiple_images($image_ids) {
        $url = $this->url;

        $url = "${url}/delete";

        return
            RecognitionEndpoints::delete_multiple($url, $this->service->key, $image_ids);
    }

    /**
     * Delete image by subject
     */
    public function delete_all_subject($subject) {
        $url = $this->url;

        $url = "${url}?subject=${subject}";

        return
            RecognitionEndpoints::delete_request($url, $this->service->key);
    }

    /**
     * Delete all images in face collection
     */
    public function delete_all() {
        return RecognitionEndpoints::delete_request($this->url, $this->service->key);
    }
}