<?php

namespace Text\Stream\Tests;

use Text\Stream\Mode;

class ModeTest extends \PHPUnit\Framework\TestCase
{
    function testR()
    {
        $mode = new Mode('r');

        $this->assertTrue($mode->isReadable());
        $this->assertFalse($mode->isWritable());
        $this->assertTrue($mode->isBinary());
        $this->assertFalse($mode->isText());
        $this->assertFalse($mode->isCreatable());
        $this->assertFalse($mode->isOverwritable());
        $this->assertFalse($mode->isTruncatable());
        $this->assertTrue($mode->isPointerAtTheBeginning());
        $this->assertFalse($mode->isPointerAtTheEnd());
        $this->assertEquals('rb', (string) $mode);
    }

    function testFromStreamR()
    {
        $mode = Mode::fromStream(fopen('php://memory', 'r'));

        $this->assertTrue($mode->isReadable());
        $this->assertFalse($mode->isWritable());
        $this->assertTrue($mode->isBinary());
        $this->assertFalse($mode->isText());
        $this->assertFalse($mode->isCreatable());
        $this->assertFalse($mode->isOverwritable());
        $this->assertFalse($mode->isTruncatable());
        $this->assertTrue($mode->isPointerAtTheBeginning());
        $this->assertFalse($mode->isPointerAtTheEnd());
        $this->assertEquals('rb', (string) $mode);
    }
}
