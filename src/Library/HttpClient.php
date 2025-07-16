<?php
namespace Library;
/**
 * HttpClient
 * https://gist.github.com/HanbitGaram/c49e5f2ea0800a8dcccb82bfc5a987aa 의 클래스 버전
 *
 * Usage:
 * ```php
 * $client = new HttpClient('POST', [
 *    'url' => 'https://example.com/api',
 *   'data' => ['key' => 'value'],
 *   'json' => true,
 *  'headers' => ['Custom-Header: value']
 * * ]);
 * * $response = $client->send();
 * * if ($response !== false) {
 *   echo "응답헤더: ";
 *  print_r($response['header']);
 * *  echo "응답 내용: ";
 * print_r($response['body']);
 * * } else {
 *  echo "요청 실패";
 * }
 */
class HttpClient
{
    private string $method;
    private array $args;
    private array $defaultArgs = [
        'url' => '',
        'headers' => [],
        'ua' => 'Mozilla/5.0 Application',
        'accept' => 'text/html, application/xhtml+xml, application/xml;q=0.9, */*;q=0.8',
        'timeout' => 3,
        'json' => true,
        'follow' => true,
        'showHeader' => true,
        'followCount' => 5,
        'verifyPeer' => false,
        'data' => [],
        'unauthorized' => false
    ];

    public function __construct(string $method = 'GET', array $args = [])
    {
        $this->method = strtoupper($method);
        $this->args = array_merge($this->defaultArgs, $args);
        $this->prepareHeaders();
    }

    private function prepareHeaders(): void
    {
        $this->args['headers'][] = 'User-Agent: ' . trim($this->args['ua']);

        if (!$this->args['json']) {
            $this->args['headers'][] = 'Accept: ' . trim($this->args['accept']);
        }
    }

    public function send(): array|false
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->args['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->args['timeout']);
        curl_setopt($ch, CURLOPT_HEADER, $this->args['showHeader']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->args['headers']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->args['follow']);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $this->args['followCount']);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->args['verifyPeer']);

        if ($this->method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            $postData = $this->args['json']
                ? json_encode($this->args['data'])
                : $this->args['data'];
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $res = curl_exec($ch);
        $retry = 0;
        while (curl_errno($ch) !== 0 && $retry < 3) {
            $res = curl_exec($ch);
            $retry++;
        }

        if (curl_errno($ch) !== 0) {
            curl_close($ch);
            echo "오류발생 : ". curl_error($ch) . "\n";
            return false;
        }

        curl_close($ch);
        return $this->parseResponse($res);
    }

    private function parseResponse(string $res): array
    {
        $response = ['header' => [], 'body' => null];
        $_parts = explode("\r\n\r\n", $res, 2);

        if (count($_parts) < 2) {
            $response['body'] = $res;
            return $response;
        }

        $_header = explode("\n", $_parts[0]);
        foreach ($_header as $val) {
            $val = str_replace("\r", "", $val);
            $position = strpos($val, ':');

            if ($position === false) {
                continue;
            }

            $_name = strtoupper(trim(substr($val, 0, $position)));
            $_value = trim(substr($val, $position + 1, 512));

            if ($_name === "" && str_starts_with($_value, 'HTTP/')) {
                $_name = "C-HTTP-PROTO";
            }

            $response['header'][$_name] = $_value;
        }

        $response['body'] = $_parts[1];

        $json = json_decode($response['body'], true);
        if ($json !== null) {
            $response['body'] = $json;
        }

        return $response;
    }
}
