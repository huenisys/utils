<?php

namespace huenisys\Utils\Tests;

use PHPUnit\Framework\TestCase;
use huenisys\Utils\MessageBagWithFormatting;
use huenisys\Utils\Common\InterfaceMessageBagWithFormatting;

class MessageBagWithFormattingTest extends TestCase
{
    public $msgBagF;

    public function setUp() :void
    {
        $this->msgBagF = new MessageBagWithFormatting([
            'hello' => 'Wassup %s?'
        ]);
    }

    /** @test **/
    public function getInstance_ofProperType()
    {
        $messageBag = new MessageBagWithFormatting();
        $this->assertInstanceOf(MessageBagWithFormatting::class, $messageBag);
        $this->assertInstanceOf(InterfaceMessageBagWithFormatting::class, $messageBag);
        $this->assertInstanceOf(InterfaceMessageBagWithFormatting::class, $messageBag->getInstance());
    }

    /** @test **/
    public function initWithNoParams_usesDefaultArray()
    {
        $this->assertEquals('Hello %s', (new MessageBagWithFormatting())->getMessage('hello'));
    }

    /** @test **/
    public function initWithParams_usesNewArray()
    {
        $this->assertEquals('Hello Abi', (new MessageBagWithFormatting([
            'hello' => 'Hello Abi'
        ]))->getMessage('hello'));
    }

    /** @test **/
    public function getEntireMessagesArray()
    {
        $this->assertEquals([], (new MessageBagWithFormatting([]))->getMessages());
    }

    /** @test **/
    public function getInstances()
    {
        $instance = new MessageBagWithFormatting();
        $this->assertEquals($instance, $instance->getInstance());
        $this->assertEquals($instance, $instance->getMessageBag());
    }

    /** @test **/
    public function getNullIfKeyNotDefined()
    {
        var_dump($val = (new MessageBagWithFormatting())->getMessage('unknown'));
        $this->assertEquals(null, $val);
    }

    /** @test **/
    public function allowSettingMessagesAfterInstanceCreation()
    {
        $messageBag = new MessageBagWithFormatting(['test'=>'hey abi']);
        $this->assertEquals('hey abi', $messageBag->getMessage('test'));

        $messageBag->setMessageBag(['test'=>'hey paul']);

        $this->assertEquals(
            'hey paul',
            $messageBag->getMessage('test')
        );
    }


    /** @test **/
    public function formattedMessage()
    {
        $this->msgBagF = new MessageBagWithFormatting([
            'wassup' => 'Wassup %s?'
        ]);

        $this->assertEquals(
            'Wassup Paul?',
            $this->msgBagF
                ->getFormattedMessage('wassup', ['Paul'])
        );
    }

    /** @test **/
    public function getFormattedMessage_whenNoFillerValuesGiven()
    {
        $this->msgBagF = new MessageBagWithFormatting([
            'wassup' => 'Wassup %s?'
        ]);

        $this->assertEquals(
            'Wassup %s?',
            $this->msgBagF->getFormattedMessage('wassup')
        );

        $this->assertEquals(
            'Wassup %s?',
            $this->msgBagF->getMessage('wassup')
        );
    }


    /** @test **/
    public function getFormattedMessage_whenFillerValuesIsNullOrEmpty()
    {
        $this->msgBagF = new MessageBagWithFormatting([
            'wassup' => 'Wassup %s?'
        ]);

        $this->msgBagF->setMessageBag([
            'wassup1' => 'Wassup %s?'
        ]);

        $this->assertEquals(
            'Wassup %s?',
            $this->msgBagF->getFormattedMessage('wassup1', [])
        );

        $this->assertEquals(
            'Wassup %s?',
            $this->msgBagF->getFormattedMessage('wassup1', null)
        );
    }

}
