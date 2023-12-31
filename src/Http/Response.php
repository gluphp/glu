<?php

declare(strict_types=1);

namespace Glu\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class Response
{
    private SymfonyResponse $symfonyResponse;

    public function __construct(
        string $contents,
        int $status = 200,
        array $headers = []
    ) {
        $this->symfonyResponse = new SymfonyResponse($contents, $status, $headers);
    }

    public static function createRedirect(string $location, int $statusCode = 302)
    {
        return new self('', $statusCode, [
            'location' => $location
        ]);
    }

    public function headers(): array
    {
        return $this->symfonyResponse->headers->all();
    }

    public function contents(): string
    {
        return $this->symfonyResponse->getContent();
    }

    public function setContents(string $contents): void
    {
        $this->symfonyResponse->setContent($contents);
    }

    public function statusCode(): int
    {
        return $this->symfonyResponse->getStatusCode();
    }

    public function addHeader(string $name, string $value): void
    {
        $this->symfonyResponse->headers->set($name, $value);
    }

    public function __toString(): string
    {
        return (string)$this->symfonyResponse;
    }


    public function replaceShortCodes(string $code)
    {

    }

    public function replace(array|string $pattern, array|string $replacement, int $limit = -1): int
    {
        $count = 0;
        $this->symfonyResponse->setContent(
            \preg_replace($pattern, $replacement, $this->symfonyResponse->getContent(), $limit, $count)
        );
        return $count;
    }

    public function setStatusCode(int $code): void
    {
        $this->symfonyResponse->setStatusCode($code);
    }
}
