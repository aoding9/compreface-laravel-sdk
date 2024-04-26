<?php

namespace Aoding9\CompreFace;

use Aoding9\CompreFace\services\DetectionService;
use Aoding9\CompreFace\services\RecognitionService;
use Aoding9\CompreFace\services\VerificationService;

class CompreFace  {
    protected $port;
    protected $options;
    protected $server;

    public function __construct($server, $port, $options) {
        $this->server = $server;
        $this->port = $port;
        $this->options = $options;
    }

    public function  initFaceRecognitionService($api_key){
        return new RecognitionService($this->server, $this->port, $this->options, $api_key);
    }

    public function  initFaceVerificationService($api_key){
        return new VerificationService($this->server, $this->port, $this->options, $api_key);
    }

    public function  initFaceDetectionService($api_key){
        return new DetectionService($this->server, $this->port, $this->options, $api_key);
    }
}
