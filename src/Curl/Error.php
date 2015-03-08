<?php

namespace Curl;

class Error
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
