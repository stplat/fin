<?php

use Illuminate\Support\Carbon;

/**
 * Parser in object Excel array.
 *
 * @param $array
 * @return $array
 */

function ParserInObjectExcelHelper($array, $version)
{
  $result = [];

  foreach ($array as $rowKey => $row) {
    if ($rowKey > 0) {
      $arr = [];

      foreach ($row as $keyCol => $col) {
        $arr[$array[0][$keyCol]] = $row[$keyCol];
        $arr['version_id'] = $version;
        $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
      }

      array_push($result, $arr);
    }
  }

  return $result;
}
