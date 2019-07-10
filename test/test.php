<?php

include_once __DIR__.'/../module/mapper.php';

$test = new Test();
$results = $test->run();
print_r($results);

echo "Test is done. Go to the application (e.g. http://localhost:20081/chaos.html) to see the results and enjoy!\n";

class Test {
    
    private $useCURL = true;
    
    public function __construct()
    {
        if (!function_exists('curl_exec')){
            $this->useCURL = false;
        }
    }
    
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
        
        $url = 'http://localhost:20080/response';
        
        if ($this->useCURL) {
            
            $ch = curl_init($url);
            $response = curl_exec($ch);
            
            // Check HTTP status code
            if (!curl_errno($ch)) {
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                if ($httpCode == 503) {
                    echo "Service unavalable\n";
                    exit;
                }
                
                return $httpCode;
            }
        } else {
            @file_get_contents($url);
            $statusLine = $http_response_header[0];
            preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
            if (isset($match[1])) {
                return $match[1];
            } else {
                echo "Service unavalable\n";
                exit;
            }
        }
    }
}
