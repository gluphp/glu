<?php declare(strict_types = 1);

namespace Glu\Http;

use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ResponseFactoryInterface;

final class Factory {

    private ResponseFactoryInterface $psrResponseFactory;

    public function createResponse(int $status, string $contents = '', array $headers = []): Response
    {
        return new Response(
            $contents,
            $status,
            $headers
        );
    }
}
