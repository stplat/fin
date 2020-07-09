<?php

/**
 * The replacer number format.
 *
 * @param float $number
 * @return String
 */

function FormatNumberHelper(Float $number)
{
  return number_format($number, 3, ',', ' ');
}
