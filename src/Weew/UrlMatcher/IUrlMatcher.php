<?php

namespace Weew\UrlMatcher;

use Weew\Collections\IDictionary;

interface IUrlMatcher {
    /**
     * @param $pattern
     * @param $path
     * @param array $patterns
     *
     * @return bool
     */
    function match($pattern, $path, array $patterns = []);

    /**
     * @param $pattern
     * @param $path
     * @param array $patterns
     *
     * @return IDictionary
     */
    function parse($pattern, $path, array $patterns = []);

    /**
     * @param string $path
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    function replace($path, $key, $value);

    /**
     * @param string $path
     * @param array $replacements
     *
     * @return string
     */
    function replaceAll($path, array $replacements);

    /**
     * @return IMatchPattern[]
     */
    function getPatterns();

    /**
     * @param array $patterns
     */
    function setPatterns(array $patterns);

    /**
     * @param $name
     * @param $pattern
     */
    function addPattern($name, $pattern);
}
