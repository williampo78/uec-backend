<?php
namespace App\Imports\Product;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class BatchImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductBasic(),
            new ProductPhoto(),
        ];
    }

}
