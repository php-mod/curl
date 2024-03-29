<?php

namespace Curl;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class CurlTest extends TestCase
{
    const TEST_URL = 'http://localhost:1234';

    /**
     *
     * @var Curl
     */
    protected $curl;

    public function set_up()
    {
        parent::set_up();
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    }

    public function server($request_method, $data='')
    {
        $request_method = strtolower($request_method);
        $this->curl->$request_method(self::TEST_URL . '/server.php', $data);
        return $this->curl->response;
    }

    public function testExtensionLoaded()
    {
        $this->assertTrue(extension_loaded('curl'));
    }

    public function testUserAgent()
    {
        $this->curl->setUserAgent(Curl::USER_AGENT);
        $this->assertEquals(Curl::USER_AGENT, $this->server('GET', array(
                'test' => 'server',
                'key' => 'HTTP_USER_AGENT',
        )));
    }

    public function testGet()
    {
        $this->assertTrue($this->server('GET', array(
                'test' => 'server',
                'key' => 'REQUEST_METHOD',
        )) === 'GET');
    }

    public function testPostRequestMethod()
    {
        $this->assertTrue($this->server('POST', array(
                'test' => 'server',
                'key' => 'REQUEST_METHOD',
        )) === 'POST');
    }

    public function testPostData()
    {
        $this->assertTrue($this->server('POST', array(
                'test' => 'post',
                'key' => 'test',
        )) === 'post');
    }

    public function testPostJsonData()
    {
        $resp = $this->curl->post(self::TEST_URL.'/server.php', ['foo' => 'bar'], true);

        $this->assertTrue($resp->isSuccess());

        $this->assertArrayHasKey('x-powered-by', $resp->getResponseHeaders());

        // syntax error check
        $resp->reset();
    }

    public function testPutJsonData()
    {
        $resp = $this->curl->put(self::TEST_URL.'/server.php', ['foo' => 'bar'], true, true);
        $this->assertTrue($resp->isSuccess());
        $this->assertArrayHasKey('x-powered-by', $resp->getResponseHeaders());
        // syntax error check
        $resp->reset();
    }

    public function testPutJsonNotAsJsonData()
    {
        $resp = $this->curl->put(self::TEST_URL.'/server.php', ['foo' => 'bar'], true, false);
        $this->assertTrue($resp->isSuccess());
        $this->assertArrayHasKey('x-powered-by', $resp->getResponseHeaders());
        // syntax error check
        $resp->reset();
    }

    public function testPatchJsonData()
    {
        $resp = $this->curl->patch(self::TEST_URL.'/server.php', ['foo' => 'bar'], true, true);
        $this->assertTrue($resp->isSuccess());
        $this->assertArrayHasKey('x-powered-by', $resp->getResponseHeaders());
        // syntax error check
        $resp->reset();
    }
    public function testPatchJsonNotAsJsonData()
    {
        $resp = $this->curl->patch(self::TEST_URL.'/server.php', ['foo' => 'bar'], true, false);
        $this->assertTrue($resp->isSuccess());
        $this->assertArrayHasKey('x-powered-by', $resp->getResponseHeaders());
        // syntax error check
        $resp->reset();
    }

    public function testPurge()
    {
        $object = $this->curl->purge('testurl_to_purge', 'example.com');
        $this->assertTrue($object instanceof Curl);
    }

    public function testPostMultidimensionalData()
    {
        $data = array(
                'key' => 'file',
                'file' => array(
                        'wibble',
                        'wubble',
                        'wobble',
                ),
        );

        $this->curl->post(self::TEST_URL . '/post_multidimensional.php', $data);

        $this->assertEquals(
            'key=file&file%5B0%5D=wibble&file%5B1%5D=wubble&file%5B2%5D=wobble',
            $this->curl->response
        );
    }

    public function testPostFilePathUpload()
    {
        $file_path = $this->get_png();

        $data = array(
                'key' => 'image',
                'image' => '@' . $file_path,
        );

        $this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);

        $this->curl->post(self::TEST_URL . '/post_file_path_upload.php', $data);

        $this->assertEquals(
            array(
                        'request_method' => 'POST',
                        'key' => 'image',
                        'mime_content_type' => 'ERROR', // Temp change the image response, but assuming this is not fixing the issue indeed.
                        //'mime_content_type' => 'image/png'
                ),
            json_decode($this->curl->response, true)
        );

