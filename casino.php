<?php

/**
 * Вычисление всех вариантов и запись в файл
 *
 * @param int $fieldCount
 * @param int $chipCount
 * @return int
 */
function calculate(int $fieldCount, int $chipCount): int
{
    list($decimalStart, $decimalFinish) = getDecimalShape($fieldCount, $chipCount);
    $handler = fopen(FILE_NAME, "a");
    writeToFile($handler, "                      \n");//Место для итога

    $validPosCounter = 0;
    if (0 != ($decimalStart % 2)) {
        if (substr_count(decbin($decimalStart), '1') == $chipCount) {
            writeToFile($handler, alignResult(decbin($decimalStart), $fieldCount) . "\n");
            $validPosCounter++;
        }

        $decimalStart++;
    }

    for ($i = $decimalStart; $i <= $decimalFinish; $i += 2) {
        $binary = decbin($i);
        $count = substr_count($binary, '1');

        if ($count == $chipCount) {
            $validPosCounter++;

            writeToFile($handler, alignResult($binary, $fieldCount) . "\n");
        } elseif ($count + 1 == $chipCount) {
            writeToFile($handler, alignResult(decbin($i + 1), $fieldCount) . "\n");
            $validPosCounter++;
        }
    }

    if ($validPosCounter >= 10) {
        fclose($handler);
        $handler = fopen(FILE_NAME, "r+");
        writeToFile($handler, $validPosCounter);
    } else {
        fclose($handler);
        $handler = fopen(FILE_NAME, "w");
        writeToFile($handler, "Менее 10 вариантов");
    }

    fclose($handler);
    return $validPosCounter;
}

/**
 * Возвращает десятичное представление начального и конечного значений диапазона
 *
 * @param int $fieldCount
 * @param int $chipCount
 * @return array
 */
function getDecimalShape(int $fieldCount, int $chipCount): array
{
    $str = '';
    for ($i = 0; $i < $fieldCount; $i++) {
        if ($chipCount > $i) {
            $str .= '1';
        } else {
            $str .= '0';
        }
    }

    return [
        bindec((binary)strrev($str)),
        bindec((binary)$str),
    ];
}

/**
 * Возвращает количество полей и фишек для генерации
 *
 * @param array $args
 * @return array
 * @throws Exception
 */
function getParams(array $args): array
{
    if ($args[1] < $args[2]) {
        return [$args[2], $args[1]];
    } elseif ($args[1] > $args[2]) {
        return [$args[1], $args[2]];
    }

    throw new Exception('Необходимо указать количество фишек и полей');
}

/**
 * Запись в файл
 *
 * @param $handler
 * @param string $string
 */
function writeToFile($handler, string $string)
{
    fputs($handler, $string);
}

/**
 * Добавляет пустые поля впереди строки
 *
 * @param string $binary
 * @param int $number
 * @return string
 */
function alignResult(string $binary, int $number):string
{
    for ($i = strlen($binary); $i <= $number; $i++) {
        $binary = '0' . $binary;
    }
    return $binary;
}


const FILE_NAME = './result.txt';
$start = microtime(true);

list($fieldCount, $chipCount) = getParams($argv);
if (file_exists(FILE_NAME)) {
    unlink(FILE_NAME);
}
calculate($fieldCount, $chipCount);

echo("Время - " . (microtime(true) - $start) . "\n");