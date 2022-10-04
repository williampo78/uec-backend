<?php

namespace App\Exports\Product;

use Maatwebsite\Excel\Concerns\FromCollection;

class ErrorImpoerLogExport implements  FromCollection
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }
}
