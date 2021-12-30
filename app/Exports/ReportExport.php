<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

// use Maatwebsite\Excel\Concerns\FromCollection;

class ReportExport implements FromCollection
{
    protected $title;
    protected $data;

    public function __construct($title, $data)
    {
        $this->title = $title;
        $this->data = $data;
    }
    public function collection()
    {
        return collect($this->createData());
    }
    //TEST
    public function createData()
    {
        $title = $this->title;
        $result[] = $title;
        foreach($this->arrangeData() as $val){
            $result[] = $val ; 
        }
        return $result;
    }
    public function arrangeData()
    {
        $data = $this->data;
        $title = $this->title;
        $result = [];
        $index = 0 ;
        foreach ($data as $datakey => $dataval) {
            $index ++   ;
            foreach ($title as $titleKey => $titleVal) {
                switch ($titleKey) {
                    case 'index':
                        $result[$datakey][$titleKey] = $index ; 
                        break;
                    default:
                        $result[$datakey][$titleKey] = $data[$datakey][$titleKey];
                        break;
                }
            }
        }
        return $result ;
    }

}
