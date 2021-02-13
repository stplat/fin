<?php

/**
 * Преобразуем массив выгрузки из excel в объект (ассоциативный массив).
 *
 * @param $data array
 * @return \Illuminate\Support\Collection
 */

function ExcelParseHelper($data)
{
  $data = array_map(function ($item, $key) use ($data) {
    $params = [];

    if ($key > 0) {
      foreach ($item as $key => $value) {
        $params[$data[0][$key]] = $value;
      }
    }

    return $params;

  }, $data, array_keys($data));

  array_shift($data);

  return collect($data);
}
