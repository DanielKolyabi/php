<?php
function validateDate(string $date) :bool {
    $dateBlocks = array_map('intval', explode("-", $date));
    if (count($dateBlocks) !== 3) {
        return false;
    }
    [$day, $month, $year] = $dateBlocks;
    $invalid = [$day < 1, $day > 31, $month < 1, $month > 12, $year > date('Y')];
    return !in_array(true, $invalid) && checkdate($month, $day, $year);
}

function validateName(string $name) :bool {
    return mb_strlen($name) >= 2;
}

