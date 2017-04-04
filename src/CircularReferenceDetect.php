<?php

/*
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antalaron\Component\CircularReferenceDetect;

use Antalaron\Component\CircularReferenceDetect\Exception\MaximumDepthReachedException;

/**
 * CircularDependencyDetect.
 *
 * @author Antal Áron <antalaron@antalaron.hu>
 */
class CircularReferenceDetect
{
    const MAX_DEPTH = 50;

    /**
     * @var int
     */
    protected $maxDepth;

    /**
     * @var bool
     */
    protected $throwExceptionOnReachMaxDepth;

    /**
     * @var bool
     */
    protected $maxDepthReached = false;

    /**
     * Constructor.
     *
     * @param int $maxDepth
     */
    public function __construct($maxDepth = self::MAX_DEPTH, $throwExceptionOnReachMaxDepth = false)
    {
        $this->maxDepth = $maxDepth;
        $this->throwExceptionOnReachMaxDepth = (bool) $throwExceptionOnReachMaxDepth;
    }

    /**
     * Singleton.
     *
     * @param int $maxDepth
     */
    public static function newInstance($maxDepth = self::MAX_DEPTH, $throwExceptionOnReachMaxDepth = false)
    {
        return new static($maxDepth, $throwExceptionOnReachMaxDepth);
    }

    /**
     * Set max depth.
     *
     * @param int $maxDepth Reset, if empty
     */
    public function setMaxDepth($maxDepth = self::MAX_DEPTH)
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * Set throw exception on reach max depth.
     *
     * @param bool $throwExceptionOnReachMaxDepth Reset, if empty
     */
    public function setThrowExceptionOnReachMaxDepth($throwExceptionOnReachMaxDepth = false)
    {
        $this->throwExceptionOnReachMaxDepth = (bool) $throwExceptionOnReachMaxDepth;
    }

    /**
     * Has circular reference.
     *
     * The nodes could be the following, in this case, the checked starting
     * nodes will be the keys of the array. The correct form is
     * `'node' => ['dependecy1', 'dependency2']`. Eg.:
     *
     * ```php
     * $nodes = [
     *     'a' => ['b'],
     *     'b' => ['c'],
     *     'c' => ['a'],
     * ];
     * ```
     *
     * If you want to check only the portion of the graph, then the graph
     * has to be the second argument, and to the first is the nodes to check.
     *
     * ```php
     * $nodes = [
     *     'a',
     *     'b',
     *     'c',
     * ];
     * $graph = [
     *     'a' => ['b'],
     *     'b' => ['c'],
     *     'c' => ['a'],
     *     'd' => ['a'],
     * ];
     * ```
     *
     * @param array      $nodes The array to detect
     * @param array|null $graph The full graph, if not equals to $nodes
     *
     * @throws MaximumDepthReachedException If flag set and maximum depth reached
     *
     * @return false|array The first curcular reference found, false if not
     */
    public function hasCircularReference(array $nodes, array $graph = null)
    {
        if (null === $graph) {
            $graph = $nodes;
            $nodes = array_keys($nodes);
        }

        // Reset
        $this->maxDepthReached = false;
        $found = $this->checkCircular($nodes, $graph);
        if (is_array($found)) {
            $last = $found[count($found) - 1];
            $key = array_search($last, $found, true);

            return array_slice($found, $key);
        }

        // Throw exception only if not found and reached, otherwise we may found
        // other circle.
        if ($this->maxDepthReached && $this->throwExceptionOnReachMaxDepth) {
            throw new MaximumDepthReachedException('Maximum depth reached.');
        }

        return $found;
    }

    protected function checkCircular(array $nodes = [], array $graph = [], array $path = [])
    {
        foreach ($nodes as $dependency) {
            $currentPath = $path;
            $currentPath[] = $dependency;

            if (in_array($dependency, $path, true)) {
                return $currentPath;
            }

            if (!array_key_exists($dependency, $graph)) {
                continue;
            }

            if ($this->maxDepth <= count($path) + 1) {
                $this->maxDepthReached = true;
                continue;
            }

            $found = $this->checkCircular($graph[$dependency], $graph, $currentPath);
            // Pass it up!
            if (is_array($found)) {
                return $found;
            }
        }

        return false;
    }
}
