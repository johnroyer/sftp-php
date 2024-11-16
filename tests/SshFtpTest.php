<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Zeroplex\Sftp\Sftp;

class SshFtpTest extends TestCase
{
    protected $sftp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sftp = new Sftp('127.0.0.1', 22, 10);
    }

    protected function tearDown(): void
    {
        $this->sftp = null;
        parent::tearDown();
    }

    public function testHostSetter()
    {
        $this->sftp = new Sftp(
            '127.0.0.1',
            1234,
            60
        );

        $propRef = new \ReflectionProperty($this->sftp, 'host');
        $propRef->setAccessible(true);

        $this->assertEquals(
            '127.0.0.1',
            $propRef->getValue($this->sftp)
        );
    }

    public static function invalidPortNumberProvider()
    {
        return [
            [-1],
            [0],
            [65536],
            [99999],
            ['test'],
        ];
    }

    /**
     * @dataProvider invalidPortNumberProvider
     */
    public function testInvalidPortNumber($portNumber)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sftp = new  Sftp('127.0.0.1', $portNumber);
    }

    public static function validPortProvider()
    {
        return [
            [1],
            [1023],
            [1024],
            [65535],
        ];
    }

    /**
     * @dataProvider validPortProvider
     */
    public function testPortSetterWithValidData($port)
    {
        $this->sftp = new Sftp(
            '127.0.0.1',
            $port,
            60
        );

        $propRef = new \ReflectionProperty($this->sftp, 'port');
        $propRef->setAccessible(true);

        $this->assertEquals(
            $port,
            $propRef->getValue($this->sftp)
        );
    }

    public static function invalidPortProvider()
    {
        return [
            [0],
            [-1],
            [65536],
        ];
    }

    /**
     * @dataProvider invalidPortProvider
     */
    public function testPortSetterWithWrongData($invalidPort)
    {
        $this->expectException(\Exception::class);

        $this->sftp = new Sftp(
            '127.0.0.1',
            $invalidPort,
            60
        );
    }

    public static function timeoutProvider()
    {
        return [
            [1, true],
            [10, true],
            [-1, true],
            [-10, true],

            ['test', false],
            [[], false],
        ];
    }

    /**
     * @dataProvider timeoutProvider
     */
    public function testTimeoutArgument($input, $expected)
    {
        if (true !== $expected) {
            $this->expectException(\Exception::class);
        }

        $this->assertTrue(
            is_object(new Sftp('127.0.0.1', 22, $input))
        );
    }

    public function testHostGetter()
    {
        $this->sftp = new Sftp('127.0.0.1', 22, 10);

        $this->assertEquals(
            '127.0.0.1',
            $this->sftp->getHost()
        );
    }

    public function testPortGetter()
    {
        $this->sftp = new Sftp('127.0.0.1', 22, 10);

        $this->assertEquals(
            22,
            $this->sftp->getPort()
        );
    }

    public function testTimeoutGetter()
    {
        $this->sftp = new Sftp('127.0.0.1', 22, 10);

        $this->assertEquals(
            10,
            $this->sftp->getTimeout()
        );
    }

    public function testloginChecker()
    {
        $propRef = new \ReflectionProperty($this->sftp, 'sftpResource');

        $propRef->setAccessible(true);
        $propRef->setValue($this->sftp, null);

        $this->assertSame(
            false,
            $this->sftp->isLoggedIn(),
        );
    }
}
