<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.25.
 * Time: 10:42
 */

namespace ShopMoves\UnasMigrationBundle\Utils;


class TimestampProvider
{
    protected $timeStamp;

    public function setTimetamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;
    }

    public function getTimestamp()
    {
        return $this->timeStamp;
    }

    public function createTimestamp()
    {
        $this->setTimetamp(date('YmdHis'));
    }
}