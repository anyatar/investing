<?php

class Mapper
{
    
    private static $pdo = null;
    private $jsonData = [   'normal'    => ['200' => 100],
                            'degraded'  => ['200' => 50, '401' => 25, '500' => 25],
                            'failure'   => ['200' => 5, '500' => 95]
                        ];
    
    public function __construct()
    {
        $iniData  = $this->parseIni();
        $this->parseJson();
        
        try {
            self::$pdo = new PDO('sqlite:' . __DIR__ . "/{$iniData['sqliteFile']}");
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $this->error("Failed to connect to SQLite db: " . $e->getMessage());
        }
    }
    
    public function setMode($mode)
    {
        $stmt = "REPLACE INTO settings (name, value) values ('mode', ?)";
        self::$pdo->prepare($stmt)->execute([$mode]);
    }
    
    public function getMode()
    {
        $stmt = self::$pdo->query("SELECT value FROM settings where name=\"mode\"");
        $row = $stmt->fetch();
        if (isset($row['value'])) {
            if (array_key_exists($row['value'], $this->jsonData)) {
                return $row['value'];
            } else {
                // the configuration was changed - reset mode, clean results
                $this->deleteResults();
            }
        } 
        
        // set the default mode
        $defaultMode = key($this->jsonData);
        $this->setMode($defaultMode);
        return $defaultMode;
    }
    
    public function deleteMode()
    {
        self::$pdo->exec("DELETE from settings where name = \"mode\" ");
    }
    
    public function getModes()
    {
        return array_keys($this->jsonData);
    }
    
    public function getModeSettings()
    {
        return $this->jsonData[$this->getMode()] ?? false;
    }
    
    public function saveResults($results)
    {
        $query = "REPLACE INTO settings (name, value) values ('results', ?)";
        
        $jsonResults = json_encode($results);
        if ($jsonResults !== false) {
            self::$pdo->prepare($query)->execute([$jsonResults]);
        }
    }
    
    public function getResults()
    {
        $stmt = self::$pdo->query("SELECT value FROM settings where name=\"results\"");
        $row = $stmt->fetch();
        return $row['value'] ?? '';
    }
    
    public function deleteResults()
    {
        self::$pdo->exec("DELETE from settings where name = \"results\" ");
    }
    
    private function error($error)
    {
        throw new Exception($error);
    }
    
    private function parseIni()
    {
        $iniData = parse_ini_file('config.ini');
        
        if (!isset($iniData['sqliteFile'])) {
            $this->error("DB configuration is not full");
        }
        
        return $iniData;
    }
    
    private function parseJson()
    {
        $jsonFile = __DIR__ . '/config.json';
        if (!file_exists($jsonFile)) return;
        $jsonStr = file_get_contents($jsonFile);
        $jsonData = json_decode($jsonStr, true);
        if ($jsonData) {
            $this->jsonData = $jsonData;
        }
    }
    
}
