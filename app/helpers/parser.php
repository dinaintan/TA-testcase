<?php

if (!function_exists('parsePumlToJson')) {
    function parsePumlToJson($fullPath)
    {
        if (!file_exists($fullPath)) {
            return [];
        }

        $lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $nodes = [];
        $idCounter = 0;
        $lastId = null;
        $branchStack = [];
        $currentPathDependencies = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (strpos($line, '@startuml') !== false || strpos($line, '@enduml') !== false || strpos($line, "'") === 0) {
                continue;
            }

            $state = '';
            $activityName = '';
            $currentDependency = null;
            $nodeCreated = false;

            if (strtolower($line) === 'start') {
                $state = 'StartState';
                $activityName = 'start';
                $idCounter++;
                $currentDependency = 0;
                $lastId = $idCounter;
                $nodeCreated = true;
            }

            if (strtolower($line) === 'stop') {
                $state = 'EndState';
                $activityName = 'stop';
                $idCounter++;
                $currentDependency = empty($branchStack) ? $lastId : $lastId;
                if (!empty($branchStack)) {
                    $branchStack[count($branchStack) - 1]['ends'][] = $lastId;
                }
                $lastId = $idCounter;
                $nodeCreated = true;
            }

            if (preg_match('/^if\s*\((.*)\)\s*then/i', $line, $matches)) {
                $state = 'DecisionState';
                $activityName = trim($matches[1]);
                $idCounter++;
                $currentDependency = $lastId;
                $lastId = $idCounter;
                $branchStack[] = ['decisionId' => $lastId, 'ends' => []];
                $currentPathDependencies[] = $lastId;
                $nodeCreated = true;
            }

            if (strtolower($line) === 'else') {
                if ($lastId && !empty($branchStack)) {
                    $branchStack[count($branchStack) - 1]['ends'][] = $lastId;
                }
                $lastId = end($branchStack)['decisionId'];
                continue;
            }

            if (strtolower($line) === 'endif') {
                if ($lastId && !empty($branchStack)) {
                    $branchStack[count($branchStack) - 1]['ends'][] = $lastId;
                }
                $ended = array_pop($branchStack);
                $currentDependency = $ended['ends'];
                $lastId = null;
                array_pop($currentPathDependencies);
                continue;
            }

            if (preg_match('/^:(.*);$/', $line, $matches)) {
                $state = 'ActivityState';
                $activityName = trim($matches[1]);
                $idCounter++;
                $currentDependency = empty($branchStack) ? $lastId : end($branchStack)['decisionId'];
                $lastId = $idCounter;
                $nodeCreated = true;
            }

            if ($nodeCreated) {
                $node = [
                    'id' => $idCounter,
                    'activity_name' => $activityName,
                    'state' => $state,
                    'dependency' => $currentDependency
                ];
                $nodes[] = $node;
            }
        }
        
        // Fase 2, 3, 4 tetap sama
        $graph = [];
        $nodeMap = [];
        foreach ($nodes as $node) {
            $nodeMap[$node['id']] = $node;
            $dependency = $node['dependency'];
            
            if (is_array($dependency)) {
                foreach ($dependency as $depId) {
                    $graph[$depId] = $graph[$depId] ?? [];
                    $graph[$depId][] = $node['id'];
                }
            } elseif ($dependency !== null) {
                $graph[$dependency] = $graph[$dependency] ?? [];
                $graph[$dependency][] = $node['id'];
            }
        }

        $allPaths = [];
        $rootNodes = array_filter($nodes, function($n) {
            return $n['dependency'] === 0;
        });

        foreach ($rootNodes as $root) {
            findPathsRecursive($root['id'], $graph, [], $allPaths);
        }
        
        $formattedPaths = [];
        foreach ($allPaths as $path) {
            $formattedPath = [];
            foreach ($path as $nodeId) {
                $formattedPath[] = $nodeMap[$nodeId];
            }
            $formattedPaths[] = $formattedPath;
        }
        
        return $formattedPaths;
    }

    function findPathsRecursive($nodeId, $graph, $currentPath, &$allPaths) {
        $currentPath[] = $nodeId;

        if (!isset($graph[$nodeId])) {
            $allPaths[] = $currentPath;
            return; // Menggunakan early return untuk mengakhiri fungsi
        }
        
        foreach ($graph[$nodeId] as $nextNodeId) {
            findPathsRecursive($nextNodeId, $graph, $currentPath, $allPaths);
        }
    }
}