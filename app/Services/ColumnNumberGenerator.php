<?php


namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Table欄位編號產生器
 */
class ColumnNumberGenerator
{
    /**
     * Model class
     *
     * @var Model
     */
    private $model;

    /**
     * 編號的欄位
     *
     * @var string
     */
    private $column;

    /**
     * create instance
     *
     * @param Model $model
     * @param string $column
     */
    public function __construct(Model $model, string $column)
    {
        $this->model = $model;
        $this->column = $column;
    }

    /**
     * create instance
     *
     * @param Model $model
     * @param string $column
     * @return ColumnNumberGenerator
     */
    public static function make(Model $model, string $column): ColumnNumberGenerator
    {
        return new static($model, $column);
    }

    /**
     * 產生編號
     *
     * @param string $prefix
     * @param integer $count
     * @param boolean $isRandom
     * @param string $dateFormat
     * @param string $type
     * @return string
     */
    public function generate(string $prefix, int $count, bool $isRandom = false, string $dateFormat = 'ymd', string $type): string
    {
        $newNumber = '';
        $numberHead = $prefix . now()->format($dateFormat);

        do {
            if ($isRandom) {
                if ($type == 'number') {
                    $numberTail = $this->getRandomNumber($count);
                } elseif ($type == 'string') {
                    $numberTail = $this->getRandomString($count);
                }
            } else {
                $numberTail = $this->getSerialNumber($numberHead, $count);
            }

            $newNumber = $numberHead . $numberTail;
        } while ($this->numberExists($newNumber));

        return $newNumber;
    }

    /**
     * 取得序列號
     *
     * @param string $numberHead
     * @param integer $count
     * @return string
     */
    public function getSerialNumber(string $numberHead, int $count): string
    {
        $newNumberTail = '';
        $lastNumber = $this->getTodayLastNumber($numberHead);

        // 有取得編號，則最後一筆編號+1
        if (isset($lastNumber)) {
            $numberHeadLength = Str::length($numberHead);
            $lastNumberTail = (string)Str::of($lastNumber)->substr($numberHeadLength);
            $newNumberTail = (int)$lastNumberTail + 1;
            $newNumberTail = Str::of($newNumberTail)->padLeft($count, '0');
        } // 未取得編號，則建立第一筆編號
        else {
            $newNumberTail = Str::of('1')->padLeft($count, '0');
        }

        return $newNumberTail;
    }

    /**
     * 取得亂數
     * @param integer $count
     * @return string
     */
    public function getRandomNumber(int $count): ?string
    {
        $range_start = str_pad(1, $count, "0", STR_PAD_LEFT);
        $range_end = str_pad(9, $count, "9", STR_PAD_LEFT);
        $number = rand($range_start, $range_end);
        $number = Str::of($number)->padLeft($count, "0");
        return $number;
    }

    /**
     * 編號是否已存在
     *
     * @param string $number
     * @return boolean
     */
    public function numberExists(string $number): bool
    {
        return $this->model->where($this->column, $number)->count() > 0;
    }

    /**
     * 取得今天最後一筆編號
     *
     * @param string $numberHead
     * @return string|null
     */
    public function getTodayLastNumber(string $numberHead): ?string
    {
        $request = $this->model
            ->where($this->column, 'like', "{$numberHead}%")
            ->latest($this->column)
            ->first();

        return isset($request) ? $request->request_no : null;
    }

    /**
     * 取得亂數
     *
     * @param integer $count
     * @return string
     */
    public function getRandomString(int $count): string
    {
        return Str::upper(Str::random($count));
    }
}
