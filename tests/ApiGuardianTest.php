<?php

use ObjectivePHP\Application\Config\Param;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 16/04/2018
 * Time: 17:08
 */

final class ApiGuardianTest extends TestCase
{
    /** @var \Yoctu\ApiGuardian\ApiGuardian */
    protected $instance;

    protected function setUp()
    {
        $this->instance = new \Yoctu\ApiGuardian\ApiGuardian();
    }

    public function testPassWhenNoApiKeyWhereSetted()
    {
        $app = $this->getMockBuilder(\ObjectivePHP\Application\ApplicationInterface::class)->getMock();
        $config = $this->getMockBuilder(\ObjectivePHP\Config\Config::class)->getMock();

        $config->expects($this->once())->method('subset')->with(Param::class)->willReturnSelf();
        $config->expects($this->once())->method('get')->with('api-keys')->willReturn([]);

        $app->expects($this->once())->method('getConfig')->willReturn($config);

        $this->instance->__invoke($app);
    }

    public function testThrowAnExceptionWhenNoTokenWhereProvided()
    {
        $app = $this->getMockBuilder(\ObjectivePHP\Application\ApplicationInterface::class)->getMock();
        $request = $this->getMockBuilder(\ObjectivePHP\Message\Request\HttpRequest::class)->getMock();
        $config = $this->getMockBuilder(\ObjectivePHP\Config\Config::class)->getMock();

        $request->expects($this->once())->method('hasHeader')->with('Authorization')->willReturn(false);

        $config->expects($this->once())->method('subset')->with(Param::class)->willReturnSelf();
        $config->expects($this->once())->method('get')->with('api-keys')->willReturn(['you-shall-not-pass-!']);

        $app->expects($this->once())->method('getConfig')->willReturn($config);
        $app->expects($this->once())->method('getRequest')->willReturn($request);

        $this->expectException(\Yoctu\ApiGuardian\Exception\NoTokenProvidedException::class);
        $this->instance->__invoke($app);
    }

    public function testThrowAnExceptionWhenTheTokenProvidedWasIncorrect()
    {
        $app = $this->getMockBuilder(\ObjectivePHP\Application\ApplicationInterface::class)->getMock();
        $request = $this->getMockBuilder(\ObjectivePHP\Message\Request\HttpRequest::class)->getMock();
        $config = $this->getMockBuilder(\ObjectivePHP\Config\Config::class)->getMock();

        $request->expects($this->once())->method('hasHeader')->with('Authorization')->willReturn(true);
        $request->expects($this->once())->method('getHeaderLine')->with('Authorization')->willReturn('h@xxt0ken');

        $config->expects($this->once())->method('subset')->with(Param::class)->willReturnSelf();
        $config->expects($this->once())->method('get')->with('api-keys')->willReturn(['you-shall-not-pass-!']);

        $app->expects($this->once())->method('getConfig')->willReturn($config);
        $app->expects($this->once())->method('getRequest')->willReturn($request);

        $this->expectException(\Yoctu\ApiGuardian\Exception\InvalidTokenException::class);
        $this->instance->__invoke($app);
    }

    public function testPassWhenTheTokenProvidedWasCorrect()
    {
        $app = $this->getMockBuilder(\ObjectivePHP\Application\ApplicationInterface::class)->getMock();
        $request = $this->getMockBuilder(\ObjectivePHP\Message\Request\HttpRequest::class)->getMock();
        $config = $this->getMockBuilder(\ObjectivePHP\Config\Config::class)->getMock();

        $request->expects($this->once())->method('hasHeader')->with('Authorization')->willReturn(true);
        $request->expects($this->once())->method('getHeaderLine')->with('Authorization')->willReturn('wowsuchtokenverysecure');

        $config->expects($this->once())->method('subset')->with(Param::class)->willReturnSelf();
        $config->expects($this->once())->method('get')->with('api-keys')->willReturn(['wowsuchtokenverysecure', 'othertoken']);

        $app->expects($this->once())->method('getConfig')->willReturn($config);
        $app->expects($this->once())->method('getRequest')->willReturn($request);

        $this->instance->__invoke($app);
    }
    public function testPassWithOptionalKeys()
    {
        $app = $this->getMockBuilder(\ObjectivePHP\Application\ApplicationInterface::class)->getMock();
        $request = $this->getMockBuilder(\ObjectivePHP\Message\Request\HttpRequest::class)->getMock();
        $config = $this->getMockBuilder(\ObjectivePHP\Config\Config::class)->getMock();

        $request->expects($this->once())->method('hasHeader')->with('Authorization')->willReturn(true);
        $request->expects($this->once())->method('getHeaderLine')->with('Authorization')->willReturn('optionalkey');

        $config->expects($this->once())->method('subset')->with(Param::class)->willReturnSelf();
        $config->expects($this->once())->method('get')->with('api-keys')->willReturn([]);

        $this->instance->setOptionalKeys(['optionalkey']);

        $app->expects($this->once())->method('getConfig')->willReturn($config);
        $app->expects($this->once())->method('getRequest')->willReturn($request);

        $this->instance->__invoke($app);
    }
}
