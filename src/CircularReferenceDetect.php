<?php

/*
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antalaron\Component\CircularReferenceDetect;

/**
 * CircularDependencyDetect.
 *
 * @author Antal Áron <antalaron@antalaron.hu>
 */
class CircularReferenceDetect
{
    const MAX_DEPTH = 50;

    protected $maxDepth;

    public function __construct($maxDepth = self::MAX_DEPTH)
    {
        $this->maxDepth = $maxDepth;
    }

    public static function newInstance($maxDepth = self::MAX_DEPTH)
    {
        return new self($maxDepth);
    }

    public function setMaxDepth($maxDepth = self::MAX_DEPTH)
    {
        $this->maxDepth = $maxDepth;
    }

    public function hasCircularReference(array $nodes, array $graph = null)
    {
        if (null === $graph) {
            $graph = $nodes;
            $nodes = array_keys($nodes);
        }

        $found = $this->checkCircular($nodes, $graph);
        if (is_array($found)) {
            $last = $found[count($found) - 1];
            $key = array_search($last, $found, true);

            return array_slice($found, $key);
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

            if ($this->maxDepth <= count($path)) {
                continue;
            }

            $found = $this->checkCircular($graph[$dependency], $graph, $currentPath);
            if (is_array($found)) {
                return $found;
            }
        }

        return false;
    }
}
