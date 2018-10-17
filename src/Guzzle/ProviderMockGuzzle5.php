<?php

/*
 * PHPacto - Contract testing solution
 *
 * Copyright (c) 2018  Damian Długosz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Bigfoot\PHPacto\Guzzle;

use Bigfoot\PHPacto\PactInterface;
use Bigfoot\PHPacto\Test\PHPactoTestTrait;
use GuzzleHttp\Ring\Client\MockHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

class ProviderMockGuzzle5 implements ProviderMock
{
    use PHPactoTestTrait;

    /**
     * @var MockHandler
     */
    private $mock;

    public function handlePact(PactInterface $pact): void
    {
        $this->mock = new MockHandler(function (array $requestArray) use ($pact): array {
            $request = self::getRequestFromArray($requestArray);

            self::assertRequestMatchesPact($pact, $request);

            $response = $pact->getResponse()->getSample();

            return self::responseToArray($response);
        });
    }

    /**
     * @return MockHandler
     */
    public function getHandler()
    {
        return $this->mock;
    }

    private static function getRequestFromArray(array $request): RequestInterface
    {
        $uri = $request['uri'];
        $method = $request['http_method'];
        $headers = $request['headers'];

        $body = new Stream('php://memory', 'w');
        $body->write($request['body']);

        return new Request($uri, $method, $body, $headers);
    }

    private static function responseToArray(ResponseInterface $response): array
    {
        return [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => (string) $response->getBody(),
        ];
    }
}