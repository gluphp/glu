<?php declare(strict_types = 1);

namespace Glu\Http;

final class Response {

    private static $reasonPhrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
    ];

    public function __construct(
        public string $contents,
        public int $statusCode = 200,
        public array $headers = [])
    {
    }

    public static function redirect(string $location, int $statusCode = 302)
    {
        return new self('', $statusCode, [
            'location' => $location
        ]);
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function __toString(): string
    {
        $headers = '';
        foreach ($this->headers as $name => $value) {
            $headers .= $name .': '.$value."\r\n";
        }

        return sprintf(
            "HTTP/1.1 %d %s\r\n%s\r\n%s",
            $this->statusCode,
            self::$reasonPhrases[$this->statusCode],
            $headers,
            $this->contents
        );
    }
}
