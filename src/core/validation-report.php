<?php

namespace Nemesis;

class ValidationReport{

    protected $entity;
    protected $status;
    protected $data;
    protected $successes;
    protected $failures;
    protected $has_critically_failed;
    protected $entities_critical_failures;

    public function __construct(){
        $this->data = array();
        $this->entities_critical_failures = array();
        $this->successes = array();
        $this->failures = array();
        $this->has_critically_failed = false;
        $this->status = true;
    }

    public function as_array(){
        $report = array(
            'status' => $this->status,
            'successes' => $this->successes,
            'failures' => $this->failures
        );

        return $report;
    }

    public function get_status(){
        return $this->status;
    }

    public function has_critically_failed(){
        return $this->has_critically_failed;
    }

    public function for_entity($entity){
        $this->entity = $entity;
    }

    public function checkup($checkup_name, $expression){
        if(!$this->check_entity()){
            $this->has_critically_failed = true;
            $this->failure();
            return false;
        }

        if($this->has_critically_failed){
            return true;
        }

        if($this->entity_critically_failed()){
            return true;
        }

        if(!$expression){
            $this->add_failure($checkup_name);
            $this->failure();
            return false;
        }
        $this->add_success($checkup_name);
        return true;
    }

    public function critical_checkup($checkup_name, $expression){
        $result = $this->checkup($checkup_name, $expression);
        if(!$result){
            $this->has_critically_failed = true;
        }
    }

    public function entity_critical_checkup($checkup_name, $expression){
        $result = $this->checkup($checkup_name, $expression);
        if(!$result){
            $this->entities_critical_failures[] = $this->entity;
        }
    }

    private function add_failure($checkup_name){
        $this->failures[$this->entity][] = $checkup_name;
    }

    private function add_success($checkup_name){
        $this->successes[$this->entity][] = $checkup_name;
    }

    private function failure(){
        $this->status = false;
    }

    private function check_entity(){
        return !is_null($this->entity) && $this->entity != '';
    }

    private function entity_critically_failed(){
        return in_array($this->entity, $this->entities_critical_failures);
    }

}
