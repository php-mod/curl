<?php

namespace Curl;

class Curl
{
    /**
     * @var resource cURL resource
     */
    private $handle;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string[]
     */
    private $cookies = [];

    /**
     * Initialize a cURL session
     *
     * @param string $url
     *
     * @throws \ErrorException
     */
    public function __construct($url = null)
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->url    = $url;
        $this->handle = curl_init($url);

        if ($this->handle === false) {
            throw new \ErrorException('Could not initialize the cURL session.');
        }
    }

    /**
     * Gets cURL version information
     *
     * @return Version
     */
    public static function version()
    {
        return new Version(curl_version());
    }

    /**
     * TRUE to automatically set the "Referer:" field in requests where it follows a "Location:" redirect.
     *
     * @param bool $autoReferer default is TRUE
     *
     * @return $this
     * @throws \ErrorException
     */
    public function autoReferer($autoReferer = true)
    {
        return $this->setBooleanOption(CURLOPT_AUTOREFERER, $autoReferer);
    }

    /**
     * Set an option for a cURL transfer
     *
     * @param $option
     * @param $value
     *
     * @return $this
     * @throws \ErrorException
     */
    private function setOption($option, $value)
    {
        if (curl_setopt($this->handle, $option, $value) === false) {
            throw new \ErrorException('failure.');
        }

        return $this;
    }

    /**
     * TRUE to mark this as a new cookie "session". It will force libcurl to ignore all cookies it is about to load
     * that are "session cookies" from the previous session. By default, libcurl always stores and loads all cookies,
     * independent if they are session cookies or not. Session cookies are cookies without expiry date and they are
     * meant to be alive and existing for this "session" only.
     *
     * @param bool $newCookieSession
     *
     * @return $this
     * @throws \ErrorException
     */
    public function newCookieSession($newCookieSession = true)
    {
        return $this->setBooleanOption(CURLOPT_COOKIESESSION, $newCookieSession);
    }

    /**
     * TRUE to output SSL certification information to STDERR on secure transfers.
     *
     * @param bool $certInfo
     *
     * @return $this
     * @throws \ErrorException
     */
    public function certInfo($certInfo = true)
    {
        if (!is_bool($certInfo)) {
            throw new \InvalidArgumentException('certInfo method only accepts string.');
        }

        if ($certInfo) {
            $this->verbose();
        }

        return $this->setOption(CURLOPT_CERTINFO, $certInfo);
    }

    /**
     * TRUE to output verbose information. Writes output to STDERR, or the file specified if $filePath.
     *
     * @param bool   $verbose
     * @param string $filePath
     *
     * @return $this
     * @throws \ErrorException
     */
    public function verbose($verbose = true, $filePath = null)
    {
        $this->setBooleanOption(CURLOPT_VERBOSE, $verbose);

        if ($filePath) {
            $this->setStdErrLocation($filePath);
        }

        return $this;
    }

    /**
     * An alternative location to output errors to instead of STDERR.
     *
     * @param string $filePath
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setStdErrLocation($filePath)
    {
        return $this->setStringOption(CURLOPT_STDERR, $filePath);
    }

    /**
     * TRUE tells the library to perform all the required proxy authentication and connection setup, but no data
     * transfer. This option is implemented for HTTP, SMTP and POP3.
     *
     * @param bool $connectOnly
     *
     * @return $this
     * @throws \ErrorException
     */
    public function connectOnly($connectOnly = true)
    {
        return $this->setBooleanOption(CURLOPT_CONNECT_ONLY, $connectOnly);
    }

    /**
     * TRUE to convert Unix newlines to CRLF newlines on transfers.
     *
     * @param bool $crlf
     *
     * @return $this
     * @throws \ErrorException
     */
    public function crlf($crlf = true)
    {
        return $this->setBooleanOption(CURLOPT_CRLF, $crlf);
    }

    /**
     * TRUE to use a global DNS cache. This option is not thread-safe and is enabled by default.
     *
     * @param bool $useGlobalCache
     *
     * @return $this
     * @throws \ErrorException
     */
    public function dnsUseGlobalCache($useGlobalCache = true)
    {
        return $this->setBooleanOption(CURLOPT_DNS_USE_GLOBAL_CACHE, $useGlobalCache);
    }

    /**
     * TRUE to fail verbosely if the HTTP code returned is greater than or equal to 400. The default behavior is to
     * return the page normally, ignoring the code.
     *
     * @param bool $failOnError
     *
     * @return $this
     * @throws \ErrorException
     */
    public function failOnError($failOnError = true)
    {
        return $this->setBooleanOption(CURLOPT_FAILONERROR, $failOnError);
    }

    /**
     * TRUE to attempt to retrieve the modification date of the remote document. This value can be retrieved using the
     * getFileTime method.
     *
     * @param bool $retrieveFileTime
     *
     * @return $this
     * @throws \ErrorException
     */
    public function retrieveFileTime($retrieveFileTime = true)
    {
        return $this->setBooleanOption(CURLOPT_FILETIME, $retrieveFileTime);
    }

    /**
     * TRUE to force the connection to explicitly close when it has finished processing, and not be pooled for reuse.
     *
     * @param bool $forbidReuse
     *
     * @return $this
     * @throws \ErrorException
     */
    public function forbidReuse($forbidReuse = true)
    {
        return $this->setBooleanOption(CURLOPT_FORBID_REUSE, $forbidReuse);
    }

    /**
     * TRUE to force the use of a new connection instead of a cached one.
     *
     * @param bool $freshConnect
     *
     * @return $this
     * @throws \ErrorException
     */
    public function freshConnect($freshConnect = true)
    {
        return $this->setBooleanOption(CURLOPT_FRESH_CONNECT, $freshConnect);
    }

    /**
     * TRUE to use EPRT (and LPRT) when doing active FTP downloads. Use FALSE to disable EPRT and LPRT and use PORT
     * only.
     *
     * @param bool $ftpUseEPRT
     *
     * @return $this
     * @throws \ErrorException
     */
    public function ftpUseEPRT($ftpUseEPRT = true)
    {
        return $this->setBooleanOption(CURLOPT_FTP_USE_EPRT, $ftpUseEPRT);
    }

    /**
     * TRUE to first try an EPSV command for FTP transfers before reverting back to PASV. Set to FALSE to disable EPSV.
     *
     * @param bool $ftpUseEPSV
     *
     * @return $this
     * @throws \ErrorException
     */
    public function ftpUseEPSV($ftpUseEPSV = true)
    {
        return $this->setBooleanOption(CURLOPT_FTP_USE_EPSV, $ftpUseEPSV);
    }

    /**
     * TRUE to create missing directories when an FTP operation encounters a path that currently doesn't exist.
     *
     * @param bool $ftpCreateMissingDirs
     *
     * @return $this
     * @throws \ErrorException
     */
    public function ftpCreateMissingDirs($ftpCreateMissingDirs = true)
    {
        return $this->setBooleanOption(CURLOPT_FTP_CREATE_MISSING_DIRS, $ftpCreateMissingDirs);
    }

    /**
     * TRUE to append to the remote file instead of overwriting it.
     *
     * @param bool $ftpAppend
     *
     * @return $this
     * @throws \ErrorException
     */
    public function ftpAppend($ftpAppend = true)
    {
        return $this->setBooleanOption(CURLOPT_FTPAPPEND, $ftpAppend);
    }

    /**
     * Pass a long specifying whether the TCP_NODELAY option is to be set or cleared (1 = set, 0 = clear). The option
     * is cleared by default.
     *
     * @param bool $tcpNoDelay
     *
     * @return $this
     * @throws \ErrorException
     */
    public function tcpNoDelay($tcpNoDelay = true)
    {
        return $this->setBooleanOption(CURLOPT_TCP_NODELAY, $tcpNoDelay);
    }

    /**
     * An alias of transferText method. Use that instead.
     *
     * @deprecated use transferText instead
     *
     * @param bool $ftpASCII
     *
     * @return $this
     * @throws \ErrorException
     */
    public function ftpASCII($ftpASCII = true)
    {
        return $this->setBooleanOption(CURLOPT_FTPASCII, $ftpASCII);
    }

    /**
     * TRUE to only list the names of an FTP directory.
     *
     * @param bool $ftpListOnly
     *
     * @return $this
     * @throws \ErrorException
     */
    public function ftpListOnly($ftpListOnly = true)
    {
        return $this->setBooleanOption(CURLOPT_FTPLISTONLY, $ftpListOnly);
    }

    /**
     * TRUE to include the header in the output.
     *
     * @param bool $includeHeader
     *
     * @return $this
     * @throws \ErrorException
     */
    public function includeHeader($includeHeader = true)
    {
        return $this->setBooleanOption(CURLOPT_HEADER, $includeHeader);
    }

    /**
     * TRUE to track the handle's request string.
     *
     * @param bool $track
     *
     * @return $this
     * @throws \ErrorException
     */
    public function headerOut($track = true)
    {
        return $this->setBooleanOption(CURLINFO_HEADER_OUT, $track);
    }

    /**
     * TRUE to reset the HTTP request method to GET.
     * Since GET is the default, this is only necessary if the request method has been changed.
     *
     * @param bool $httpGET
     *
     * @return $this
     * @throws \ErrorException
     */
    public function httpGET($httpGET = true)
    {
        return $this->setBooleanOption(CURLOPT_HTTPGET, $httpGET);
    }

    /**
     * TRUE to tunnel through a given HTTP proxy.
     *
     * @param bool $httpProxyTunnel
     *
     * @return $this
     * @throws \ErrorException
     */
    public function httpProxyTunnel($httpProxyTunnel = true)
    {
        return $this->setBooleanOption(CURLOPT_HTTPPROXYTUNNEL, $httpProxyTunnel);
    }

    /**
     * TRUE to scan the ~/.netrc file to find a username and password for the remote site that a connection is being
     * established with.
     *
     * @param bool $netrc
     *
     * @return $this
     * @throws \ErrorException
     */
    public function netrc($netrc = true)
    {
        return $this->setBooleanOption(CURLOPT_NETRC, $netrc);
    }

    /**
     * TRUE to exclude the body from the output. Request method is then set to HEAD. Changing this to FALSE does not
     * change it to GET.
     *
     * @param bool $noBody
     *
     * @return $this
     * @throws \ErrorException
     */
    public function noBody($noBody = true)
    {
        return $this->setBooleanOption(CURLOPT_NOBODY, $noBody);
    }

    /**
     * TRUE to disable the progress meter for cURL transfers.
     * Note: PHP automatically sets this option to TRUE, this should only be changed for debugging purposes.
     *
     * @param bool $noProgress
     *
     * @return $this
     * @throws \ErrorException
     */
    public function noProgress($noProgress = true)
    {
        return $this->setBooleanOption(CURLOPT_NOPROGRESS, $noProgress);
    }

    /**
     * TRUE to ignore any cURL function that causes a signal to be sent to the PHP process.
     * This is turned on by default in multi-threaded SAPIs so timeout options can still be used.
     *
     * @param bool $noSignal
     *
     * @return $this
     * @throws \ErrorException
     */
    public function noSignal($noSignal = true)
    {
        return $this->setBooleanOption(CURLOPT_NOSIGNAL, $noSignal);
    }

    /**
     * TRUE to do a regular HTTP POST.
     * This POST is the normal application/x-www-form-urlencoded kind, most commonly used by HTML forms.
     *
     * @param bool $post
     *
     * @return $this
     * @throws \ErrorException
     */
    public function post($post = true)
    {
        return $this->setBooleanOption(CURLOPT_POST, $post);
    }

    /**
     * TRUE to return the transfer as a string of the return value of exec() method instead of outputting it out
     * directly.
     *
     * @param bool $returnTransfer
     *
     * @return $this
     * @throws \ErrorException
     */
    public function returnTransfer($returnTransfer = true)
    {
        return $this->setBooleanOption(CURLOPT_RETURNTRANSFER, $returnTransfer);
    }

    /**
     * TRUE to use ASCII mode for FTP transfers. For LDAP, it retrieves data in plain text instead of HTML. On Windows
     * systems, it will not set STDOUT to binary mode.
     *
     * @param $transferText
     *
     * @return $this
     * @throws \ErrorException
     */
    public function transferText($transferText)
    {
        return $this->setBooleanOption(CURLOPT_TRANSFERTEXT, $transferText);
    }

    /**
     * TRUE to prepare for an upload.
     *
     * @param bool $upload
     *
     * @return $this
     * @throws \ErrorException
     */
    public function upload($upload = true)
    {
        return $this->setBooleanOption(CURLOPT_UPLOAD, $upload);
    }

    /**
     * The size of the buffer to use for each read. There is no guarantee this request will be fulfilled, however.
     *
     * @param int $bufferSize
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setBufferSize($bufferSize)
    {
        return $this->setIntegerOption(CURLOPT_BUFFERSIZE, $bufferSize);
    }

    /**
     * The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
     *
     * @param int $connectTimeout
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setConnectTimeout($connectTimeout)
    {
        return $this->setIntegerOption(CURLOPT_CONNECTTIMEOUT, $connectTimeout);
    }

    /**
     * The number of milliseconds to wait while trying to connect. Use 0 to wait indefinitely.
     * If libcurl is built to use the standard system name resolver, that
     * portion of the connect will still use full-second resolution for
     * timeouts with a minimum timeout allowed of one second.
     *
     * @param int $connectTimeoutMs
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setConnectTimeoutMs($connectTimeoutMs)
    {
        return $this->setIntegerOption(CURLOPT_CONNECTTIMEOUT_MS, $connectTimeoutMs);
    }

    /**
     * The number of seconds to keep DNS entries in memory. This option is set to 120 (2 minutes) by default.
     *
     * @param int $dnsCacheTimeout
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setDnsCacheTimeout($dnsCacheTimeout)
    {
        return $this->setIntegerOption(CURLOPT_DNS_CACHE_TIMEOUT, $dnsCacheTimeout);
    }

    /**
     * The FTP authentication method (when is activated):
     * CURLFTPAUTH_SSL (try SSL first),
     * CURLFTPAUTH_TLS (try TLS first), or
     * CURLFTPAUTH_DEFAULT (let cURL decide).
     *
     * @param int $ftpSslAuth
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setFtpSslAuth($ftpSslAuth)
    {
        return $this->setIntegerOption(CURLOPT_FTPSSLAUTH, $ftpSslAuth);
    }

    /**
     * CURL_HTTP_VERSION_NONE (default, lets CURL decide which version to use),
     * CURL_HTTP_VERSION_1_0 (forces HTTP/1.0), or CURL_HTTP_VERSION_1_1 (forces HTTP/1.1).
     *
     * @param int $httpVersion
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setHttpVersion($httpVersion)
    {
        return $this->setIntegerOption(CURLOPT_HTTP_VERSION, $httpVersion);
    }

    /**
     * The HTTP authentication method(s) to use. The options are:
     * CURLAUTH_BASIC,
     * CURLAUTH_DIGEST,
     * CURLAUTH_GSSNEGOTIATE,
     * CURLAUTH_NTLM,
     * CURLAUTH_ANY, and
     * CURLAUTH_ANYSAFE.
     * The bitwise | (or) operator can be used to combine more than one method. If this is done, cURL will poll the
     * server to see what methods it supports and pick the best one. CURLAUTH_ANY is an alias for CURLAUTH_BASIC |
     * CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM. CURLAUTH_ANYSAFE is an alias for CURLAUTH_DIGEST |
     * CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM.
     *
     * @param int $httpAuth
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setHttpAuth($httpAuth)
    {
        return $this->setIntegerOption(CURLOPT_HTTPAUTH, $httpAuth);
    }

    /**
     * The transfer speed, in bytes per second, that the transfer should be below during the count of
     * CURLOPT_LOW_SPEED_TIME seconds before PHP considers the transfer too slow and aborts.
     *
     * @param int $lowSpeedLimit
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setLowSpeedLimit($lowSpeedLimit)
    {
        return $this->setIntegerOption(CURLOPT_LOW_SPEED_LIMIT, $lowSpeedLimit);
    }

    /**
     * The number of seconds the transfer speed should be below CURLOPT_LOW_SPEED_LIMIT before PHP considers the
     * transfer too slow and aborts.
     *
     * @param int $lowSpeedTime
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setLowSpeedTime($lowSpeedTime)
    {
        return $this->setIntegerOption(CURLOPT_LOW_SPEED_TIME, $lowSpeedTime);
    }

    /**
     * The maximum amount of persistent connections that are allowed.
     * When the limit is reached, CURLOPT_CLOSEPOLICY is used to determine which connection to close.
     *
     * @param int $maxConnects
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setMaxConnects($maxConnects)
    {
        return $this->setIntegerOption(CURLOPT_MAXCONNECTS, $maxConnects);
    }

    /**
     * The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
     *
     * @param int $maxRedirects
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setMaxRedirects($maxRedirects)
    {
        return $this->setIntegerOption(CURLOPT_MAXREDIRS, $maxRedirects);
    }

    /**
     * An alternative port number to connect to.
     *
     * @param int $port
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setPort($port)
    {
        return $this->setIntegerOption(CURLOPT_PORT, $port);
    }

    /**
     * A bitmask of 1 (301 Moved Permanently), 2 (302 Found) and 4 (303 See Other) if the HTTP POST method should be
     * maintained when CURLOPT_FOLLOWLOCATION is set and a specific type of redirect occurs.
     *
     * @param int $postRedirect
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setPostRedirect($postRedirect)
    {
        return $this->setIntegerOption(CURLOPT_POSTREDIR, $postRedirect);
    }

    /**
     * Bitmask of CURLPROTO_* values. If used, this bitmask limits what protocols libcurl may use in the transfer. This
     * allows you to have a libcurl built to support a wide range of protocols but still limit specific transfers to
     * only be allowed to use a subset of them. By default libcurl will accept all protocols it supports.
     *
     * @see setRedirectProtocols
     *      Valid protocol options are:
     *      CURLPROTO_HTTP,
     *      CURLPROTO_HTTPS,
     *      CURLPROTO_FTP,
     *      CURLPROTO_FTPS,
     *      CURLPROTO_SCP,
     *      CURLPROTO_SFTP,
     *      CURLPROTO_TELNET,
     *      CURLPROTO_LDAP,
     *      CURLPROTO_LDAPS,
     *      CURLPROTO_DICT,
     *      CURLPROTO_FILE,
     *      CURLPROTO_TFTP,
     *      CURLPROTO_ALL
     *
     * @param int $protocols
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setProtocols($protocols)
    {
        return $this->setIntegerOption(CURLOPT_PROTOCOLS, $protocols);
    }

    /**
     * The HTTP authentication method(s) to use for the proxy connection.
     * Use the same bitmasks as described in setHttpAuth. For proxy authentication, only CURLAUTH_BASIC and
     * CURLAUTH_NTLM are currently supported.
     *
     * @see setHttpAuth
     *
     * @param int $proxyAuth
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setProxyAuth($proxyAuth)
    {
        return $this->setIntegerOption(CURLOPT_PROXYAUTH, $proxyAuth);
    }

    /**
     * The port number of the proxy to connect to. This port number can also be set in CURLOPT_PROXY.
     *
     * @param int $proxyPort
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setProxyPort($proxyPort)
    {
        return $this->setIntegerOption(CURLOPT_PROXYPORT, $proxyPort);
    }

    /**
     * Either CURLPROXY_HTTP (default) or CURLPROXY_SOCKS5.
     *
     * @param int $proxyType
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setProxyType($proxyType)
    {
        return $this->setIntegerOption(CURLOPT_PROXYTYPE, $proxyType);
    }

    /**
     * Bitmask of CURLPROTO_* values. If used, this bitmask limits what protocols libcurl may use in a transfer that it
     * follows to in a redirect when CURLOPT_FOLLOWLOCATION is enabled. This allows you to limit specific transfers to
     * only be allowed to use a subset of protocols in redirections. By default libcurl will allow all protocols except
     * for FILE and SCP. This is a difference compared to pre-7.19.4 versions which unconditionally would follow to all
     * protocols supported.
     *
     * @see setProtocols
     *      for protocol constant values.
     *
     * @param int $redirectProtocols
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setRedirectProtocols($redirectProtocols)
    {
        return $this->setIntegerOption(CURLOPT_REDIR_PROTOCOLS, $redirectProtocols);
    }

    /**
     * The offset, in bytes, to resume a transfer from.
     *
     * @param int $resumeFrom
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setResumeFrom($resumeFrom)
    {
        return $this->setIntegerOption(CURLOPT_RESUME_FROM, $resumeFrom);
    }

    /**
     * 1 to check the existence of a common name in the SSL peer certificate. 2 to check the existence of a common name
     * and also verify that it matches the hostname provided. In production environments the value of this option
     * should be kept at 2 (default value). Support for value 1 removed in cURL 7.28.1
     *
     * @param int $sslVerifyHost
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslVerifyHost($sslVerifyHost)
    {
        return $this->setIntegerOption(CURLOPT_SSL_VERIFYHOST, $sslVerifyHost);
    }

    /**
     * One of CURL_SSLVERSION_DEFAULT (0),
     * CURL_SSLVERSION_TLSv1 (1),
     * CURL_SSLVERSION_SSLv2 (2),
     * CURL_SSLVERSION_SSLv3 (3),
     * CURL_SSLVERSION_TLSv1_0 (4),
     * CURL_SSLVERSION_TLSv1_1 (5) or
     * CURL_SSLVERSION_TLSv1_2 (6).
     * Note:
     * Your best bet is to not set this and let it use the default.
     * Setting it to 2 or 3 is very dangerous given the known vulnerabilities in SSLv2 and SSLv3.
     *
     * @param int $sslVersion
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslVersion($sslVersion)
    {
        return $this->setIntegerOption(CURLOPT_SSLVERSION, $sslVersion);
    }

    /**
     * How CURLOPT_TIMEVALUE is treated.
     * Use CURL_TIMECOND_IFMODSINCE to return the page only if it has been modified since the time specified in
     * CURLOPT_TIMEVALUE. If it hasn't been modified, a "304 Not Modified" header will be returned assuming
     * CURLOPT_HEADER is TRUE. Use CURL_TIMECOND_IFUNMODSINCE for the reverse effect. CURL_TIMECOND_IFMODSINCE is the
     * default.
     *
     * @param int $timeCondition
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setTimeCondition($timeCondition)
    {
        return $this->setIntegerOption(CURLOPT_TIMECONDITION, $timeCondition);
    }

    /**
     * The maximum number of seconds to allow cURL functions to execute.
     *
     * @param int $timeout
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setTimeout($timeout)
    {
        return $this->setIntegerOption(CURLOPT_TIMEOUT, $timeout);
    }

    /**
     * The maximum number of milliseconds to allow cURL functions to execute.
     * If libcurl is built to use the standard system name resolver, that portion of the connect will still use
     * full-second resolution for timeouts with a minimum timeout allowed of one second.
     *
     * @param int $timeoutMs
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setTimeoutMs($timeoutMs)
    {
        return $this->setIntegerOption(CURLOPT_TIMEOUT_MS, $timeoutMs);
    }

    /**
     * The time in seconds since January 1st, 1970. The time will be used by CURLOPT_TIMECONDITION. By default,
     * CURL_TIMECOND_IFMODSINCE is used.
     *
     * @param int $timeValue
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setTimeValue($timeValue)
    {
        return $this->setIntegerOption(CURLOPT_TIMEVALUE, $timeValue);
    }

    /**
     * If a download exceeds this speed (counted in bytes per second) on cumulative average during the transfer, the
     * transfer will pause to keep the average rate less than or equal to the parameter value. Defaults to unlimited
     * speed.
     *
     * @param int $maxRecvSpeedLarge
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setMaxRecvSpeedLarge($maxRecvSpeedLarge)
    {
        return $this->setIntegerOption(CURLOPT_MAX_RECV_SPEED_LARGE, $maxRecvSpeedLarge);
    }

    /**
     * If an upload exceeds this speed (counted in bytes per second) on cumulative average during the transfer, the
     * transfer will pause to keep the average rate less than or equal to the parameter value. Defaults to unlimited
     * speed.
     *
     * @param int $maxSendSpeedLarge
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setMaxSendSpeedLarge($maxSendSpeedLarge)
    {
        return $this->setIntegerOption(CURLOPT_MAX_SEND_SPEED_LARGE, $maxSendSpeedLarge);
    }

    /**
     * A bitmask consisting of one or more of CURLSSH_AUTH_PUBLICKEY, CURLSSH_AUTH_PASSWORD, CURLSSH_AUTH_HOST,
     * CURLSSH_AUTH_KEYBOARD. Set to CURLSSH_AUTH_ANY to let libcurl pick one.
     *
     * @param int $sshAuthTypes
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSshAuthTypes($sshAuthTypes)
    {
        return $this->setIntegerOption(CURLOPT_SSH_AUTH_TYPES, $sshAuthTypes);
    }

    /**
     * Allows an application to select what kind of IP addresses to use when resolving host names. This is only
     * interesting when using host names that resolve addresses using more than one version of IP, possible values are
     * CURL_IPRESOLVE_WHATEVER, CURL_IPRESOLVE_V4, CURL_IPRESOLVE_V6, by default CURL_IPRESOLVE_WHATEVER.
     *
     * @param int $ipResolve
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setIpResolve($ipResolve)
    {
        return $this->setIntegerOption(CURLOPT_IPRESOLVE, $ipResolve);
    }

    /**
     * The name of a file holding one or more certificates to verify the
     * peer with. This only makes sense when used in combination with
     * CURLOPT_SSL_VERIFYPEER.
     * Requires absolute path.
     *
     * @param string $caInfo
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setCaInfo($caInfo)
    {
        return $this->setStringOption(CURLOPT_CAINFO, $caInfo);
    }

    /**
     * A directory that holds multiple CA certificates. Use this option
     * alongside CURLOPT_SSL_VERIFYPEER.
     *
     * @param string $capath
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setCapath($capath)
    {
        return $this->setStringOption(CURLOPT_CAPATH, $capath);
    }

    /**
     * The contents of the "Cookie: " header to be
     * used in the HTTP request.
     *
     * @param $key
     * @param $value
     *
     * @return $this
     * @throws Exception
     */
    public function setCookie($key, $value)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException('setCapath method only accepts string.');
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException('setCapath method only accepts string.');
        }

        if (isset($this->cookies[ $key ])) {
            throw new Exception('key already added');
        }

        $this->cookies[ $key ] = $value;

        return $this;
    }

    /**
     * The name of the file containing the cookie data. The cookie file can
     * be in Netscape format, or just plain HTTP-style headers dumped into
     * a file.
     * If the name is an empty string, no cookies are loaded, but cookie
     * handling is still enabled.
     *
     * @param string $cookieFile
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setCookieFile($cookieFile)
    {
        return $this->setStringOption(CURLOPT_COOKIEFILE, $cookieFile);
    }

    /**
     * The name of a file to save all internal cookies to when the handle is closed,
     * e.g. after a call to curl_close.
     *
     * @param string $cookieJar
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setCookieJar($cookieJar)
    {
        return $this->setStringOption(CURLOPT_COOKIEJAR, $cookieJar);
    }

    /**
     * A custom request method to use instead of
     * "GET" or "HEAD" when doing
     * a HTTP request. This is useful for doing
     * "DELETE" or other, more obscure HTTP requests.
     * Valid values are things like "GET",
     * "POST", "CONNECT" and so on;
     * i.e. Do not enter a whole HTTP request line here. For instance,
     * entering "GET /index.html HTTP/1.0\r\n\r\n"
     * would be incorrect.
     * Note:
     * Don't do this without making sure the server supports the custom
     * request method first.
     *
     * @param string $customRequest
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setCustomRequest($customRequest)
    {
        return $this->setStringOption(CURLOPT_CUSTOMREQUEST, $customRequest);
    }

    /**
     * Like CURLOPT_RANDOM_FILE, except a filename
     * to an Entropy Gathering Daemon socket.
     *
     * @param string $egdSocket
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setEgdSocket($egdSocket)
    {
        return $this->setStringOption(CURLOPT_EGDSOCKET, $egdSocket);
    }

    /**
     * The contents of the "Accept-Encoding: " header.
     * This enables decoding of the response. Supported encodings are
     * "identity", "deflate", and
     * "gzip". If an empty string, "",
     * is set, a header containing all supported encoding types is sent.
     * Added in cURL 7.10.
     *
     * @param string $encoding
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setEncoding($encoding)
    {
        return $this->setStringOption(CURLOPT_ENCODING, $encoding);
    }

    /**
     * The value which will be used to get the IP address to use
     * for the FTP "PORT" instruction. The "PORT" instruction tells
     * the remote server to connect to our specified IP address.  The
     * string may be a plain IP address, a hostname, a network
     * interface name (under Unix), or just a plain '-' to use the
     * systems default IP address.
     *
     * @param string $ftpPort
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setFtpPort($ftpPort)
    {
        return $this->setStringOption(CURLOPT_FTPPORT, $ftpPort);
    }

    /**
     * The name of the outgoing network interface to use. This can be an
     * interface name, an IP address or a host name.
     *
     * @param string $interface
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setInterface($interface)
    {
        return $this->setStringOption(CURLOPT_INTERFACE, $interface);
    }

    /**
     * The password required to use the CURLOPT_SSLKEY
     * or CURLOPT_SSH_PRIVATE_KEYFILE private key.
     * Added in cURL 7.16.1.
     *
     * @param string $keyPassword
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setKeyPassword($keyPassword)
    {
        return $this->setStringOption(CURLOPT_KEYPASSWD, $keyPassword);
    }

    /**
     * The KRB4 (Kerberos 4) security level. Any of the following values
     * (in order from least to most powerful) are valid:
     * "clear",
     * "safe",
     * "confidential",
     * "private"..
     * If the string does not match one of these,
     * "private" is used. Setting this option to NULL
     * will disable KRB4 security. Currently KRB4 security only works
     * with FTP transactions.
     *
     * @param string $krb4level
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setKrb4level($krb4level)
    {
        return $this->setStringOption(CURLOPT_KRB4LEVEL, $krb4level);
    }

    /**
     * The full data to post in a HTTP "POST" operation.
     * To post a file, prepend a filename with @ and
     * use the full path. The filetype can be explicitly specified by
     * following the filename with the type in the format
     * ';type=mimetype'. This parameter can either be
     * passed as a urlencoded string like 'para1=val1&amp;para2=val2&amp;...'
     * or as an array with the field name as key and field data as value.
     * If value is an array, the
     * Content-Type header will be set to
     * multipart/form-data.
     * As of PHP 5.2.0, value must be an array if
     * files are passed to this option with the @ prefix.
     * As of PHP 5.5.0, the @ prefix is deprecated and
     * files can be sent using CURLFile. The
     * @ prefix can be disabled for safe passing of
     * values beginning with @ by setting the
     * CURLOPT_SAFE_UPLOAD option to TRUE.
     *
     * @param array $postFields
     * @param bool  $multiPartFormData
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setPostFields(array $postFields, $multiPartFormData = false)
    {
        $this->setOption(
            CURLOPT_POSTFIELDS,
            $multiPartFormData ? $postFields : http_build_query($postFields)
        );

        return $this;
    }

    /**
     * The HTTP proxy to tunnel requests through.
     *
     * @param string $proxy
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setProxy($proxy)
    {
        return $this->setStringOption(CURLOPT_PROXY, $proxy);
    }

    /**
     * A username and password formatted as
     * "[username]:[password]" to use for the
     * connection to the proxy.
     *
     * @param string $proxyUserAndPassword
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setProxyUserAndPassword($proxyUserAndPassword)
    {
        return $this->setStringOption(CURLOPT_PROXYUSERPWD, $proxyUserAndPassword);
    }

    /**
     * A filename to be used to seed the random number generator for SSL.
     *
     * @param string $randomFile
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setRandomFile($randomFile)
    {
        return $this->setStringOption(CURLOPT_RANDOM_FILE, $randomFile);
    }

    /**
     * Range(s) of data to retrieve in the format
     * "X-Y" where X or Y are optional. HTTP transfers
     * also support several intervals, separated with commas in the format
     * "X-Y,N-M".
     *
     * @param string $range
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setRange($range)
    {
        return $this->setStringOption(CURLOPT_RANGE, $range);
    }

    /**
     * The contents of the "Referer: " header to be used
     * in a HTTP request.
     *
     * @param string $referer
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setReferer($referer)
    {
        return $this->setStringOption(CURLOPT_REFERER, $referer);
    }

    /**
     * A string containing 32 hexadecimal digits. The string should be the
     * MD5 checksum of the remote host's public key, and libcurl will reject
     * the connection to the host unless the md5sums match.
     * This option is only for SCP and SFTP transfers.
     * Added in cURL 7.17.1.
     *
     * @param string $sshHostPublicKeyMd5
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSshHostPublicKeyMd5($sshHostPublicKeyMd5)
    {
        return $this->setStringOption(CURLOPT_SSH_HOST_PUBLIC_KEY_MD5, $sshHostPublicKeyMd5);
    }

    /**
     * The file name for your public key. If not used, libcurl defaults to
     * $HOME/.ssh/id_dsa.pub if the HOME environment variable is set,
     * and just "id_dsa.pub" in the current directory if HOME is not set.
     * Added in cURL 7.16.1.
     *
     * @param string $sshPublicKeyFile
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSshPublicKeyFile($sshPublicKeyFile)
    {
        return $this->setStringOption(CURLOPT_SSH_PUBLIC_KEYFILE, $sshPublicKeyFile);
    }

    /**
     * The file name for your private key. If not used, libcurl defaults to
     * $HOME/.ssh/id_dsa if the HOME environment variable is set,
     * and just "id_dsa" in the current directory if HOME is not set.
     * If the file is password-protected, set the password with
     * CURLOPT_KEYPASSWD.
     * Added in cURL 7.16.1.
     *
     * @param string $sshPrivateKeyFile
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSshPrivateKeyFile($sshPrivateKeyFile)
    {
        return $this->setStringOption(CURLOPT_SSH_PRIVATE_KEYFILE, $sshPrivateKeyFile);
    }

    /**
     * A list of ciphers to use for SSL. For example,
     * RC4-SHA and TLSv1 are valid
     * cipher lists.
     *
     * @param string $sslCipherList
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslCipherList($sslCipherList)
    {
        return $this->setStringOption(CURLOPT_SSL_CIPHER_LIST, $sslCipherList);
    }

    /**
     * The name of a file containing a PEM formatted certificate.
     *
     * @param string $sslCertificate
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslCertificate($sslCertificate)
    {
        return $this->setStringOption(CURLOPT_SSLCERT, $sslCertificate);
    }

    /**
     * The password required to use the
     * CURLOPT_SSLCERT certificate.
     *
     * @param string $sslCertificatePassword
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslCertificatePassword($sslCertificatePassword)
    {
        return $this->setStringOption(CURLOPT_SSLCERTPASSWD, $sslCertificatePassword);
    }

    /**
     * The format of the certificate. Supported formats are
     * "PEM" (default), "DER",
     * and "ENG".
     * Added in cURL 7.9.3.
     *
     * @param string $sslCertificateType
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslCertificateType($sslCertificateType)
    {
        return $this->setStringOption(CURLOPT_SSLCERTTYPE, $sslCertificateType);
    }

    /**
     * The identifier for the crypto engine of the private SSL key
     * specified in CURLOPT_SSLKEY.
     *
     * @param string $sslEngine
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslEngine($sslEngine)
    {
        return $this->setStringOption(CURLOPT_SSLENGINE, $sslEngine);
    }

    /**
     * The identifier for the crypto engine used for asymmetric crypto
     * operations.
     *
     * @param string $sslEngineDefault
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslEngineDefault($sslEngineDefault)
    {
        return $this->setStringOption(CURLOPT_SSLENGINE_DEFAULT, $sslEngineDefault);
    }

    /**
     * The name of a file containing a private SSL key.
     *
     * @param string $sslKey
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslKey($sslKey)
    {
        return $this->setStringOption(CURLOPT_SSLKEY, $sslKey);
    }

    /**
     * The secret password needed to use the private SSL key specified in
     * CURLOPT_SSLKEY.
     * Note:
     * Since this option contains a sensitive password, remember to keep
     * the PHP script it is contained within safe.
     *
     * @param string $sslKeyPassword
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslKeyPassword($sslKeyPassword)
    {
        return $this->setStringOption(CURLOPT_SSLKEYPASSWD, $sslKeyPassword);
    }

    /**
     * The key type of the private SSL key specified in
     * CURLOPT_SSLKEY. Supported key types are
     * "PEM" (default), "DER",
     * and "ENG".
     *
     * @param string $sslKeyType
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setSslKeyType($sslKeyType)
    {
        return $this->setStringOption(CURLOPT_SSLKEYTYPE, $sslKeyType);
    }

    /**
     * The contents of the "User-Agent: " header to be
     * used in a HTTP request.
     *
     * @param string $userAgent
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setUserAgent($userAgent)
    {
        return $this->setStringOption(CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * A username and password formatted as
     * "[username]:[password]" to use for the
     * connection.
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setUserAndPassword($username, $password)
    {
        if (!is_string($username)) {
            throw new \InvalidArgumentException('setUserAndPassword method only accepts string.');
        }

        if (!is_string($password)) {
            throw new \InvalidArgumentException('setUserAndPassword method only accepts string.');
        }

        $this->setOption(CURLOPT_USERPWD, $username . ':' . $password);

        return $this;
    }

    /**
     * An array of HTTP 200 responses that will be treated as valid
     * responses and not as errors.
     * Added in cURL 7.10.3.
     *
     * @param array $http200aliases
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setHttp200aliases(array $http200aliases)
    {
        return $this->setOption(CURLOPT_HTTP200ALIASES, $http200aliases);
    }

    /**
     * An array of HTTP header fields to set, in the format
     * array('Content-type: text/plain', 'Content-length: 100')
     *
     * @param array $httpHeader
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setHttpHeader(array $httpHeader)
    {
        return $this->setOption(CURLOPT_HTTPHEADER, $httpHeader);
    }

    /**
     * An array of FTP commands to execute on the server after the FTP
     * request has been performed.
     *
     * @param array $postQuote
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setPostQuote(array $postQuote)
    {
        return $this->setOption(CURLOPT_POSTQUOTE, $postQuote);
    }

    /**
     * An array of FTP commands to execute on the server prior to the FTP
     * request.
     *
     * @param array $quote
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setQuote(array $quote)
    {
        return $this->setOption(CURLOPT_QUOTE, $quote);
    }

    /**
     * The file that the transfer should be written to. The default
     * is STDOUT (the browser window).
     *
     * @param resource $file
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setFile($file)
    {
        if (!is_resource($file)) {
            throw new \InvalidArgumentException('setFile method only accepts resource.');
        }

        return $this->setOption(CURLOPT_FILE, $file);
    }

    /**
     * An alternative location to output errors to instead of
     * STDERR.
     *
     * @param resource $stderr
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setStderr($stderr)
    {
        return $this->setResourceOption(CURLOPT_STDERR, $stderr);
    }

    /**
     * The file that the header part of the transfer is written to.
     *
     * @param resource $writeHeader
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setWriteHeader($writeHeader)
    {
        return $this->setResourceOption(CURLOPT_WRITEHEADER, $writeHeader);
    }

    /**
     * A callback accepting two parameters.
     * The first is the cURL resource, the second is a
     * string with the header data to be written. The header data must
     * be written when by this callback. Return the number of
     * bytes written.
     *
     * @param string|array $headerFunction
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setHeaderFunction($headerFunction)
    {
        return $this->setOption(CURLOPT_HEADERFUNCTION, $headerFunction);
    }

    /**
     * A callback accepting three parameters.
     * The first is the cURL resource, the second is a
     * string containing a password prompt, and the third is the maximum
     * password length. Return the string containing the password.
     *
     * @param string $passwordFunction
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setPasswordFunction($passwordFunction)
    {
        return $this->setStringOption(CURLOPT_PASSWDFUNCTION, $passwordFunction);
    }

    /**
     * A callback accepting five parameters.
     * The first is the cURL resource, the second is the total number of
     * bytes expected to be downloaded in this transfer, the third is
     * the number of bytes downloaded so far, the fourth is the total
     * number of bytes expected to be uploaded in this transfer, and the
     * fifth is the number of bytes uploaded so far.
     * Note:
     * The callback is only called when the CURLOPT_NOPROGRESS
     * option is set to FALSE.
     * Return a non-zero value to abort the transfer. In which case, the
     * transfer will set a CURLE_ABORTED_BY_CALLBACK
     * error.
     *
     * @param string $progressFunction
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setProgressFunction($progressFunction)
    {
        return $this->setStringOption(CURLOPT_PROGRESSFUNCTION, $progressFunction);
    }

    /**
     * A callback accepting three parameters.
     * The first is the cURL resource, the second is a
     * stream resource provided to cURL through the option
     * CURLOPT_INFILE, and the third is the maximum
     * amount of data to be read. The callback must return a string
     * with a length equal or smaller than the amount of data requested,
     * typically by reading it from the passed stream resource. It should
     * return an empty string to signal EOF.
     *
     * @param string $readFunction
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setReadFunction($readFunction)
    {
        return $this->setStringOption(CURLOPT_READFUNCTION, $readFunction);
    }

    /**
     * A callback accepting two parameters.
     * The first is the cURL resource, and the second is a
     * string with the data to be written. The data must be saved by
     * this callback. It must return the exact number of bytes written
     * or the transfer will be aborted with an error.
     *
     * @param string $writeFunction
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setWriteFunction($writeFunction)
    {
        return $this->setStringOption(CURLOPT_WRITEFUNCTION, $writeFunction);
    }

    /**
     * A result of curl_share_init(). Makes the cURL handle to use the data from the shared handle.
     *
     * @param mixed $share
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setShare($share)
    {
        return $this->setOption(CURLOPT_SHARE, $share);
    }

    /**
     * Close a cURL session
     * Closes a cURL session and frees all resources. The cURL handle, $this->curl, is also deleted.
     *
     * @see http://php.net/manual/en/function.curl-close.php
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Close a cURL session
     * Closes a cURL session and frees all resources. The cURL handle, $this->curl, is also deleted.
     *
     * @see http://php.net/manual/en/function.curl-close.php
     */
    public function close()
    {
        if (is_resource($this->handle)) {
            curl_close($this->handle);
        }
    }

    /**
     * Copy a cURL handle along with all of its preferences
     *
     * @see http://php.net/manual/en/function.curl-copy-handle.php
     */
    public function __clone()
    {
        $this->handle = curl_copy_handle($this->handle);
    }

    /**
     * URL encodes the given string
     *
     * @see http://php.net/manual/en/function.curl-escape.php
     *
     * @param $string
     *
     * @return string
     * @throws \Exception
     */
    public function escape($string)
    {
        $return = curl_escape($this->handle, $string);
        if ($return === false) {
            throw new \Exception('An error occurred');
        }

        return $return;
    }

    /**
     * Perform a cURL session
     *
     * @see http://php.net/manual/en/function.curl-exec.php
     * @return mixed
     * @throws \Exception
     */
    public function execute()
    {
        $this->prepareCookies();

        $return = curl_exec($this->handle);
        if ($return === false) {
            throw new Exception(curl_error($this->handle), curl_errno($this->handle));
        }

        return $return;
    }

    private function prepareCookies()
    {
        $cookieParts = [];
        foreach ($this->cookies as $key => $value) {
            $cookieParts[] = $key . '=' . $value;
        }
        if (!empty($cookieParts)) {
            $this->setCookieString(implode('; ', $cookieParts));
        }

        return $this;
    }

    /**
     * The contents of the "Cookie: " header to be
     * used in the HTTP request.
     * Note that multiple cookies are separated with a semicolon followed
     * by a space (e.g., "fruit=apple; colour=red")
     *
     * @param string $cookie
     *
     * @return $this
     * @throws \ErrorException
     */
    private function setCookieString($cookie)
    {
        return $this->setStringOption(CURLOPT_COOKIE, $cookie);
    }

    /**
     * Last effective URL
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getEffectiveUrl()
    {
        return $this->getOneInfo(CURLINFO_EFFECTIVE_URL);
    }

    /**
     * Get information regarding a specific transfer
     *
     * @see http://php.net/manual/en/function.curl-getinfo.php
     *
     * @param $info
     *
     * @return mixed
     * @throws \Exception
     */
    private function getOneInfo($info)
    {
        $return = curl_getinfo($this->handle, $info);
        if ($return === false) {
            throw new Exception(curl_error($this->handle), curl_errno($this->handle));
        }

        return $return;
    }

    /**
     * Last received HTTP code
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getHttpCode()
    {
        return $this->getOneInfo(CURLINFO_HTTP_CODE);
    }

    /**
     * Remote time of the retrieved document, if -1 is returned the time of the document is unknown
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getFileTime()
    {
        return $this->getOneInfo(CURLINFO_FILETIME);
    }

    /**
     * Total transaction time in seconds for last transfer
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getTotalTime()
    {
        return $this->getOneInfo(CURLINFO_TOTAL_TIME);
    }

    /**
     * Time in seconds until name resolving was complete
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getNameLookupTime()
    {
        return $this->getOneInfo(CURLINFO_NAMELOOKUP_TIME);
    }

    /**
     * Time in seconds it took to establish the connection
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getConnectTime()
    {
        return $this->getOneInfo(CURLINFO_CONNECT_TIME);
    }

    /**
     * Time in seconds from start until just before file transfer begins
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getPreTransferTime()
    {
        return $this->getOneInfo(CURLINFO_PRETRANSFER_TIME);
    }

    /**
     * Time in seconds until the first byte is about to be transferred
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getStartTransferTime()
    {
        return $this->getOneInfo(CURLINFO_STARTTRANSFER_TIME);
    }

    /**
     * Number of redirects, with the CURLOPT_FOLLOWLOCATION option enabled
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getRedirectCount()
    {
        return $this->getOneInfo(CURLINFO_REDIRECT_COUNT);
    }

    /**
     * Time in seconds of all redirection steps before final transaction was started, with the CURLOPT_FOLLOWLOCATION
     * option enabled
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getRedirectTime()
    {
        return $this->getOneInfo(CURLINFO_REDIRECT_TIME);
    }

    /**
     * With the CURLOPT_FOLLOWLOCATION option disabled: redirect URL found in the last transaction, that should be
     * requested manually next. With the CURLOPT_FOLLOWLOCATION option enabled: this is empty. The redirect URL in this
     * case is available in CURLINFO_EFFECTIVE_URL
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getRedirectUrl()
    {
        return $this->getOneInfo(CURLINFO_REDIRECT_URL);
    }

    /**
     * IP address of the most recent connection
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getPrimaryIp()
    {
        return $this->getOneInfo(CURLINFO_PRIMARY_IP);
    }

    /**
     * Destination port of the most recent connection
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getPrimaryPort()
    {
        return $this->getOneInfo(CURLINFO_PRIMARY_PORT);
    }

    /**
     * Local (source) IP address of the most recent connection
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getLocalIp()
    {
        return $this->getOneInfo(CURLINFO_LOCAL_IP);
    }

    /**
     * Local (source) port of the most recent connection
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getLocalPort()
    {
        return $this->getOneInfo(CURLINFO_LOCAL_PORT);
    }

    /**
     * Total number of bytes uploaded
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getSizeUpload()
    {
        return $this->getOneInfo(CURLINFO_SIZE_UPLOAD);
    }

    /**
     * Total number of bytes downloaded
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getSizeDownload()
    {
        return $this->getOneInfo(CURLINFO_SIZE_DOWNLOAD);
    }

    /**
     * Average download speed
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getSpeedDownload()
    {
        return $this->getOneInfo(CURLINFO_SPEED_DOWNLOAD);
    }

    /**
     * Average upload speed
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getSpeedUpload()
    {
        return $this->getOneInfo(CURLINFO_SPEED_UPLOAD);
    }

    /**
     * Total size of all headers received
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getHeaderSize()
    {
        return $this->getOneInfo(CURLINFO_HEADER_SIZE);
    }

    /**
     * The request string sent. For this to
     * work, add the CURLINFO_HEADER_OUT option to the handle by calling
     * curl_setopt()
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getHeaderOut()
    {
        return $this->getOneInfo(CURLINFO_HEADER_OUT);
    }

    /**
     * Total size of issued requests, currently only for HTTP requests
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getRequestSize()
    {
        return $this->getOneInfo(CURLINFO_REQUEST_SIZE);
    }

    /**
     * Result of SSL certification verification requested by setting CURLOPT_SSL_VERIFYPEER
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getSslVerifyResult()
    {
        return $this->getOneInfo(CURLINFO_SSL_VERIFYRESULT);
    }

    /**
     * content-length of download, read from Content-Length: field
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getContentLengthDownload()
    {
        return $this->getOneInfo(CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    }

    /**
     * Specified size of upload
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getContentLengthUpload()
    {
        return $this->getOneInfo(CURLINFO_CONTENT_LENGTH_UPLOAD);
    }

    /**
     * Content-Type: of the requested document, NULL indicates server did not send valid Content-Type: header
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getContentType()
    {
        return $this->getOneInfo(CURLINFO_CONTENT_TYPE);
    }

    /**
     * Private data associated with this cURL handle, previously set with the CURLOPT_PRIVATE option of curl_setopt()
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getPrivate()
    {
        return $this->getOneInfo(CURLINFO_PRIVATE);
    }

    /**
     * TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive,
     * PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
     *
     * @param bool $followLocation
     *
     * @return $this
     * @throws \ErrorException
     */
    public function followLocation($followLocation = true)
    {
        return $this->setBooleanOption(CURLOPT_FOLLOWLOCATION, $followLocation);
    }

    /**
     * Gets the cURL resource
     *
     * @return resource
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get information regarding a specific transfer
     *
     * @return Info
     */
    public function getInfo()
    {
        return new Info(curl_getinfo($this->handle));
    }

    /**
     * HTTP PUT a file.
     *
     * @param string $inFile     The file that the transfer should be read from when uploading.
     * @param int    $inFileSize The expected size, in bytes, of the file when uploading a file to a remote site.
     *                           Note that using this option will not stop libcurl from sending more data, as exactly
     *                           what is sent depends on CURLOPT_READFUNCTION.
     *
     * @return $this
     * @throws \ErrorException
     */
    public function put($inFile, $inFileSize)
    {
        $this->setOption(CURLOPT_PUT, true);
        $this->setOption(CURLOPT_INFILE, $inFile);
        $this->setOption(CURLOPT_INFILESIZE, $inFileSize);

        return $this;
    }

    /**
     * Gets the url used to initialise the session.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * The URL to fetch. This can also be set when initializing a
     * session with curl_init().
     *
     * @param string $url
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setUrl($url)
    {
        $this->setStringOption(CURLOPT_URL, $url);
        $this->url = $url;
        return $this;
    }

    private function setBooleanOption($key, $value)
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException($key . ' only accepts boolean.');
        }

        return $this->setOption($key, $value);
    }

    private function setStringOption($key, $value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException($key . ' only accepts string.');
        }

        return $this->setOption($key, $value);
    }

    private function setIntegerOption($key, $value)
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException($key . ' only accepts integer.');
        }

        return $this->setOption($key, $value);
    }

    private function setResourceOption($key, $value)
    {
        if (!is_resource($value)) {
            throw new \InvalidArgumentException($key . ' only accepts resource.');
        }

        return $this->setOption($key, $value);
    }

    /**
     * TRUE to disable support for the @ prefix for
     * uploading files in CURLOPT_POSTFIELDS, which
     * means that values starting with <em>@</em> can be safely
     * passed as fields. CURLFile may be used for uploads instead.
     */
    /*   public function safeUpload()
       {
           if (!is_bool($ftpUseEPSV)) {
               throw new \InvalidArgumentException('ftpUseEPSV method only accepts string.');
           }

           $this->setOption(CURLOPT_SAFE_UPLOAD, $value);

           return $this;
       }*/

    /**
     * FALSE to stop cURL from verifying the peer's
     * certificate. Alternate certificates to verify against can be
     * specified with the CURLOPT_CAINFO option
     * or a certificate directory can be specified with the
     * CURLOPT_CAPATH option.
     */
    /*   public function sslVerifyPeer()
       {
           if (!is_bool($ftpUseEPSV)) {
               throw new \InvalidArgumentException('ftpUseEPSV method only accepts string.');
           }

           $this->setOption(CURLOPT_SSL_VERIFYPEER, $value);

           return $this;
       }*/

    /**
     * TRUE to keep sending the username and password
     * when following locations (using
     * CURLOPT_FOLLOWLOCATION), even when the
     * hostname has changed.
     */
    /*   public function UNRESTRICTED_AUTH()
       {
           if (!is_bool($ftpUseEPSV)) {
               throw new \InvalidArgumentException('ftpUseEPSV method only accepts string.');
           }

           $this->setOption(CURLOPT_UNRESTRICTED_AUTH, $value);

           return $this;
       }

       public function setUserAgent($user_agent)
       {
           $this->setOption(CURLOPT_USERAGENT, $user_agent);
       }

       public function get($url, $data = [])
       {
           if (count($data) > 0) {
               $this->setOption(CURLOPT_URL, $url . '?' . http_build_query($data));
           }
           else {
               $this->setOption(CURLOPT_URL, $url);
           }
           $this->setOption(CURLOPT_HTTPGET, true);
           $this->_exec();
       }

       public function _exec()
       {
           $this->response           = curl_exec($this->handle);
           $this->curl_error_code    = curl_errno($this->handle);
           $this->curl_error_message = curl_error($this->handle);
           $this->curl_error         = !($this->curl_error_code === 0);
           $this->http_status_code   = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
           $this->http_error         = in_array(floor($this->http_status_code / 100), [4, 5]);
           $this->error              = $this->curl_error || $this->http_error;
           $this->error_code         = $this->error ? ($this->curl_error ? $this->curl_error_code : $this->http_status_code) : 0;

           $this->request_headers  = preg_split('/\r\n/', curl_getinfo($this->handle, CURLINFO_HEADER_OUT), null,
                                                PREG_SPLIT_NO_EMPTY);
           $this->response_headers = '';
           if (!(strpos($this->response, "\r\n\r\n") === false)) {
               list($response_header, $this->response) = explode("\r\n\r\n", $this->response, 2);
               if ($response_header === 'HTTP/1.1 100 Continue') {
                   list($response_header, $this->response) = explode("\r\n\r\n", $this->response, 2);
               }
               $this->response_headers = preg_split('/\r\n/', $response_header, null, PREG_SPLIT_NO_EMPTY);
           }

           $this->http_error_message = $this->error ? (isset($this->response_headers['0']) ? $this->response_headers['0'] : '') : '';
           $this->error_message      = $this->curl_error ? $this->curl_error_message : $this->http_error_message;

           return $this->error_code;
       }

       public function put($url, $data = [])
       {
           $this->setOption(CURLOPT_URL, $url . '?' . http_build_query($data));
           $this->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
           $this->_exec();
       }

       public function setBasicAuthentication($username, $password)
       {
           $this->setHttpAuth(self::AUTH_BASIC);
           $this->setOption(CURLOPT_USERPWD, $username . ':' . $password);
       }

       public function getHandle()
       {
           return $this->handle;
       }

       protected function setHttpAuth($httpauth)
       {
           $this->setOption(CURLOPT_HTTPAUTH, $httpauth);
       }

       public function setHeader($key, $value)
       {
           $this->_headers[ $key ] = $key . ': ' . $value;
           $this->setOption(CURLOPT_HTTPHEADER, array_values($this->_headers));
       }

       public function setReferrer($referrer)
       {
           $this->setOption(CURLOPT_REFERER, $referrer);
       }

       public function setCookie($key, $value)
       {
           $this->_cookies[ $key ] = $value;
           $this->setOption(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
       }

       public function reset()
       {
           $this->close();
           $this->_cookies           = [];
           $this->_headers           = [];
           $this->error              = false;
           $this->error_code         = 0;
           $this->error_message      = null;
           $this->curl_error         = false;
           $this->curl_error_code    = 0;
           $this->curl_error_message = null;
           $this->http_error         = false;
           $this->http_status_code   = 0;
           $this->http_error_message = null;
           $this->request_headers    = null;
           $this->response_headers   = null;
           $this->response           = null;
           curl_reset($this->handle);
       }
       */
}
