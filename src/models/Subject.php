<?php
/**
 * @User yangyang
 * @Date 2024/4/22 12:55
 */
namespace Aoding9\CompreFace\models;

use Aoding9\CompreFace\endpoints\SubjectEndpoints;
use Aoding9\CompreFace\services\BaseService;

class Subject {
    protected $service;
    protected $url;
    protected $key;

    public function __construct(BaseService $service, $url, $key) {
        $this->service = $service;
        $this->url = $url;
        $this->key = $key;
    }

    /**
     * List the subjects
     */
    public function  list() {
        return SubjectEndpoints::list($this->url, $this->key);
    }

    /**
     * Add subject
     */
    public function add($subject) {
        return SubjectEndpoints::add($subject, $this->url, $this->key);
    }

    /**
     * Rename the subject
     */
    public function rename($presentSubjectName, $newSubjectName) {
        $url = $this->url;
        $url = `${url}/${presentSubjectName}`;
        return SubjectEndpoints::rename($newSubjectName, $url, $this->key);
    }

    /**
     * Delete a subject
     */
    public function delete($subject) {
        $url = $this->url;
        $url = `${url}/${subject}`;
        return SubjectEndpoints::deleteSubject($url, $this->key);
    }

    /**
     * Delete all subject
     */
    public function deleteAll() {
        return SubjectEndpoints::deleteSubject($this->url, $this->key);
    }
}