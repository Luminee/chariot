<?php

namespace Luminee\Chariot\Foundations;

use DB;

trait DBBuilder
{
    /**
     * @param $table
     * @param array $multipleData
     * @return mixed
     */
    public function batchUpdate($table, $multipleData = array())
    {
        if (empty($multipleData)) return false;

        $updateColumn = array_keys($multipleData[0]);
        $referenceColumn = array_shift($updateColumn); //e.g id
        $whereIn = "";

        $q = "UPDATE `" . $table . "` SET ";
        foreach ($updateColumn as $uColumn) {
            $q .= $uColumn . " = CASE ";

            foreach ($multipleData as $data) {
                $q .= "WHEN " . $referenceColumn . " = " . $data[$referenceColumn] . " THEN '" . $data[$uColumn] . "' ";
            }
            $q .= "ELSE " . $uColumn . " END, ";
        }
        foreach ($multipleData as $data) {
            $whereIn .= "'" . $data[$referenceColumn] . "', ";
        }
        $q = rtrim($q, ", ") . " WHERE " . $referenceColumn . " IN (" . rtrim($whereIn, ', ') . ")";

        // Update
        return DB::update(DB::raw($q));
    }
}