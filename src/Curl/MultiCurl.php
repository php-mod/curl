<?php

namespace Curl;

class MultiCurl
{
    private $multiHandle;

    /**
     * @var Curl[]
     */
    private $handles = [];

    /**
     * Initialize a new cURL multi handle
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->multiHandle = curl_multi_init();
        if($this->multiHandle === false) {
            throw new \Exception('Failure');
        }
    }

    /**
     * Add a normal cURL handle to a cURL multi handle
     * @see http://php.net/manual/en/function.curl-multi-add-handle.php
     * @param Curl $ch
     *
     * @return $this
     * @throws \Exception
     */
    public function add(Curl $ch)
    {
        $return = curl_multi_add_handle($this->multiHandle, $ch->getHandle());
        if($return !== 0) {
            throw new \Exception('Error ' . $return);
        }
        $this->handles[] = $ch;

        return $this;
    }

    /**
     * Remove a multi handle from a set of cURL handles
     * @param Curl $ch
     * @see http://php.net/manual/en/function.curl-multi-remove-handle.php
     * @return $this
     * @throws \Exception
     */
    public function remove(Curl $ch)
    {
        if(!in_array($ch, $this->handles)) {
            throw new \Exception('Curl handle not added to this multi session.');
        }
        $this->safeRemove($ch);
        unset($this->handles[array_search($ch, $this->handles)]);
        return $this;
    }

    public function __destruct()
    {
        echo 'Destruct multi: [OK]' . PHP_EOL;
        foreach($this->handles as $handle) {
            $this->safeRemove($handle);
        }
        curl_multi_close($this->multiHandle);
    }

    /**
     * @param Curl $ch
     *
     * @throws \Exception
     */
    private function safeRemove(Curl $ch)
    {
        $return = curl_multi_remove_handle($this->multiHandle, $ch->getHandle());
        if($return !== 0) {
            throw new \Exception('Error occurred: ' . $return);
        }
    }

    /**
     * Run the sub-connections of the current cURL handle
     */
    public function exec()
    {
        // TODO
    }

    /**
     * @return resource
     */
    public function getMultiHandle()
    {
        return $this->multiHandle;
    }
}
