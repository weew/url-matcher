<?php

namespace Weew\UrlMatcher;

interface IMatchPattern {
    /**
     * @return string
     */
    function getName();

    /**
     * @return string
     */
    function getPattern();

    /**
     * @return string
     */
    function getRegexName();

    /**
     * @return string
     */
    function getRegexPattern();
}
