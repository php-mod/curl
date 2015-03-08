<?php

$html = file_get_contents('tmp.html');

//preg_match_all('#<tr>(.*?)</tr>#s', $html, $trs); // tables
preg_match_all('#<li(?:.*?)>(.*?)</li>#s', $html, $trs); // lists

ob_start();
foreach($trs[1] as $tr) {
    //preg_match_all('#<td(?:.*?)>(.*?)</td>#s', $tr, $tds);

    $code = trim(strip_tags($tr));
    //$comment = trim(strip_tags($tds[2][1]));
    //$note = trim(strip_tags($tds[2][2]));

    $line = trim(strip_tags($tr));

    //list($code, $comment) = explode('-', $line, 2);

    $code = trim($code, '"');
    //$comment = trim($comment);

    $var = get_var($code);

    echo '    /**' . PHP_EOL;
    //echo to_comment($comment) . PHP_EOL;
    //echo to_comment($note) . PHP_EOL;
    //echo '     *' . PHP_EOL;
    //echo '     * @param string $'. $var . PHP_EOL;
    //echo '     *' . PHP_EOL;
    echo '     * @return mixed' . PHP_EOL;
    //echo '     * @throws \ErrorException' . PHP_EOL;
    echo '     */' . PHP_EOL;
    echo '    public function get' . ucfirst($var) . '()' . PHP_EOL;
    echo '    {' . PHP_EOL;
    //echo '        if (!is_string($' . $var . ')) {' . PHP_EOL;
    //echo '            throw new \InvalidArgumentException(\'set' . ucfirst($var) . ' method only accepts string.\');' . PHP_EOL;
    //echo '        }' . PHP_EOL;
    //echo PHP_EOL;
    //echo '        return $this->getInfo(' . $code . ');' . PHP_EOL;
    //echo PHP_EOL;
    echo '        return $this->info[\'' . $code . '\'];' . PHP_EOL;
    echo '    }' . PHP_EOL;
    echo PHP_EOL;

}
file_put_contents('tmp.txt', ob_get_contents());
ob_clean();

function to_comment($string) {
    $lines = explode(PHP_EOL, $string);
    $comment = [];
    foreach($lines as $line) {
        $comment[] = '     * ' . trim($line);
    }
    return implode(PHP_EOL, $comment);
}

function get_var($code) {
    $var = strtolower($code);

    $var = str_replace('curlinfo_', '', $var);
    $var = str_replace('size', '_size', $var);
    $var = str_replace('policy', '_policy', $var);
    $var = str_replace('timeout', '_timeout', $var);
    $var = str_replace('auth', '_auth', $var);
    $var = str_replace('ssl', '_ssl', $var);
    $var = str_replace('file', '_file', $var);
    $var = str_replace('connect', '_connect', $var);
    $var = str_replace('redirect', '_REDIRECT', $var);
    $var = str_replace('redir', '_redirect', $var);
    $var = str_replace('port', '_port', $var);
    $var = str_replace('type', '_type', $var);
    $var = str_replace('host', '_host', $var);
    $var = str_replace('version', '_version', $var);
    $var = str_replace('condition', '_condition', $var);
    $var = str_replace('value', '_value', $var);
    $var = str_replace('resolve', '_resolve', $var);
    $var = str_replace('info', '_info', $var);
    $var = str_replace('cookie', '_cookie', $var);
    $var = str_replace('cookiejar', '_cookie_jar', $var);
    $var = str_replace('request', '_request', $var);
    $var = str_replace('socket', '_socket', $var);
    $var = str_replace('passwd', '_password', $var);
    $var = str_replace('fields', '_fields', $var);
    $var = str_replace('userpwd', '_user_and_password', $var);
    $var = str_replace('sslcert', '_ssl_certificate', $var);
    $var = str_replace('engine', '_engine', $var);
    $var = str_replace('key', '_key', $var);
    $var = str_replace('agent', '_agent', $var);
    $var = str_replace('header', '_header', $var);
    $var = str_replace('quote', '_quote', $var);
    $var = str_replace('function', '_function', $var);
    $var = str_replace('time', '_time', $var);
    $var = str_replace('lookup', '_lookup', $var);
    $var = str_replace('transfer', '_transfer', $var);
    $var = str_replace('result', '_RESULT', $var);

    $var = strtolower($var);

    $var = trim($var, '_');

    $words = explode('_', $var);

    $var = array_shift($words);
    foreach($words as $word) {
        $var .= ucfirst($word);
    }

    return $var;
}
