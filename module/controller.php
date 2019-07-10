<?php

include_once 'mapper.php';

class Controller 
{
    private $mapper;
    
    public function __construct()
    {
        $this->mapper = new Mapper();
    }
    
    public function setMode()
    {
        $payload = [];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) {
            $postParams = file_get_contents('php://input');
            if ($postParams) {
                $payload = json_decode($postParams, true);
            }
        }
        
        $mode = $payload['mode'] ?? null;
        
        $this->validateMode($mode);
        
        try {
            $this->mapper->setMode($mode);
            $this->mapper->deleteResults();
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
        
        $this->returnBoolResponse(true, '');
    }
    
    public function getModes()
    {
       $modes = $this->mapper->getModes();
       $this->returnJsonResponse($modes);
    }
    
    public function getMode()
    {        
       $mode = $this->mapper->getMode();
       $this->returnJsonResponse( ['mode' => $mode] );
    }
    
    public function getResponse()
    {
       $modes = $this->mapper->getModeSettings();
       if ($modes === false) {
           // clean the incorrect mode
           $mode = $this->mapper->getMode();
           $this->mapper->deleteMode();
           throw new Exception("No settings for the mode: \"$mode\". ");
       }
       
       $random = random_int(1, 100);
       foreach ($modes as $status => $probability) {
           if ($random <= $probability) {
               http_response_code($status);
               exit;
           }
           $random -= $probability;
       }
       
    }
    
    public function deleteMode()
    {
        $this->mapper->deleteMode();
    }
    
    public function getResults()
    {
        $this->returnJsonResponse($this->mapper->getResults(), false);
    }
    
    protected function validateMode($mode)
    {
        if (empty($mode)) {
            $this->error('No mode specified');
        }
        
        if (!in_array($mode, $this->mapper->getModes())) {
            $this->error('Invalid mode specified');
        }
    }
    
    private function error($error)
    {
        $this->returnBoolResponse(false, $error);
        exit;
    }
    
    private function returnBoolResponse($success, $error)
    {
        $this->returnJsonResponse( ['success' => $success, 'error' => $error] );
    }
    
    private function returnJsonResponse($response, $encode=true)
    {
        header('Content-Type: application/json');
        echo ($encode ? json_encode($response) : $response);
    }
   
}
