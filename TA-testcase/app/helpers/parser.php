<?php

if (!function_exists('parsePumlToJson')) {
    function parsePumlToJson($fullPath)
    {
        if (!file_exists($fullPath)) {
            return [];
        }

        $lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $result = [];
        $idCounter = 0;
        $lastRow = null;

        // Stack untuk percabangan
        $branchStack = []; // Menyimpan decision node
        $branchEnds  = []; // Menyimpan ujung tiap cabang sebelum endif

        foreach ($lines as $line) {
            $line = trim($line);

            // skip baris komentar / header
            if (strpos($line, '@startuml') !== false || strpos($line, '@enduml') !== false || strpos($line, "'") === 0) {
                continue;
            }

            $lastId = $lastRow ? $lastRow['id'] : 0;
            $currentDependency = $lastId;
            $state = '';
            $activityName = '';

            if (strtolower($line) === 'start') {
                $state = 'StartState';
                $activityName = 'start';
                $currentDependency = 0;

            } elseif (strtolower($line) === 'stop') {
                $state = 'EndState';
                $activityName = 'stop';

                // kalau ada cabang aktif → stop menutup cabang itu
                if (!empty($branchStack)) {
                    $branchEnds[] = $lastId;
                    $currentDependency = $lastId;
                }

            } elseif (preg_match('/^if\s*\((.*)\)\s*then/i', $line, $matches)) {
                // Decision
                $state = 'DecisionState';
                $activityName = trim($matches[1]);
                $currentDependency = $lastId;

                $idCounter++;
                $decisionId = $idCounter;
                $branchStack[] = $decisionId;
                $branchEnds = []; // reset ujung cabang baru

                $lastRow = [
                    'no' => count($result) + 1,
                    'state' => $state,
                    'activity_name' => $activityName,
                    'id' => $decisionId,
                    'dependency' => $currentDependency
                ];
                $result[] = $lastRow;
                continue;

            } elseif (strtolower($line) === 'else') {
                // else → simpan ujung cabang pertama
                if ($lastRow) {
                    $branchEnds[] = $lastRow['id'];
                }
                continue;

            } elseif (strtolower($line) === 'endif') {
                // endif → simpan ujung cabang terakhir
                if ($lastRow) {
                    $branchEnds[] = $lastRow['id'];
                }
                // pop decision dari stack
                array_pop($branchStack);
                continue;

            } elseif (preg_match('/^:(.*);$/', $line, $matches)) {
                $state = 'ActivityState';
                $activityName = trim($matches[1]);

                if (!empty($branchEnds)) {
                    // join dari semua cabang → dependency array
                    $currentDependency = $branchEnds;
                    $branchEnds = [];
                } elseif (!empty($branchStack)) {
                    // masih dalam cabang → depend ke decision node
                    $currentDependency = end($branchStack);
                }
            }

            if (!empty($state)) {
                $lastRow = [
                    'no' => count($result) + 1,
                    'state' => $state,
                    'activity_name' => $activityName,
                    'id' => ++$idCounter,
                    'dependency' => $currentDependency
                ];
                $result[] = $lastRow;
            }
        }

        return $result;
    }
}