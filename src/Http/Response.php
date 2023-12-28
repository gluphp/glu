<?php declare(strict_types = 1);

namespace Glu\Http;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;

final class Response {

    private ResponseInterface $psr7Response;

    public function __construct(
        string $contents,
        int $status = 200,
        array $headers = [])
    {
        $this->psr7Response = new Psr7Response($status, $headers, $contents);
    }

    public static function createRedirect(string $location, int $statusCode = 302)
    {
        return new self('', $statusCode, [
            'location' => $location
        ]);
    }

    public function psr7Response(): ResponseInterface
    {
        return $this->psr7Response;
    }

    public function headers(): array
    {
        return $this->psr7Response->getHeaders();
    }

    public function contents(): string
    {
        return $this->psr7Response->getBody()->getContents();
    }

    public function setContents(string $contents): void
    {
        $this->psr7Response->getBody()->write($contents);
    }

    public function statusCode(): int
    {
        return $this->psr7Response->getStatusCode();
    }

    public function addHeader(string $name, string $value): void
    {
        $this->psr7Response = $this->psr7Response->withHeader($name, $value);
    }

    public function __toString(): string
    {
        $headers = '';
        foreach ($this->psr7Response->getHeaders() as $name => $value) {
            $headers .= $this->psr7Response->getHeaderLine($name)."\r\n";
        }

        return sprintf(
            "HTTP/%s %d %s\r\n%s\r\n%s",
            $this->psr7Response->getProtocolVersion(),
            $this->statusCode(),
            $this->psr7Response->getReasonPhrase(),
            $headers,
            $this->contents
        );
    }


    public function replaceShortCodes(string $code)
    {

    }

    public function replaceBody(string $contents): void
    {
        $this->psr7Response->getBody()->write($contents);
    }
}