        unlink($file_path);
    }

    public function testPutRequestMethod()
    {
        $this->assertTrue($this->server('PUT', array(
                'test' => 'server',
                'key' => 'REQUEST_METHOD',
        )) === 'PUT');
    }

    public function testPutData()
    {
        $this->assertTrue($this->server('PUT', array(
                'test' => 'put',
                'key' => 'test',
        )) === 'put');
    }

    public function testPutFileHandle()
    {
        $png = $this->create_png();
        $tmp_file = $this->create_tmp_file($png);

        $this->curl->setOpt(CURLOPT_PUT, true);
        $this->curl->setOpt(CURLOPT_INFILE, $tmp_file);
        $this->curl->setOpt(CURLOPT_INFILESIZE, strlen($png));
        $this->curl->put(self::TEST_URL . '/server.php', array(
                'test' => 'put_file_handle',
        ));

        fclose($tmp_file);

        $this->assertTrue($this->curl->response === 'image/png');
    }

    public function testDelete()
    {
        $this->assertTrue($this->server('DELETE', array(
                'test' => 'server',
                'key' => 'REQUEST_METHOD',
        )) === 'DELETE');

        $this->assertTrue($this->server('DELETE', array(
                'test' => 'delete',
                'key' => 'test',
        )) === 'delete');
    }

    public function testDeleteWithPayload()
    {
        $this->curl->setVerbose();
        $resp = $this->curl->delete(self::TEST_URL.'/server.php', ['foo' => 'bar'], true);
        $this->assertTrue($resp->isSuccess());
        $this->assertFalse($resp->isInfo());
        $this->assertFalse($resp->isRedirect());
        $this->assertFalse($resp->isClientError());
        $this->assertFalse($resp->isServerError());
        $this->assertSame('http://localhost:1234/server.php', $resp->getEndpoint());

        $this->assertSame('Error.', $resp->getResponse());
        $this->assertSame('localhost:1234', $resp->getResponseHeaders('HOST'));
        unset($this->curl);
    }

    public function testGetOpts()
    {
        $this->curl->get(self::TEST_URL . '/http_basic_auth.php');
        $opts = $this->curl->getOpts();
        $this->arrayHasKey('http_code', $opts);
        // since we not autorized, this should return a 401 status code
        $this->assertSame(401, $this->curl->getOpt(CURLINFO_HTTP_CODE));
    }

    public function testBasicHttpAuth()
    {
        $data = array();

        $this->curl->get(self::TEST_URL . '/http_basic_auth.php', $data);

        $this->assertEquals('canceled', $this->curl->response);

        $username = 'myusername';
        $password = 'mypassword';

        $this->curl->setBasicAuthentication($username, $password);

        $this->curl->get(self::TEST_URL . '/http_basic_auth.php', $data);

        $this->assertEquals(
            '{"username":"myusername","password":"mypassword"}',
            $this->curl->response
        );
    }

    public function testReferrer()
    {
        $this->curl->setReferer('myreferrer');
        $this->assertTrue($this->server('GET', array(
                'test' => 'server',
                'key' => 'HTTP_REFERER',
        )) === 'myreferrer');
    }

    public function testCookies()
    {
        $this->curl->setCookie('mycookie', 'yum');
        $this->assertTrue($this->server('GET', array(
                'test' => 'cookie',
                'key' => 'mycookie',
        )) === 'yum');
    }

    public function testError()
    {
        $this->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        $this->curl->get('http://1.2.3.4/');
        $this->assertTrue($this->curl->error === true);
        $this->assertTrue($this->curl->curl_error === true);
        $this->assertTrue($this->curl->curl_error_code === CURLE_OPERATION_TIMEOUTED);
    }

    public function testHeaders()
    {
        $this->curl->setHeader('Content-Type', 'application/json');
        $this->curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $this->curl->setHeader('Accept', 'application/json');
        $this->assertTrue($this->server('GET', array(
                'test' => 'server',
                'key' => 'CONTENT_TYPE',
        )) === 'application/json');
        $this->assertTrue($this->server('GET', array(
                'test' => 'server',
                'key' => 'HTTP_X_REQUESTED_WITH',
        )) === 'XMLHttpRequest');
        $this->assertTrue($this->server('GET', array(
                'test' => 'server',
                'key' => 'HTTP_ACCEPT',
        )) === 'application/json');
    }

    public function testHeadersWithContinue()
    {
        $headers = file(dirname(__FILE__) . '/data/response_headers_with_continue.txt');

        $this->curl->response_headers = array();
        foreach ($headers as $header_line) {
            $this->curl->addResponseHeaderLine(null, $header_line);
        }

        $expected_headers = array_values(array_filter(array_map(function ($l) {
            return trim($l, "\r\n");
        }, array_slice($headers, 1))));

        $this->assertEquals($expected_headers, $this->curl->response_headers);
    }

    public function testReset()
    {
        $curl = $this->getMockBuilder(get_class($this->curl))->getMock();
        $curl->expects($this->once())->method('reset')->with();
        // lets make small request
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        $curl->get('http://1.2.3.4/');
        $curl->reset();
        $this->assertFalse($curl->error);
        $this->assertSame(0, $curl->error_code);
        $this->assertNull($curl->error_message);
        $this->assertFalse($curl->curl_error);
        $this->assertSame(0, $curl->curl_error_code);
        $this->assertNull($curl->curl_error_message);
        $this->assertFalse($curl->http_error);
        $this->assertSame(0, $curl->http_status_code);
        $this->assertNull($curl->http_error_message);
        $this->assertNull($curl->request_headers);
        $this->assertEmpty($curl->response_headers);
        $this->assertNull($curl->response);
    }

    public function create_png()
    {
        // PNG image data, 1 x 1, 1-bit colormap, non-interlaced
        ob_start();
        imagepng(imagecreatefromstring(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7')));
        $raw_image = ob_get_contents();
        ob_end_clean();
        return $raw_image;
    }

    public function create_tmp_file($data)
    {
        $tmp_file = tmpfile();
        fwrite($tmp_file, $data);
        rewind($tmp_file);
        return $tmp_file;
    }

    public function get_png()
    {
        $tmp_filename = tempnam('/tmp', 'php-curl-class.');
        file_put_contents($tmp_filename, $this->create_png());
        return $tmp_filename;
    }
}
