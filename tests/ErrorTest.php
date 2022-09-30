<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Error;

final class ErrorTest extends TestCase
{
    public function dataProvider(): array
    {
        $params = ['p1' => 'a', 'p2' => 'b', 'p3' => 'c'];
        $labels = ['l1', 'l2', 'l3'];

        return [
            [[], [], []],
            [$params, [], []],
            [[], $labels, []],
            [$params, $labels, []],
        ];
    }

    public function dataProviderWithKeys(): array
    {
        $params = ['p1' => 'a', 'p2' => 'b', 'p3' => 'c'];
        $labels = ['l1', 'l2', 'l3'];
        $keys = ['key1', 'key2', 'key3'];

        return [
            [[], [], $keys],
            [[], $labels, $keys],
            [$params, [], $keys],
            [$params, $labels, $keys],
        ];
    }

    public function testFromReturnsAnInstancewithDefaultMessageOnly(): void
    {
        $test = Error::from('default');

        $this->assertInstanceOf(Error::class, $test);
        $this->assertEquals($test->params(), []);
        $this->assertEquals($test->labels(), []);
        $this->assertEquals($test->keys(), []);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFromReturnsAnInstanceWithGivenParamsAndLabels(array $params, array $labels): void
    {
        $test = Error::from('default', $params, ...$labels);

        $this->assertInstanceOf(Error::class, $test);
        $this->assertEquals($test->params(), $params);
        $this->assertEquals($test->labels(), $labels);
        $this->assertEquals($test->keys(), []);
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataProviderWithKeys
     */
    public function testDefaultReturnsDefault(array $params, array $labels, array $keys): void
    {
        $test = Error::from('default', $params, ...$labels)->nested(...$keys);

        $this->assertEquals($test->default(), 'default');
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataProviderWithKeys
     */
    public function testParamsReturnsParams(array $params, array $labels, array $keys): void
    {
        $test = Error::from('default', $params, ...$labels)->nested(...$keys);

        $this->assertEquals($test->params(), $params);
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataProviderWithKeys
     */
    public function testLabelsReturnsLabels(array $params, array $labels, array $keys): void
    {
        $test = Error::from('default', $params, ...$labels)->nested(...$keys);

        $this->assertEquals($test->labels(), $labels);
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataProviderWithKeys
     */
    public function testKeysReturnsKeys(array $params, array $labels, array $keys): void
    {
        $test = Error::from('default', $params, ...$labels)->nested(...$keys);

        $this->assertEquals($test->keys(), $keys);
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataProviderWithKeys
     */
    public function testLabeledReturnsANewInstanceWithLabelsAppended(array $params, array $labels, array $keys): void
    {
        $test1 = Error::from('default', $params, ...$labels)->nested(...$keys);
        $test2 = $test1->labeled();
        $test3 = $test2->labeled('newl1', 'newl2', 'newl3');

        $this->assertSame($test1, $test2);
        $this->assertNotSame($test2, $test3);
        $this->assertEquals($test3->labels(), [...$labels, 'newl1', 'newl2', 'newl3']);
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataProviderWithKeys
     */
    public function testLabelledReturnsANewInstanceWithLabelsAppended(array $params, array $labels, array $keys): void
    {
        $test1 = Error::from('default', $params, ...$labels)->nested(...$keys);
        $test2 = $test1->labelled();
        $test3 = $test2->labelled('newl1', 'newl2', 'newl3');

        $this->assertSame($test1, $test2);
        $this->assertNotSame($test2, $test3);
        $this->assertEquals($test3->labels(), [...$labels, 'newl1', 'newl2', 'newl3']);
    }

    /**
     * @dataProvider dataProvider
     * @dataProvider dataProviderWithKeys
     */
    public function testNestedReturnsANewInstanceWithKeysPrepended(array $params, array $labels, array $keys): void
    {
        $test1 = Error::from('default', $params, ...$labels)->nested(...$keys);
        $test2 = $test1->nested();
        $test3 = $test2->nested('newkey1', 'newkey2', 'newkey3');

        $this->assertSame($test1, $test2);
        $this->assertNotSame($test2, $test3);
        $this->assertEquals($test3->keys(), ['newkey1', 'newkey2', 'newkey3', ...$keys]);
    }
}
