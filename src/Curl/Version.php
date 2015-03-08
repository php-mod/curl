<?php

namespace Curl;

class Version
{
    /**
     * @var array
     */
    private $version;

    /**
     * @param array $version
     */
    public function __construct(array $version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function version()
    {
        return $this->version['version'];
    }
}
