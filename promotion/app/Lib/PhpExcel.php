<?php
namespace App\Lib;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PhpExcel{
    public function make($list){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if(count($list)){
            $keys = array_keys($list[0]);
            $column = 'A';
            foreach($keys as $key){
                $sheet->setCellValue($column++.'1', $key);
            }
            $row = 2;
            foreach($list as $obj){
                $column = 'A';
                foreach($keys as $key){
                    $sheet->setCellValue($column++.$row, $obj[$key]);
                }
                $row++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}