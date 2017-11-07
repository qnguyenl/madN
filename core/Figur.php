<?php

/**
 * Created by PhpStorm.
 * User: quynguyenlam
 * Date: 09.04.17
 * Time: 16:56
 */
class Figur
{
    protected $position;
    protected $prevPosition;
    protected $number;
    protected $ready = false;
    protected $finish = false;
    protected $spieler;

    public function __construct(Spieler $spieler,int $nummer)
    {
        $this->spieler = $spieler;
        $this->number = $nummer;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->ready;
    }

    /**
     * @param bool $ready
     */
    public function setReady(bool $ready)
    {
        $this->ready = $ready;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return Spieler
     */
    public function getSpieler(): Spieler
    {
        return $this->spieler;
    }

    /**
     * @param Spieler $spieler
     */
    public function setSpieler(Spieler $spieler)
    {
        $this->spieler = $spieler;
    }

    /**
     * @return bool
     */
    public function isFinish(): bool
    {
        return $this->finish;
    }

    /**
     * @param bool $finish
     */
    public function setFinish(bool $finish)
    {
        $this->finish = $finish;
    }

    /**
     * @return mixed
     */
    public function getPrevPosition()
    {
        return $this->prevPosition;
    }

    /**
     * @param mixed $prevPosition
     */
    public function setPrevPosition($prevPosition)
    {
        $this->prevPosition = $prevPosition;
    }
}