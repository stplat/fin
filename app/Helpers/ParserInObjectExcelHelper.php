<?php

use Illuminate\Support\Carbon;

/**
 * Parser in object Excel array.
 *
 * @param $array
 * @param $version
 * @param $period
 * @return array
 */

function ParserInObjectExcelHelper($array, $version = null, $period = null)
{
  $result = [];

  foreach ($array as $rowKey => $row) {
    if ($rowKey > 0) {
      $arr = [];

      foreach ($row as $keyCol => $col) {
        $arr[$array[0][$keyCol]] = $row[$keyCol];
        $version ? $arr['version_id'] = $version : '';
        $period ? $arr['period_id'] = $period : '';
        $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
      }

      array_push($result, $arr);
    }
  }

  return $result;
}
