<?php

namespace Tests\Weew\UrlMatcher;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Weew\UrlMatcher\MatchPattern;

class MatchPatternTest extends PHPUnit_Framework_TestCase {
    public function test_getters() {
        $pattern = new MatchPattern('foo', '[0-9]+');
        $this->assertEquals('foo', $pattern->getName());
        $this->assertEquals('[0-9]+', $pattern->getPattern());
    }

    public function test_create_with_invalid_name() {
        $this->setExpectedException(InvalidArgumentException::class);
        new MatchPattern(1, 'foo');
    }

    public function test_create_with_invalid_pattern() {
        $this->setExpectedException(InvalidArgumentException::class);
        new MatchPattern('foo', 1);
    }
}
