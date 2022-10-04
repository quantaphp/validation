<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Error;
use Quanta\Validation\ErrorFormatter;
use Quanta\Validation\ErrorFormatterInterface;

final class ErrorFormatterTest extends TestCase
{
    private $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ErrorFormatter;
    }

    public function testImplementsErrorFormatterInterface(): void
    {
        $this->assertInstanceOf(ErrorFormatterInterface::class, $this->formatter);
    }

    public function testFormatsDefaultWithParameters(): void
    {
        $params = ['p1' => 'a', 'p2' => 'b', 'p3' => 1];

        $test1 = ($this->formatter)(Error::from('default'));
        $test2 = ($this->formatter)(Error::from('> %s:%s:%s <', $params));

        $this->assertEquals($test1, 'default');
        $this->assertEquals($test2, '> a:b:1 <');
    }

    public function testReplacesKeyPlaceholderAndPrependsPath(): void
    {
        $params = ['p1' => 'a', 'p2' => 'b', 'p3' => 1];

        $test1 = ($this->formatter)(Error::from('> {key} %s:%s:%s <', $params));
        $test2 = ($this->formatter)(Error::from('> {key} %s:%s:%s <', $params,)->nested('key1'));
        $test3 = ($this->formatter)(Error::from('> {key} %s:%s:%s <', $params,)->nested('key1', 'key2', 'key3'));
        $test4 = ($this->formatter)(Error::from('> %s:%s:%s <', $params,)->nested('key1', 'key2', 'key3'));

        $this->assertEquals($test1, '> value a:b:1 <');
        $this->assertEquals($test2, '> [key1] a:b:1 <');
        $this->assertEquals($test3, '[key1][key2] > [key3] a:b:1 <');
        $this->assertEquals($test4, '[key1][key2][key3] > a:b:1 <');
    }
}
