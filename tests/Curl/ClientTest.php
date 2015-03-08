<?php

namespace Curl;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    private $testUrl = 'http://127.0.0.1:8000';

    public function testGetUserAgent()
    {
        $client   = new Client($this->testUrl);
        $response = json_decode($client->get());

        $this->assertEquals('libcurl-' . Curl::version()->version() . '/php-' . PHP_VERSION, $response->userAgent);
    }

    public function testGet()
    {
        $client   = new Client($this->testUrl);
        $response = json_decode($client->get());

        $this->assertEquals('GET', $response->requestMethod);
    }

    public function testUrl()
    {
        $data = ['foo' => 'bar'];

        // GET
        $client = new Client($this->testUrl);
        $client->get($data);

        $this->assertEquals($this->testUrl . '/', $client->getBaseUrl());
        $this->assertEquals($this->testUrl . '/?' . http_build_query($data), $client->getEffectiveUrl());

        // POST
        $client = new Client($this->testUrl);
        $client->post($data);

        $this->assertEquals($this->testUrl . '/', $client->getBaseUrl());
        $this->assertEquals($this->testUrl . '/', $client->getEffectiveUrl());

        // PUT
        $client = new Client($this->testUrl);
        $client->put($data);

        $this->assertEquals($this->testUrl . '/', $client->getBaseUrl());
        $this->assertEquals($this->testUrl . '/', $client->getEffectiveUrl());

        // PATCH
        $client = new Client($this->testUrl);
        $client->patch($data);

        $this->assertEquals($this->testUrl . '/', $client->getBaseUrl());
        $this->assertEquals($this->testUrl . '/', $client->getEffectiveUrl());

        // DELETE
        $client = new Client($this->testUrl);
        $client->delete($data);

        $this->assertEquals($this->testUrl . '/', $client->getBaseUrl());
        $this->assertEquals($this->testUrl . '/?' . http_build_query($data), $client->getEffectiveUrl());

        // HEAD
        $client = new Client($this->testUrl);
        $client->head($data);

        $this->assertEquals($this->testUrl . '/', $client->getBaseUrl());
        $this->assertEquals($this->testUrl . '/?' . http_build_query($data), $client->getEffectiveUrl());

        // OPTIONS
        $client = new Client($this->testUrl);
        $client->options($data);

        $this->assertEquals($this->testUrl . '/', $client->getBaseUrl());
        $this->assertEquals($this->testUrl . '/?' . http_build_query($data), $client->getEffectiveUrl());

    }

    public function testPostRequestMethod()
    {
        $client   = new Client($this->testUrl);
        $response = json_decode($client->post());

        $this->assertEquals('POST', $response->requestMethod);
    }

    public function testPostData()
    {
        $data     = ['foo' => 'bar'];
        $client   = new Client($this->testUrl);
        $response = json_decode($client->post($data), JSON_OBJECT_AS_ARRAY);

        $this->assertEquals($data, $response['post']);
    }

    public function testPostAssociativeArrayData()
    {
        $data     = [
            'username' => 'my_username',
            'password' => 'my_password',
            'data'     => [
                'param1' => 'string',
                'param2' => 123,
                'param3' => 3.14
            ]
        ];
        $client   = new Client($this->testUrl);
        $response = json_decode($client->post($data), JSON_OBJECT_AS_ARRAY);

        $this->assertEquals($data, $response['post']);
    }

    public function testPostFilePathUpload()
    {
        $filePath = dirname(__DIR__) . '/data/test.png';

        $this->assertTrue(file_exists($filePath));

        $data = [
            'image' => new \CURLFile($filePath)
        ];

        $client = new Client($this->testUrl);

        $response = json_decode($client->post($data, true), JSON_OBJECT_AS_ARRAY);

        $this->assertEquals([
            'image' => [
                'originalName' => 'test.png',
                'mimeType'     => 'image/png',
                'size'         => 2855
            ]
        ], $response['files']);

    }

    public function testPut()
    {
        $client   = new Client($this->testUrl);
        $response = json_decode($client->put());

        $this->assertEquals('PUT', $response->requestMethod);
    }

    public function testPutData()
    {
        $data     = ['foo' => 'bar'];
        $client   = new Client($this->testUrl);
        $response = json_decode($client->put($data), JSON_OBJECT_AS_ARRAY);

        $this->assertEquals($data, $response['post']);
    }

    public function testPutFileHandle()
    {
        $filePath = dirname(__DIR__) . '/data/test.png';

        $this->assertTrue(file_exists($filePath));

        $client   = new Client($this->testUrl);
        $response = json_decode($client->putFile($filePath));

        $this->assertEquals(mime_content_type($filePath), $response->content->mimeContentType);
        $this->assertEquals(filesize($filePath), $response->content->fileSize);
    }

    public function testPatchRequestMethod()
    {
        $client   = new Client($this->testUrl);
        $response = json_decode($client->patch());

        $this->assertEquals('PATCH', $response->requestMethod);
    }

    public function testDelete()
    {
        $client   = new Client($this->testUrl);
        $response = json_decode($client->delete());

        $this->assertEquals('DELETE', $response->requestMethod);

        $client   = new Client($this->testUrl);
        $response = json_decode($client->delete(['foo' => 'bar']));

        $this->assertEquals('bar', $response->get->foo);
    }

    public function testHeadRequestMethod()
    {
        $client   = new Client($this->testUrl);
        $response = $client->head();

        $this->assertEquals('HEAD', $client->getHeader('X-REQUEST-METHOD'));
        $this->assertEmpty($response);
    }

    public function testOptionsRequestMethod()
    {
        $client = new Client($this->testUrl);
        $client->options();

        $this->assertEquals('OPTIONS', $client->getHeader('X-REQUEST-METHOD'));
    }

    public function testDownload()
    {
        $filePath = tempnam(sys_get_temp_dir(), 'php_curl_download_');

        $client = new Client();
        $client->download($this->testUrl . '/tree.jpg', $filePath);

        $this->assertEquals('image/jpeg', mime_content_type($filePath));
        $this->assertEquals(57883, filesize($filePath));
    }

    public function testBasicHttpAuth()
    {
        $client = new Client($this->testUrl . '/http/basic');

        $this->assertEquals('', $client->get());
        $this->assertEquals('401', $client->getStatus());
        $this->assertEquals('Unauthorized', $client->getStatusMessage());

        // Set Authentication

        $client = new Client($this->testUrl . '/http/basic');

        $username = 'user_http_basic';
        $password = 'httpbasicpass';

        $client->setBasicAuthentication($username, $password);

        $this->assertEquals('user_http_basic', json_decode($client->get())->http_user);
        $this->assertEquals('200', $client->getStatus());
        $this->assertEquals('OK', $client->getStatusMessage());
    }

    public function testDigestHttpAuth()
    {
        $client = new Client($this->testUrl . '/http/digest');

        $this->assertEquals('', $client->get());
        $this->assertEquals('401', $client->getStatus());
        $this->assertEquals('Unauthorized', $client->getStatusMessage());

        // Set Authentication

        $client = new Client($this->testUrl . '/http/digest');

        $username = 'user_http_digest';
        $password = 'invalid_httpdigestpass';

        $client->setDigestAuthentication($username, $password);

        $this->assertEquals('', $client->get());
        $this->assertEquals('401', $client->getStatus());
        $this->assertEquals('Unauthorized', $client->getStatusMessage());

        $client = new Client($this->testUrl . '/http/digest');

        $username = 'user_http_digest';
        $password = 'httpdigestpass';

        $client->setDigestAuthentication($username, $password);

        $this->assertStringStartsWith('Digest username="user_http_digest", realm="secure-api", nonce="',
            json_decode($client->get())->server->{'HTTP_AUTHORIZATION'});
        $this->assertEquals('200', $client->getStatus());

        // TODO Why it doesn't work?
        //$this->assertEquals('OK', $client->getStatusMessage());
    }

    public function testReferrer()
    {
        $client = new Client($this->testUrl);

        $client->setReferer('A_REFERER');

        $this->assertEquals('A_REFERER', json_decode($client->get())->referer);
    }

    public function testResponseBody()
    {
        $client = new Client($this->testUrl);
        $client->get();
        $this->assertEquals('OK', $client->getStatusMessage());

        $client = new Client($this->testUrl);
        $client->post();
        $this->assertEquals('OK', $client->getStatusMessage());

        $client = new Client($this->testUrl);
        $client->put();
        $this->assertEquals('OK', $client->getStatusMessage());

        $client = new Client($this->testUrl);
        $client->patch();
        $this->assertEquals('OK', $client->getStatusMessage());

        $client = new Client($this->testUrl);
        $client->delete();
        $this->assertEquals('OK', $client->getStatusMessage());

        // TODO Why it gives OK?
        $client = new Client($this->testUrl);
        $client->head();
        //$this->assertEquals('', $client->getStatusMessage());

        $client = new Client($this->testUrl);
        $client->options();
        $this->assertEquals('OK', $client->getStatusMessage());
    }

    public function testCookies()
    {
        $client = new Client($this->testUrl);
        $client->setCookie('my_cookie', 'm_i_a_m');
        $response = $client->get();
        $this->assertEquals('m_i_a_m', json_decode($response)->cookies->my_cookie);

        $client = new Client($this->testUrl);
        $client->setCookie('my_cookie', 'm i a m');
        $response = $client->get();
        $this->assertEquals('m i a m', json_decode($response)->cookies->my_cookie);
    }

    public function testCookieFile()
    {
        $cookieFile = dirname(__DIR__) . '/data/cookie-file.txt';

        $client = new Client($this->testUrl);

        $client->setCookieFile($cookieFile);

        $response = $client->get();
        $this->assertEquals('m_i_a_m', json_decode($response)->cookies->my_cookie);
    }

    public function testCookieJar()
    {
        $cookieJar = dirname(__DIR__) . '/data/cookie-jar.txt';

        if(file_exists($cookieJar)) {
            unlink($cookieJar);
        }

        $client = new Client($this->testUrl);

        $client->setCookieJar($cookieJar);

        $client->get();

        $client->close();

        $this->assertTrue(file_exists($cookieJar));
        $this->assertTrue(
            !(
                strpos(
                    file_get_contents($cookieJar), "\t" . 'server-cookie' . "\t" . 'contentOfServerCookie'
                ) === false
            )
        );
    }

    public function testMultipleCookieResponse()
    {
        $client = new Client($this->testUrl);

        $client->get();

        $this->assertEquals(
            'server-cookie=contentOfServerCookie; path=/; httponly',
            $client->getHeader('Set-Cookie')
        );
    }

    public function testTimeoutMsError()
    {
        $client = new Client('http://www.google.be');
        $client->setConnectionTimeout(0.001);

        try {
            $client->get();
            $this->fail('It should throws an exception');
        } catch (Exception $e) {
            $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $e->getCode());
        }
    }

    public function testTimeoutError()
    {
        $client = new Client($this->testUrl);
        $client->setTimeout(1);

        try {
            $client->get(['sleep' => 1]);
            $this->fail('It should throws an exception');
        } catch (Exception $e) {
            $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $e->getCode());
        }
    }

    public function testErrorMessage()
    {
        $client = new Client($this->testUrl);

        $client->get('/not_found');

        $this->assertEquals(404, $client->getStatus());
        $this->assertEquals('Not', $client->getStatusMessage());
        //$this->assertEquals([], $client->getHeaders());
    }

    public function testNestedData()
    {
        $client = new Client($this->testUrl);

        $data = array(
            'username' => 'my_username',
            'info' => array(
                'name' => 'my_name',
                'mail' => 'my_email',
                'profile' => array(
                    'twitter' => 'my_twitter',
                    'github' => 'my_github',
                ),
            ),
        );

        $this->assertEquals($data, json_decode($client->post($data), JSON_OBJECT_AS_ARRAY)['post']);
    }

}
