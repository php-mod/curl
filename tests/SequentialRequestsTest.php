<?php

namespace Curl;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test sequential requests using the same Curl object instance
 * to ensure options are properly reset between requests.
 *
 * This test addresses the issue where CURLOPT_CUSTOMREQUEST and
 * related options are not cleared after PUT/PATCH/DELETE requests,
 * causing subsequent GET requests to fail.
 */
class SequentialRequestsTest extends TestCase
{
    const TEST_URL = 'http://localhost:1234';

    /**
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

    /**
     * Test that GET works correctly after a PUT request
     */
    public function testGetAfterPut()
    {
        // First, make a PUT request with payload
        $this->curl->put(self::TEST_URL . '/server.php', ['test' => 'put', 'key' => 'test'], true);
        $this->assertTrue($this->curl->isSuccess(), 'PUT request should succeed');

        // Then, make a GET request
        $this->curl->get(self::TEST_URL . '/server.php', ['test' => 'server', 'key' => 'REQUEST_METHOD']);
        $this->assertTrue($this->curl->isSuccess(), 'GET request after PUT should succeed');
        $this->assertEquals('GET', $this->curl->response, 'Request method should be GET, not PUT');
    }

    /**
     * Test that GET works correctly after a DELETE request
     */
    public function testGetAfterDelete()
    {
        // First, make a DELETE request with payload
        $this->curl->delete(self::TEST_URL . '/server.php', ['test' => 'delete', 'key' => 'test'], true);
        $this->assertTrue($this->curl->isSuccess(), 'DELETE request should succeed');

        // Then, make a GET request
        $this->curl->get(self::TEST_URL . '/server.php', ['test' => 'server', 'key' => 'REQUEST_METHOD']);
        $this->assertTrue($this->curl->isSuccess(), 'GET request after DELETE should succeed');
        $this->assertEquals('GET', $this->curl->response, 'Request method should be GET, not DELETE');
    }

    /**
     * Test that GET works correctly after a PATCH request
     */
    public function testGetAfterPatch()
    {
        // First, make a PATCH request with payload
        $this->curl->patch(self::TEST_URL . '/server.php', ['test' => 'patch', 'key' => 'test'], true);
        $this->assertTrue($this->curl->isSuccess(), 'PATCH request should succeed');

        // Then, make a GET request
        $this->curl->get(self::TEST_URL . '/server.php', ['test' => 'server', 'key' => 'REQUEST_METHOD']);
        $this->assertTrue($this->curl->isSuccess(), 'GET request after PATCH should succeed');
        $this->assertEquals('GET', $this->curl->response, 'Request method should be GET, not PATCH');
    }

    /**
     * Test that POST works correctly after a PUT request
     */
    public function testPostAfterPut()
    {
        // First, make a PUT request with payload
        $this->curl->put(self::TEST_URL . '/server.php', ['test' => 'put', 'key' => 'test'], true);
        $this->assertTrue($this->curl->isSuccess(), 'PUT request should succeed');

        // Then, make a POST request
        $this->curl->post(self::TEST_URL . '/server.php', ['test' => 'server', 'key' => 'REQUEST_METHOD']);
        $this->assertTrue($this->curl->isSuccess(), 'POST request after PUT should succeed');
        $this->assertEquals('POST', $this->curl->response, 'Request method should be POST, not PUT');
    }

    public function tear_down()
    {
        if ($this->curl) {
            $this->curl->close();
        }
        parent::tear_down();
    }
}
