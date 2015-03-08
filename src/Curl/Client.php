<?php

namespace Curl;

class Client
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @var string[]
     */
    private $parsedHeaders = null;

    /**
     * @param $url
     */
    public function __construct($url = null)
    {
        $this->curl = new Curl($url);
        $this->curl
            ->returnTransfer()
            ->setUserAgent('libcurl-' . Curl::version()->version() . '/php-' . PHP_VERSION)
            ->setHeaderFunction([
                $this,
                'headerCallback'
            ])->followLocation();
    }

    public function headerCallback($handle, $header)
    {
        if ($handle !== $this->curl->getHandle()) {
            throw new Exception('Error occurred.');
        }
        $this->headers[] = trim($header);

        return strlen($header);
    }

    public function get($uriParams = [])
    {
        if (is_array($uriParams)) {
            $uriParams = '?' . http_build_query($uriParams);
        }
        elseif (!is_string($uriParams)) {
            throw new Exception('Unsupported variable type');
        }

        $this->curl->setUrl($this->curl->getUrl() . $uriParams);

        return $this->curl->execute();
    }

    public function getEffectiveUrl()
    {
        return $this->curl->getEffectiveUrl();
    }

    public function post(array $data = [], $multiPartFormData = false)
    {
        return $this->curl->post()->setPostFields($data, $multiPartFormData)->execute();
    }

    public function put(array $data = [])
    {
        return $this->curl->setCustomRequest('PUT')->setPostFields($data)->execute();
    }

    public function patch(array $data = [])
    {
        return $this->curl->setCustomRequest('PATCH')->execute();
    }

    public function delete(array $data = [])
    {
        $this->curl->setUrl($this->getBaseUrl() . '?' . http_build_query($data));

        return $this->curl->setCustomRequest('DELETE')->execute();
    }

    public function getBaseUrl()
    {
        $info = parse_url($this->curl->getUrl());

        return $info['scheme'] . '://' . $info['host'] . ':' . $info['port'] . '/';
    }

    public function head(array $data = [])
    {
        return $this->curl
            ->setUrl($this->getBaseUrl() . '?' . http_build_query($data))
            ->setCustomRequest('HEAD')
            ->noBody()
            ->execute();
    }

    public function options(array $data = [])
    {
        $this->curl->setUrl($this->getBaseUrl() . '?' . http_build_query($data));

        return $this->curl->setCustomRequest('OPTIONS')->execute();
    }

    /**
     * @return Curl
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * @param      $filePath
     * @param null $size
     *
     * @return mixed
     * @throws Exception
     */
    public function putFile($filePath, $size = null)
    {
        $resource = fopen($filePath, 'r');

        $response = $this->curl->put($resource, $size ? $size : filesize($filePath))->execute();

        fclose($resource);

        return $response;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $key
     *
     * @return null|string
     */
    public function getHeader($key)
    {
        if ($this->parsedHeaders === null) {
            $this->parsedHeaders = [];
            foreach ($this->headers as $header) {
                $parts = explode(':', $header, 2);
                if (count($parts) == 2) {
                    $this->parsedHeaders[ strtolower($parts[0]) ] = trim($parts[1]);
                }
            }
        }
        $key = strtolower($key);
        if (isset($this->parsedHeaders[ $key ])) {
            return $this->parsedHeaders[ $key ];
        }

        return null;
    }

    /**
     * @param $url
     * @param $filePath
     *
     * @return $this
     * @throws Exception
     */
    public function download($url, $filePath)
    {
        $fp = fopen($filePath, 'w+');
        $this->curl->setUrl($url)->setTimeout(50)->setFile($fp)->followLocation()->execute();
        fclose($fp);

        return $this;
    }

    public function getStatus()
    {
        return $this->curl->getHttpCode();
    }

    public function getStatusMessage()
    {
        $header = $this->headers[0];
        $parts  = explode(' ', $header);

        return $parts[2];
    }

    public function setBasicAuthentication($username, $password)
    {
        $this->curl->setHttpAuth(CURLAUTH_BASIC)->setUserAndPassword($username, $password);

        return $this;
    }

    public function setDigestAuthentication($username, $password)
    {
        $this->curl->setHttpAuth(CURLAUTH_DIGEST)->setUserAndPassword($username, $password);

        return $this;
    }

    public function setReferer($referer)
    {
        $this->curl->setReferer($referer);

        return $this;
    }

    public function setCookie($key, $value)
    {
        $this->curl->setCookie($key, $value);

        return $this;
    }

    public function setCookieFile($file)
    {
        $this->curl->setCookieFile($file);

        return $this;
    }

    public function setCookieJar($cookieJar)
    {
        $this->curl->setCookieJar($cookieJar);

        return $this;
    }

    public function __destruct()
    {
        unset($this->curl);
    }

    public function close()
    {
        $this->curl->close();
    }

    public function setTimeout($time)
    {
        $this->curl->setTimeout($time);
    }

    public function setConnectionTimeout($time)
    {
        if (is_float($time)) {
            $time *= 1000;
            $this->curl->setConnectTimeoutMs((int)round($time));
        }
        else {
            $this->curl->setConnectTimeout($time);
        }
    }
}
