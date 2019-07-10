<?php

define("BASE_URL", "http://localhost:20081");

include_once __DIR__.'/../module/mapper.php';

$test = new Test();
$results = $test->run();
print_r($results);

echo "Test is done. Go to the application (e.g. " . BASE_URL . "/chaos.html) to see the results and enjoy!\n";

class Test {
    
    public function run() {
        
        $results = [];
        
        for ($i=0; $i<100; $i++) {
            
            $httpCode = $this->getResponseCode();
            
            if (!isset($results[$httpCode])) {
                $results[$httpCode] = array('status' => $httpCode, 'count' => 1);
            } else {
                $results[$httpCode]['count']++;
            }
        }
        
        $mapper = new Mapper();
        $mapper->saveResults($results);
        
        return $results;
    }
    
    private function getResponseCode() {
        
        $url = BASE_URL . '/response';
        
        @file_get_contents($url);
        $statusLine = $http_response_header[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
        if (isset($match[1])) {
            return $match[1];
        } else {
            echo "Error accessing $url\n";
            exit;
        }
    }
}
