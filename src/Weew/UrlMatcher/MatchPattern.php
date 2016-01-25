<?php

namespace Weew\UrlMatcher;

use InvalidArgumentException;

class MatchPattern implements IMatchPattern {
    /**
     * @var sting
     */
    protected $name;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $regexName;

    /**
     * @var string
     */
    protected $regexPattern;

    /**
     * MatchPattern constructor.
     *
     * @param $name
     * @param $pattern
     * @param bool $optional
     */
    public function __construct($name, $pattern, $optional = false) {
        if ( ! is_string($name)) {
            throw new InvalidArgumentException('Name name must be a string.');
        }

        if ( ! is_string($pattern)) {
            throw new InvalidArgumentException('Pattern must be a string.');
        }

        $this->name = $name;
        $this->pattern = $pattern;
        $this->regexName = $this->createRegexName($name, $optional);
        $this->regexPattern = $this->createRegexPattern($pattern, $optional);
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getRegexName() {
        return $this->regexName;
    }

    /**
     * @return string
     */
    public function getRegexPattern() {
        return $this->regexPattern;
    }

    /**
     * @param $name
     * @param $optional
     *
     * @return string
     */
    protected function createRegexName($name, $optional) {
        return $optional
            ? '#\{' . preg_quote($name) . '\?\}#'
            : '#\{' . preg_quote($name) . '\}#';
    }

    /**
     * @param $pattern
     * @param $optional
     *
     * @return string
     */
    protected function createRegexPattern($pattern, $optional) {
        return $optional
            ? '(' . $pattern . ')?'
            : '(' . $pattern . ')';
    }
}
