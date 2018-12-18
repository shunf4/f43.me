<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\ConstraintRss;
use AppBundle\Validator\Constraints\ConstraintRssValidator;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use PHPUnit\Framework\TestCase;

class ConstraintRssValidatorTest extends TestCase
{
    public function testValidatorValid()
    {
        $constraint = new ConstraintRss();

        $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->never())
            ->method('addViolation');

        $client = new Client();

        $mock = new Mock([
            new Response(200, [], Stream::factory('This is a valid')),
        ]);

        $client->getEmitter()->attach($mock);

        $validator = new ConstraintRssValidator($client);
        $validator->initialize($context);
        $validator->validate('http://0.0.0.0', $constraint);
    }

    public function testValidatorFail()
    {
        $constraint = new ConstraintRss();

        $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with(
                $this->equalTo($constraint->message),
                $this->equalTo(['%string%' => 'http://0.0.0.0'])
            );

        $client = new Client();

        $mock = new Mock([
            new Response(200, [], Stream::factory('This is a not valid')),
        ]);

        $client->getEmitter()->attach($mock);

        $validator = new ConstraintRssValidator($client);
        $validator->initialize($context);
        $validator->validate('http://0.0.0.0', $constraint);
    }

    public function testValidatorFailFirst()
    {
        $constraint = new ConstraintRss();

        $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with(
                $this->equalTo($constraint->message),
                $this->equalTo(['%string%' => 'http://0.0.0.0'])
            );

        $client = new Client();

        $mock = new Mock([
            new Response(400, [], Stream::factory('oops')),
            new Response(200, [], Stream::factory('This is a not valid')),
        ]);

        $client->getEmitter()->attach($mock);

        $validator = new ConstraintRssValidator($client);
        $validator->initialize($context);
        $validator->validate('http://0.0.0.0', $constraint);
    }

    public function testValidatorFailTwice()
    {
        $constraint = new ConstraintRss();

        $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->never())
            ->method('addViolation');

        $client = new Client();

        $mock = new Mock([
            new Response(400, [], Stream::factory('oops')),
            new Response(400, [], Stream::factory('oops')),
        ]);

        $client->getEmitter()->attach($mock);

        $validator = new ConstraintRssValidator($client);
        $validator->initialize($context);
        $validator->validate('http://0.0.0.0', $constraint);
    }
}