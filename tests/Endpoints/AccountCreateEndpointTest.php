<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use TwinePM\Endpoints\AccountCreateEndpoint;

class AccountCreateEndpointTest extends TestCase {
    function testInvoke() {
        $stub = $this
            ->getMockBuilder(AccountCreateEndpoint::class)
            ->getMock();

        $containerMock = $this
            ->getMockBuilder(Container::class)
            ->getMock();

        $this->assertTrue($stub($containerMock) instanceof ResponseInterface);
    }
}