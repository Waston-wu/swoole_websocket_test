<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/29/029
 * Time: 9:44
 */
$arrq = array();
for($i = 0; $i <1000000; $i++)
{
    $data = "hello $i\n";
    $arrq[] = $data;
    if ($i % 100 == 99 and count($arrq) > 100)
    {
        $popN = rand(10, 99);
        for ($j = 0; $j < $popN; $j++)
        {
            array_shift($arrq);
        }
    }
}
$popN = count($arrq);
for ($j = 0; $j < $popN; $j++)
{
    array_shift($arrq);
}