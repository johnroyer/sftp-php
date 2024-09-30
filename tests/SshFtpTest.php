<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Zeroplex\Sftp\Sftp;

class SshFtpTest extends TestCase
{
    protected $sftp;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    protected function tearDown(): void
    {
        $this->sftp = null;
        parent::tearDown(); // TODO: Change the autogenerated stub
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
}
