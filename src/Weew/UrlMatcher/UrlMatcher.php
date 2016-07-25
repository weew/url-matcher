<?php

namespace Weew\UrlMatcher;

use Weew\Collections\Dictionary;
use Weew\Collections\IDictionary;

class UrlMatcher implements IUrlMatcher {
    /**
     * @var IMatchPattern[]
     */
    protected $patterns = [];

    /**
     * UrlMatcher constructor.
     */
    public function __construct() {
        $this->addDefaultPatterns();
    }

    /**
     * @param $pattern
     * @param $path
     * @param array $patterns
     *
     * @return bool
     */
    public function match($pattern, $path, array $patterns = []) {
        $patterns = $this->mergeWithLocalPatterns($patterns);

        $path = $this->addTrailingSlash($path);
        $pattern = $this->createRegexPattern($pattern, $patterns);
        $matches = [];

        if (preg_match_all($pattern, $path, $matches) === 1) {
            $matchedPath = $this->addTrailingSlash(array_get($matches, '0.0'));

            return $matchedPath == $path;
        };

        return false;
    }

    /**
     * @param $pattern
     * @param $path
     * @param array $patterns
     *
     * @return IDictionary
     */
    public function parse($pattern, $path, array $patterns = []) {
        $patterns = $this->mergeWithLocalPatterns($patterns);
        $names = $this->extractParameterNames($pattern);
        $values = $this->extractParameterValues($pattern, $path, $patterns);
        $parameters = array_combine($names, array_pad($values, count($names), null));

        return new Dictionary($parameters);
    }

    /**
     * @param string $path
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    public function replace($path, $key, $value) {
        $path = str_replace(s('{%s}', $key), $value, $path);

        return $path;
    }

    /**
     * @param string $path
     * @param array $replacements
     *
     * @return string
     */
    public function replaceAll($path, array $replacements) {
        foreach ($replacements as $key => $value) {
            $path = $this->replace($path, $key, $value);
        }

        return $path;
    }

    /**
     * @return IMatchPattern[]
     */
    public function getPatterns() {
        return $this->patterns;
    }

    /**
     * @param IMatchPattern[] $patterns
     */
    public function setPatterns(array $patterns) {
        $this->patterns = $patterns;
    }

    /**
     * @param $name
     * @param $pattern
     */
    public function addPattern($name, $pattern) {
        array_unshift($this->patterns, $this->createPattern($name, $pattern));
        array_unshift($this->patterns, $this->createPattern($name, $pattern, true));
    }

    /**
     * @param $path
     * @param IMatchPattern[] $patterns
     *
     * @return string
     */
    protected function createRegexPattern($path, array $patterns = []) {
        $pattern = $this->applyCustomRegexPatterns($path, $patterns);
        $pattern = $this->applyStandardRegexPatterns($pattern);
        $pattern = s('#%s#', $pattern);

        return $pattern;
    }

    /**
     * @param $path
     * @param IMatchPattern[] $patterns
     *
     * @return string
     */
    protected function applyCustomRegexPatterns($path, array $patterns) {
        foreach ($patterns as $pattern) {
            $path = preg_replace([$pattern->getRegexName()], $pattern->getRegexPattern(), $path);
        }

        return $path;
    }

    /**
     * @param $path
     *
     * @return string
     */
    protected function applyStandardRegexPatterns($path) {
        $pattern = preg_replace('#\{([a-zA-Z0-9_-]+)\?\}#', '([^/]+)?', $path);
        $pattern = preg_replace('#\{([a-zA-Z0-9_-]+)\}#', '([^/]+)', $pattern);

        return $pattern;
    }

    /**
     * @param $path
     *
     * @return array
     */
    protected function extractParameterNames($path) {
        $names = [];
        $matches = [];
        preg_match_all('#\{([a-zA-Z0-9?]+)\}#', $path, $matches);

        foreach (array_get($matches, 1, []) as $name) {
            $names[] = str_replace('?', '', $name);
        }

        return $names;
    }

    /**
     * @param $pattern
     * @param $path
     * @param IMatchPattern[] $patterns
     *
     * @return array
     */
    protected function extractParameterValues($pattern, $path, array $patterns = []) {
        $path = $this->addTrailingSlash($path);
        $matches = [];

        $pattern = $this->createRegexPattern($pattern, $patterns);
        preg_match_all($pattern, $path, $matches);
        array_shift($matches);

        $values = $this->processParameterValues($matches);

        return $values;
    }

    /**
     * @param array $matches
     *
     * @return array
     */
    protected function processParameterValues(array $matches) {
        $values = [];

        foreach ($matches as $group) {
            if (is_array($group)) {
                foreach ($group as $value) {
                    if ($value == '') {
                        $value = null;
                    } else {
                        $value = $this->removeTrailingSlash($value);
                    }

                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * @param $string
     *
     * @return string
     */
    protected function addTrailingSlash($string) {
        if ( ! str_ends_with($string, '/')) {
            $string .= '/';
        }

        return $string;
    }

    /**
     * @param $string
     *
     * @return string
     */
    protected function removeTrailingSlash($string) {
        if (str_ends_with($string, '/')) {
            $string = substr($string, 0, -1);
        }

        return $string;
    }

    /**
     * @param array $patterns
     *
     * @return IMatchPattern[]
     */
    protected function mergeWithLocalPatterns(array $patterns) {
        $mergedPatterns = $this->patterns;

        foreach ($patterns as $name => $pattern) {
            array_unshift($mergedPatterns, $this->createPattern($name, $pattern));
            array_unshift($mergedPatterns, $this->createPattern($name, $pattern, true));
        }

        return $mergedPatterns;
    }

    /**
     * @param $name
     * @param $pattern
     * @param bool $optional
     *
     * @return IMatchPattern
     */
    protected function createPattern($name, $pattern, $optional = false) {
        return new MatchPattern($name, $pattern, $optional);
    }

    /**
     * Register default patterns.
     */
    protected function addDefaultPatterns() {
        $this->addPattern('any', '.+');
    }
}
