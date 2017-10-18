<?php
use PHPUnit\Framework\TestCase;
use TwinePM\Endpoints\AbstractEndpoint;
class AbstractEndpointTest extends TestCase {
    function testGetOptionsJson() {
        $stub = $this->getMockForAbstractClass(AbstractEndpoint::class);

        $retObj = [
            "foo" => "1",
            "bar" => "2",
        ];

        $stub
            ->expects($this->once())
            ->method("getOptionsObject")
            ->willReturn($retObj);

        $this->assertEquals($stub->getOptionsJson(), json_encode($retObj));
    }
}