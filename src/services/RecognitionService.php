<?php
namespace Aoding9\CompreFace\services;

// import { recognition_endpoints } from '../endpoints/recognition_endpoints.js';
// import { common_functions } from '../functions/index.js';
// import { subject_endpoints } from '../endpoints/subject_endpoints.js';
// import { upload } from '../endpoints/upload.js';

use Aoding9\CompreFace\models\FaceCollection;
use Aoding9\CompreFace\endpoints\RecognitionEndpoints;
use Aoding9\CompreFace\models\Subject;

class RecognitionService extends BaseService {
    protected string $recognize_base_url;
    protected $faceCollection;
    protected $subject;

    public function __constructor($server, $port, $options, $key) {
        parent::__construct($server, $port, $options, $key);
        $this->base_url = 'api/v1/recognition/faces';
        $this->recognize_base_url = "api/v1/recognition/recognize";
    }

    /**
     * Recognize face(s) from given image
     */
    public function recognize($image_path, $options) {
        // add_options_to_url() adds this parameter to url if user passes some value as option otherwise function ignores this parameter
        $required_url_parameters = [
            'limit'              => true,
            'det_prob_threshold' => true,
            'prediction_count'   => true,
            'face_plugins'       => true,
            'status'             => true,
        ];

        if (!array_key_exists('limit', $options)) {
            $options ['limit'] = 0;
        }

        // add parameters to basic url
        $full_url = $this->get_full_url($this->recognize_base_url, $this->server, $this->port);
        $url = $this->add_options_to_url($full_url, $this->options, $options, $required_url_parameters);

        return $this->upload($image_path, $url, $this->key);
    }

    /**
     * Contains functions related to face collection
     */

    public function getFaceCollection() {
        if ($this->faceCollection) {
            return $this->faceCollection;
        }
        $url = $this->get_full_url($this->base_url, $this->server, $this->port);
        $key = $this->key;
        return $this->faceCollection = new FaceCollection($this, $url, $key);
    }

    public function getSubjects() {
        if ($this->subject) {
            return $this->subject;
        }
        $base_subject_url = preg_replace('faces', 'subjects', $this->base_url);
        $url = $this->get_full_url($base_subject_url, $this->server, $this->port);
        $key = $this->key;
        return $this->subject = new Subject($this, $url, $key);
    }
}