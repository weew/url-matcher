<?php

namespace Tests\Weew\UrlMatcher;

use PHPUnit_Framework_TestCase;
use Weew\UrlMatcher\MatchPattern;
use Weew\UrlMatcher\UrlMatcher;

class UrlMatcherTest extends PHPUnit_Framework_TestCase {
    public function test_match() {
        $matcher = new UrlMatcher();
        $this->assertTrue($matcher->match('foo/bar', 'foo/bar'));
        $this->assertTrue($matcher->match('foo/{name}', 'foo/bar'));
        $this->assertFalse($matcher->match('foo/{name}', 'foo'));
        $this->assertTrue($matcher->match('foo/{name?}', 'foo'));
        $this->assertTrue($matcher->match('foo/{name?}', 'foo/bar'));
        $this->assertFalse($matcher->match('foo/{name?}', ''));

        $this->assertTrue($matcher->match('foo/{name}/{nickname}', 'foo/bar/baz'));
        $this->assertTrue($matcher->match('foo/{name}/{nickname?}', 'foo/bar'));
        $this->assertTrue($matcher->match('foo/{name}/yolo/{nickname?}', 'foo/bar/yolo'));
        $this->assertTrue($matcher->match('foo/{name}/yolo/{nickname}', 'foo/bar/yolo/baz'));
        $this->assertFalse($matcher->match('foo/{name}/yolo/{nickname}', 'foo/bar/baz'));
    }

    public function test_match_with_default_patterns() {
        $matcher = new UrlMatcher();
        $this->assertTrue($matcher->match('foo/{any}', 'foo/bar'));
        $this->assertTrue($matcher->match('foo/{any}', 'foo/bar/baz'));
        $this->assertFalse($matcher->match('foo/{any}', 'foo'));

        $this->assertTrue($matcher->match('foo/{any?}', 'foo/bar'));
        $this->assertTrue($matcher->match('foo/{any?}', 'foo/bar/bar'));
        $this->assertTrue($matcher->match('foo/{any?}', 'foo'));
    }

    public function test_match_with_preset_patterns() {
        $matcher = new UrlMatcher();
        $matcher->addPattern('id', '[0-9]+');
        $this->assertTrue($matcher->match('foo/{id}', 'foo/123'));
        $this->assertFalse($matcher->match('foo/{id}', 'foo/a23'));
        $this->assertTrue($matcher->match('foo/{id?}', 'foo/123'));
        $this->assertTrue($matcher->match('foo/{id?}', 'foo'));
        $this->assertFalse($matcher->match('foo/{id?}', 'foo/a23'));
        $this->assertTrue($matcher->match('foo/{id?}/{any?}', 'foo/123/yolo'));
    }

    public function test_match_with_inline_presets() {
        $matcher = new UrlMatcher();
        $this->assertTrue($matcher->match('foo/{id}', 'foo/123', ['id' => '[0-9]+']));
        $this->assertFalse($matcher->match('foo/{id}', 'foo/a23', ['id' => '[0-9]+']));
    }

    public function test_parse() {
        $matcher = new UrlMatcher();

        $dict = $matcher->parse('foo/{id}', 'foo/bar');
        $this->assertEquals('bar', $dict->get('id'));

        $dict = $matcher->parse('foo/{id}/{name}', 'foo/bar/yolo');
        $this->assertEquals('bar', $dict->get('id'));
        $this->assertEquals('yolo', $dict->get('name'));

        $dict = $matcher->parse('foo/{id}/{name?}', 'foo/bar/yolo');
        $this->assertEquals('bar', $dict->get('id'));
        $this->assertEquals('yolo', $dict->get('name'));

        $dict = $matcher->parse('foo/{id}/{name?}', 'foo/bar');
        $this->assertEquals('bar', $dict->get('id'));
        $this->assertNull($dict->get('name'));
    }

    public function test_parse_with_preset_patterns() {
        $matcher = new UrlMatcher();

        $dict = $matcher->parse('foo/{any}', 'foo/bar/baz');
        $this->assertEquals('bar/baz', $dict->get('any'));

        $dict = $matcher->parse('foo/{any}', 'foo');
        $this->assertNull($dict->get('any'));

        $dict = $matcher->parse('foo/{any?}', 'foo/bar');
        $this->assertEquals('bar', $dict->get('any'));

        $dict = $matcher->parse('foo/{any?}', 'foo');
        $this->assertNull($dict->get('any'));
    }

    public function test_parse_with_inline_patterns() {
        $matcher = new UrlMatcher();

        $dict = $matcher->parse('foo/{id}', 'foo/123', ['id' => '[0-9]+']);
        $this->assertEquals(123, $dict->get('id'));

        $dict = $matcher->parse('foo/{id}', 'foo/a23', ['id' => '[0-9]+']);
        $this->assertNull($dict->get('id'));

        $dict = $matcher->parse('foo/{id?}', 'foo/123', ['id' => '[0-9]+']);
        $this->assertEquals(123, $dict->get('id'));

        $dict = $matcher->parse('foo/{id?}', 'foo/a23', ['id' => '[0-9]+']);
        $this->assertNull($dict->get('id'));

        $dict = $matcher->parse('foo/{id?}/{name}', 'foo/123/yolo', ['id' => '[0-9]+']);
        $this->assertEquals(123, $dict->get('id'));
        $this->assertEquals('yolo', $dict->get('name'));

        $dict = $matcher->parse('foo/{id?}/{name}', 'foo/a23/yolo', ['id' => '[0-9]+']);
        $this->assertNull($dict->get('id'));
        $this->assertNull($dict->get('yolo'));
    }

    public function test_get_and_set_patterns() {
        $matcher = new UrlMatcher();
        $patterns = [new MatchPattern('foo', 'bar')];
        $matcher->setPatterns($patterns);
        $this->assertTrue($matcher->getPatterns() === $patterns);
    }

    public function test_replace() {
        $matcher = new UrlMatcher();
        $this->assertEquals(
            '/baz/bar/yolo/baz',
            $matcher->replace('/{foo}/bar/yolo/{foo}', 'foo', 'baz')
        );
    }

    public function test_replace_in_host() {
        $matcher = new UrlMatcher();
        $this->assertEquals(
            'baz.yolo.swag/bar/yolo/baz',
            $matcher->replace('{foo}.yolo.swag/bar/yolo/{foo}', 'foo', 'baz')
        );
    }
}
