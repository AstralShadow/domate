<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MathExam;

use \MongoDB\BSON\ObjectId as ObjectId;
use \MongoDB\BSON\UTCDateTime as UTCDateTime;
use \MongoDB\Database as Database;
use MathExam\Exercise as Exercise;
use MathExam\ExerciseVariant as ExerciseVariant;
use MathExam\ExerciseGroup as ExerciseGroup;
use MathExam\Test as Test;
use MathExam\TestSolution as TestSolution;

class TestVariantGenerator
{

    public static function generateTestVariant(Database $database, TestSolution $solution, Test $test) {
        $groupUsages = [];
        $groups = [];
        foreach ((array) $test->contents as $pair){
            $g_oid = (string) $pair["id"];
            if (!isset($groupUsages[$g_oid])){
                $groupUsages[$g_oid] = 0;
                $groups[$g_oid] = new ExerciseGroup($database, new ObjectId($g_oid));
            }
            $groupUsages[$g_oid] += max(0, (int) $pair["repeat"]);
        }

        $exercisesByGroup = [];
        $exercisesObjects = [];
        foreach ($groupUsages as $g_oid => $count){
            $exercises = [];
            $exercisesByGroup[$g_oid] = [];
            for ($i = 0; $i < $count; $i++){
                if (!count($exercises)){
                    $exercises = (array) $groups[$g_oid]->getContents();
                    if (!count($exercises)){
                        break;
                    }
                }
                $get = mt_rand() % count($exercises);
                $e_oid = array_splice($exercises, $get, 1)[0];
                $exercisesByGroup[$g_oid][] = $e_oid;
                if (!isset($exercisesObjects[$e_oid])){
                    $exercisesObjects[$e_oid] = new Exercise($database, new ObjectId($e_oid));
                }
            }
        }

        $tasks = [];
        foreach ((array) $test->contents as $pair){
            $g_oid = (string) $pair["id"];
            $repeat = max(0, (int) $pair["repeat"]);
            for ($i = 0; $i < $repeat; $i++){
                $e_oid = array_shift($exercisesByGroup[$g_oid]);
                $exercise = $exercisesObjects[$e_oid];
                $tasks[] = ExerciseVariant::create($database, $exercise);
            }
        }

        $taskIds = [];
        foreach ($tasks as $exerciseVariant){
            $taskIds[] = (string) $exerciseVariant->getId();
        }

        $update = [
            '$push' => [
                "tasks" => [
                    '$each' => $taskIds
                ]
            ]
        ];
        $solution->update($update);
    }

}
