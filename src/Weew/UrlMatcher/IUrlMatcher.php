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
