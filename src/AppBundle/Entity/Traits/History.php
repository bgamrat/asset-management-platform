<?php

Namespace AppBundle\Entity\Traits;

trait History
{
    private $history;

    public function getHistory()
    {
        return $this->history;
    }

    public function setHistory( $history )
    {
        $this->history = $history;
    }
}