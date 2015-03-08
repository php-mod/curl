<?php

namespace Curl;

class CurlTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $this->assertTrue(true);
    }
    public function bFunction()
    {
        // create both cURL resources
        $ch1 = new Curl();
        $ch2 = new Curl();

        // set URL and other appropriate options
        $ch1
            ->setUrl("http://www.example.com/")
            ->includeHeader(false);
        $ch2
            ->setUrl("http://www.php.net/")
            ->includeHeader(false);

        //create the multiple cURL handle
        $mh = new MultiCurl();

        //add the two handles
        $mh
            ->add($ch1)
            ->add($ch2);

        $active = null;
        // execute the handles
        do {
            $mrc = curl_multi_exec($mh->getMultiHandle(), $active);

        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            $s = curl_multi_select($mh->getMultiHandle());
            if ($s != -1) {

                do {
                    $mrc = curl_multi_exec($mh->getMultiHandle(), $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        //close the handles
        unset($mh);
    }

    public function curl_multi_add_handle()
    {
        // create both cURL resources
        $ch1 = new Curl();
        $ch2 = new Curl();

        // set URL and other appropriate options
        $ch1
            ->setUrl("http://www.example.com/")
            ->includeHeader(false);
        $ch2
            ->setUrl("http://www.php.net/")
            ->includeHeader(false);

        //create the multiple cURL handle
        $mh = new MultiCurl();

        //add the two handles
        $mh
            ->add($ch1)
            ->add($ch2);

        $running = null;
        //execute the handles
        do {
            curl_multi_exec($mh->getMultiHandle(),$running);
        } while($running > 0);

        //close all the handles
        unset($mh);
    }
}
