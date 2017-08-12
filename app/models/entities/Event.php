<?php

/**
 * Class Event for storing Google calendar event data.
 * Implements 'JsonSerializable' to allow it's instance serialized by json_encode.
 */
class Event implements JsonSerializable
{

    private $title;
    private $start;
    private $end;
    private $allDay = false;

    public function __construct($title, $start, $end)
    {
        $this->title = $title;
        $this->start = $start;
        $this->end = $end;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }

}