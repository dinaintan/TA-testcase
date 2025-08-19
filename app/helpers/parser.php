<?php

if (!function_exists('parsePumlToJson')) {
    function parsePumlToJson($fullPath)
    {
        if (!file_exists($fullPath)) {
            return [];
        }

        $lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // 1. Fase Parsing: Membangun daftar node dan dependensi
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

            if (strtolower($line) === 'start') {
                $state = 'StartState';
                $activityName = 'start';
                $idCounter++;
                $currentDependency = 0;
                $lastId = $idCounter;

            } elseif (strtolower($line) === 'stop') {
                $state = 'EndState';
                $activityName = 'stop';
                $idCounter++;
                if (!empty($branchStack)) {
                    $currentDependency = $lastId;
                    $branchStack[count($branchStack) - 1]['ends'][] = $lastId;
                } else {
                    $currentDependency = $lastId;
                }
                $lastId = $idCounter;

            } elseif (preg_match('/^if\s*\((.*)\)\s*then/i', $line, $matches)) {
                $state = 'DecisionState';
                $activityName = trim($matches[1]);
                $idCounter++;
                
                $currentDependency = $lastId;
                $lastId = $idCounter;

                $branchStack[] = [
                    'decisionId' => $lastId,
                    'ends' => []
                ];
                $currentPathDependencies[] = $lastId;
                
            } elseif (strtolower($line) === 'else') {
                if ($lastId && !empty($branchStack)) {
                    $branchStack[count($branchStack) - 1]['ends'][] = $lastId;
                }
                $lastId = end($branchStack)['decisionId'];
                continue;

            } elseif (strtolower($line) === 'endif') {
                if ($lastId && !empty($branchStack)) {
                    $branchStack[count($branchStack) - 1]['ends'][] = $lastId;
                }
                $ended = array_pop($branchStack);
                $currentDependency = $ended['ends'];
                $lastId = null;
                
                // Hapus dependensi percabangan yang sudah selesai
                array_pop($currentPathDependencies);

            } elseif (preg_match('/^:(.*);$/', $line, $matches)) {
                $state = 'ActivityState';
                $activityName = trim($matches[1]);
                $idCounter++;
                
                if (!empty($branchStack)) {
                    $currentDependency = end($branchStack)['decisionId'];
                } else {
                    $currentDependency = $lastId;
                }
                $lastId = $idCounter;
            }

            if (!empty($state)) {
                $node = [
                    'id' => $idCounter,
                    'activity_name' => $activityName,
                    'state' => $state,
                    'dependency' => $currentDependency
                ];
                $nodes[] = $node;
            }
        }
        
        // 2. Fase Pembuatan Graf: Mengubah daftar node menjadi struktur graf
        $graph = [];
        $nodeMap = [];
        foreach ($nodes as $node) {
            $nodeMap[$node['id']] = $node;
            $dependency = $node['dependency'];
            
            if (is_array($dependency)) {
                foreach ($dependency as $depId) {
                    if (!isset($graph[$depId])) $graph[$depId] = [];
                    $graph[$depId][] = $node['id'];
                }
            } elseif ($dependency !== null) {
                if (!isset($graph[$dependency])) $graph[$dependency] = [];
                $graph[$dependency][] = $node['id'];
            }
        }

        // 3. Fase Penelusuran Jalur: Mencari semua jalur lengkap
        $allPaths = [];
        $rootNodes = array_filter($nodes, function($n) {
            return $n['dependency'] === 0;
        });

        foreach ($rootNodes as $root) {
            findPathsRecursive($root['id'], $graph, [], $allPaths);
        }
        
        // 4. Fase Output: Memformat jalur menjadi data yang mudah dibaca
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
        } else {
            foreach ($graph[$nodeId] as $nextNodeId) {
                findPathsRecursive($nextNodeId, $graph, $currentPath, $allPaths);
            }
        }
    }
}