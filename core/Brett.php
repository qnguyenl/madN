<?php

/**
 * Created by PhpStorm.
 * User: quynguyenlam
 * Date: 09.04.17
 * Time: 16:42
 */
class Brett
{
    protected $spielers = [];
    protected $haus = [];
    protected $feld = [];
    protected $feldAnzahl;
    protected $maxSpieler;
    protected $randomApiConfig;
    protected $randomData;
    protected $currentSpieler;
    protected static $zugNumber = 0;
    public function __construct($config)
    {
        $spielers = $config['spieler'];
        $this->randomApiConfig = $config['randomApiConfig'];
        $anzahlSpieler = count($spielers);
        if($anzahlSpieler>1 && $anzahlSpieler<=6){
            if($anzahlSpieler == 2){
                //brett für 4 benutzen
                $this->feldAnzahl = 40;
                $this->maxSpieler = 4;
            }elseif ($anzahlSpieler == 3){
                //brett für 6 benutzen. Try a fair game
                $this->feldAnzahl = 48;
                $this->maxSpieler = 6;
            }elseif ($anzahlSpieler ==4){
                //brett für 4 benutzen
                $this->feldAnzahl = 40;
                $this->maxSpieler = 4;
            }else{
                //brett für 6 benutzen
                $this->feldAnzahl = 48;
                $this->maxSpieler = 6;
            }
            foreach ($spielers as $sp){
                $name = $sp['name'];
                $spieler = new Spieler($name);
                $this->haus[$name] = [];
                $this->spielers[$name] = $spieler;
            }
        }else{
            throw new Exception("Anzahl der Spieler muss min. 2 und max. 6");
        }
    }

    public function start()
    {
        $this->spielerZuordnen();
        $this->randomData = $this->getRandomData($this->randomApiConfig['url'],$this->randomApiConfig['params']);
        $spieler = current($this->spielers);
        $diceResult = $this->wurf();
        $zugLogs = [];
        while(!$this->getWinner() && $diceResult != false){
            try {
                $gezogeneFigur = $spieler->moveFigur($diceResult, $this->feldAnzahl);
                $zugLogs[] = $this->zugLog($spieler, $gezogeneFigur, $diceResult);
            }catch (Exception $e){
                if($e->getCode() == 0){
                    return $zugLogs;
                }else{
                    die($e->getMessage());
                }
            }
            if($diceResult != 6){
                $spieler = next($this->spielers);
                if($spieler === false){
                    reset($this->spielers);
                    $spieler = current($this->spielers);
                }
            }
            $diceResult = $this->wurf();
        }
        return $zugLogs;
    }

    public function zugLog($spieler, $gezogeneFigur, $diceResult)
    {
        $result = [];
        $result['diceResult'] = $spieler->getName()." hat ".$diceResult." gewurft";
        $result['gezogeneFigur'] = "Keine Bewegung";
        $result['geschlageneFigur'] = "Keine geschlagene Figur";
        if($gezogeneFigur !== false) {
            $prevPos = $gezogeneFigur->getPrevPosition();
            $newPos = $gezogeneFigur->getPosition();
            if ($this->getFigurAtPos($newPos) !== false) {
                $geschlageneFigur = $this->getFigurAtPos($newPos);
                $geschlageneFigur->setPrevPosition($newPos);
                $geschlageneFigur->setPosition(null);
                $geschlageneFigur->setReady(false);
                $result['geschlageneFigur'] = <<<EOF
                Figur {$geschlageneFigur->getNumber()} von Spieler ({$geschlageneFigur->getSpieler()->getName()}) wird geschlagen.
EOF;
            }
            if ($gezogeneFigur->isFinish()) {
                $this->haus[$spieler->getName()][] = $gezogeneFigur;
                $gezogeneFigur->setPosition(null);
                $result['gezogeneFigur'] = <<<EOF
                Figur {$gezogeneFigur->getNumber()} von Spieler ({$gezogeneFigur->getSpieler()->getName()}) hat Ziel erreicht.
EOF;
                unset($this->feld[$newPos]);
            } else {
                $this->feld[$newPos] = $gezogeneFigur;
                if ($prevPos === null) {
                    $result['gezogeneFigur'] = <<<EOF
                    Figur {$gezogeneFigur->getNumber()} von Spieler ({$gezogeneFigur->getSpieler()->getName()}) wird in Startposition ($newPos) gesetzt.
EOF;
                } else{
                    $result['gezogeneFigur'] = <<<EOF
                    Figur {$gezogeneFigur->getNumber()} von Spieler ({$gezogeneFigur->getSpieler()->getName()}) geht von $prevPos bis $newPos.
EOF;
                }
            }
            unset($this->feld[$prevPos]);
        }
        return $result;
    }

    public function wurf()
    {
        if(self::$zugNumber<count($this->randomData)) {
            $result = $this->randomData[self::$zugNumber];
            self::$zugNumber++;
            return $result;
        }
        return false;
    }

    private function getRandomData($url, array $post = NULL, array $options = array())
    {
        $defaults = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($post)
        ];
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $response = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($response,true);
        if(isset($result['error'])){
            throw new Exception("Random.org error {$result['error']['message']}",0);
        }
        return $result['result']['random']['data'];
    }

    protected function getFigurAtPos(int $positionsNummer)
    {
        if(array_key_exists($positionsNummer,$this->feld)){
            return $this->feld[$positionsNummer];
        }
        return false;
    }

    protected function spielerZuordnen()
    {
        $ecke = 1;
        if(count($this->spielers) == 2){
            foreach ($this->spielers as $spieler){
                $spieler->setEcke($ecke);
                $startPos = ($ecke - 1) * ($this->feldAnzahl / $this->maxSpieler);
                $zielPos = ($this->feldAnzahl / $this->maxSpieler) * ($this->maxSpieler - $ecke + 1) - 1;
                $spieler->setStartUndZiel($startPos,$zielPos);
                $ecke +=2;
            }
        }else{
            foreach ($this->spielers as $spieler){
                $spieler->setEcke($ecke);
                $startPos = ($ecke - 1) * ($this->feldAnzahl / $this->maxSpieler);
                $zielPos = ($startPos - 1) < 0 ? $this->feldAnzahl - 1 : $startPos - 1;
                $spieler->setStartUndZiel($startPos,$zielPos);
                $ecke++;
            }
        }
    }

    public function getWinner()
    {
        foreach($this->spielers as $name => $spieler){
            if(count($this->haus[$name]) == 4){
                return $spieler;
            }
        }
        return false;
    }

    public function getSpieler($name)
    {
        if(array_key_exists($name, $this->spielers)){
            return $this->spielers[$name];
        }else{
            throw new Exception("Es gibt keine spieler mit der Name {$name}");
        }
    }

    public function getAllSpieler()
    {
        return $this->spielers;
    }
}