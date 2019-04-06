<?php

namespace huenisys\Utils\Tests;

use PHPUnit\Framework\TestCase;
use huenisys\Utils\MessageBag;
use huenisys\Utils\Common\InterfaceMessageBag;

class MessageBagTest extends TestCase
{
    /** @test **/
    public function getInstance_ofProperType()
    {
        $messageBag = new MessageBag();
        $this->assertInstanceOf(MessageBag::class, $messageBag);
        $this->assertInstanceOf(InterfaceMessageBag::class, $messageBag);
        $this->assertInstanceOf(InterfaceMessageBag::class, $messageBag->getInstance());
    }

    /** @test **/
    public function initWithNoParams_usesDefaultArray()
    {
        $this->assertEquals('Hello World', (new MessageBag())->getMessage('hello'));
    }

    /** @test **/
    public function initWithParams_usesNewArray()
    {
        $this->assertEquals('Hello Abi', (new MessageBag([
            'hello' => 'Hello Abi'
        ]))->getMessage('hello'));
    }

    /** @test **/
    public function getEntireMessagesArray()
    {
        $this->assertEquals([], (new MessageBag([]))->getMessages());
    }

    /** @test **/
    public function getInstances()
    {
        $instance = new MessageBag();
        $this->assertEquals($instance, $instance->getInstance());
        $this->assertEquals($instance, $instance->getMessageBag());
    }

    /** @test **/
    public function getNullIfKeyNotDefined()
    {
        var_dump($val = (new MessageBag())->getMessage('unknown'));
        $this->assertEquals(null, $val);
    }

    /** @test **/
    public function allowSettingMessagesAfterInstanceCreation()
    {
        $messageBag = new MessageBag(['test'=>'hey abi']);
        $this->assertEquals('hey abi', $messageBag->getMessage('test'));

        $messageBag->setMessageBag(['test'=>'hey paul']);

        $this->assertEquals(
            'hey paul',
            $messageBag->getMessage('test')
        );
    }
}
