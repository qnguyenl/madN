<?php

/**
 * Created by PhpStorm.
 * User: quynguyenlam
 * Date: 09.04.17
 * Time: 17:13
 */
class Spieler
{
    protected $figurs;
    protected $name;
    protected $ecke;
    protected $startPos;
    protected $zielPos;

    public function __construct($name)
    {
        $this->figurs = [new Figur($this,1),new Figur($this,2),new Figur($this,3), new Figur($this,4)];
        $this->name = $name;
    }

    public function moveFigur(int $diceResult, int $feldAnzahl)
    {
        $prioFigurs = $this->getPrioFigurs();
        if(count($prioFigurs) == 0){
            throw new Exception("Die Spiele sollte geendet. Der Spieler {$this->getName()} hat Ziel erreicht.",0);
        }
        foreach ($prioFigurs as $figur){
            $prevPos = $figur->getPosition();
            if($figur->isReady()) {
                $absolutePos = $figur->getPosition() + $diceResult;
                $newPos = $absolutePos >= $feldAnzahl ? $absolutePos - $feldAnzahl : $absolutePos;
            }else{
                if($diceResult == 6){
                    $newPos = $this->getStartPos();
                }
            }
            if (isset($newPos) && !$this->hasFigurAtPos($newPos)) {
                if (!$figur->isReady()) {
                    $figur->setPrevPosition($prevPos);
                    $figur->setPosition($newPos);
                    $figur->setReady(true);
                    return $figur;
                } else {
                    $abstand = $this->getZielPos() - $figur->getPosition();
                    if ($abstand > 0 && $abstand <= 6) {
                        if ($newPos == $this->getZielPos()) {
                            $figur->setPrevPosition($prevPos);
                            $figur->setPosition($newPos);
                            $figur->setReady(true);
                            $figur->setFinish(true);
                            return $figur;
                        }
                    } else {
                        $figur->setPrevPosition($prevPos);
                        $figur->setPosition($newPos);
                        $figur->setReady(true);
                        return $figur;
                    }
                }
            }
        }
        return false;
    }

    public function hasFigurAtPos(int $pos)
    {
        foreach ($this->figurs as $figur){
            if($figur->getPosition() === $pos){
                return true;
            }
        }
        return false;
    }

    public function getPrioFigurs(){
        $notReadyFigurs = $this->getNotReadyFigurs();
        if(count($notReadyFigurs) == 4){
            return $notReadyFigurs;
        }elseif (count($notReadyFigurs)>=1){
            $readyFigurs = $this->getReadyFigurs();
            $tmp = [];
            foreach ($readyFigurs as $figur){
                if($figur->getPosition() == $this->startPos){
                    array_unshift($tmp,$figur);
                }else{
                    $tmp[] = $figur;
                }
            }
            return array_merge($notReadyFigurs,$tmp);
        }else{
            return $this->getReadyFigurs();
        }
    }

    public function getNotFinishFigurs()
    {
        $notFinishFigur = array_values(array_filter($this->figurs, function(Figur $figur){
            return !$figur->isFinish();
        }));
        if(count($notFinishFigur) == 0){
            throw new Exception("Die Spiele sollte geendet. Der Spieler {$this->getName()} hat Ziel erreicht.",0);
        }
        return $notFinishFigur;
    }

    public function getReadyFigurs()
    {
        $notFinishFigurs = $this->getNotFinishFigurs();
        $readyFigurs = array_values(array_filter($notFinishFigurs,function(Figur $figur){
            return $figur->isReady();
        }));
        usort($readyFigurs,function(Figur $a, Figur $b){
           return ($a->getPosition() - $b->getPosition());
        });
        return $readyFigurs;
    }

    public function getNotReadyFigurs()
    {
        $notFinishFigur = $this->getNotFinishFigurs();
        $notReadyFigurs = array_filter($notFinishFigur, function($figur){
            return !$figur->isReady();
        });
        return $notReadyFigurs;
    }

    public function setStartUndZiel(int $start, int $ziel)
    {
        $this->setStartPos($start);
        $this->setZielPos($ziel);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getEcke()
    {
        return $this->ecke;
    }

    /**
     * @param mixed $ecke
     */
    public function setEcke($ecke)
    {
        $this->ecke = $ecke;
    }

    /**
     * @param $nummer
     * @return figur
     */
    public function getFigur($nummer)
    {
        if($nummer > 0 && $nummer <= 4){
            return $this->figurs[$nummer-1];
        }else{
            throw new Exception("Es gibt keine Figur mit {$nummer}.");
        }
    }

    /**
     * @param mixed $startPos
     */
    private function setStartPos($startPos)
    {
        $this->startPos = $startPos;
    }

    /**
     * @return mixed
     */
    public function getStartPos()
    {
        return $this->startPos;
    }

    /**
     * @param mixed $zielPos
     */
    private function setZielPos($zielPos)
    {
        $this->zielPos = $zielPos;
    }

    /**
     * @return mixed
     */
    public function getZielPos()
    {
        return $this->zielPos;
    }
}