<?php

declare(strict_types=1);

namespace Glu\Tests\Http;

use Glu\Http\Response;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function test_a(): void
    {
        $response = new Response('this is the content', 200, ['content-type' => 'text/plain']);

        self::assertSame(
            "HTTP/1.1 200 OK\r\ncontent-type: text/plain\r\n\r\nthis is the content",
            (string) $response
        );
    }
}
