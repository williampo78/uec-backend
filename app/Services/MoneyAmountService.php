<?php

namespace App\Services;

class MoneyAmountService
{
    // 原幣金額
    private $original_price = 0;

    // 原幣未稅金額
    private $original_nontax_price = 0;

    // 原幣稅額
    private $original_tax_price = 0;

    // 本幣金額
    private $price = 0;

    // 本幣未稅金額
    private $nontax_price = 0;

    // 本幣稅額
    private $tax_price = 0;

    // 匯率
    private $exchange_rate = 1;

    // 單價
    private $unit_price = 0;

    // 數量
    private $quantity = 0;

    // 稅別
    private $tax_type = 'TAXABLE';

    /**
     * Get the value of original_price
     */
    public function getOriginalPrice()
    {
        return $this->original_price;
    }

    /**
     * Set the value of original_price
     *
     * @return  self
     */
    public function setOriginalPrice($original_price)
    {
        $this->original_price = $original_price;

        return $this;
    }

    /**
     * Get the value of original_nontax_price
     */
    public function getOriginalNontaxPrice()
    {
        return $this->original_nontax_price;
    }

    /**
     * Get the value of original_tax_price
     */
    public function getOriginalTaxPrice()
    {
        return $this->original_tax_price;
    }

    /**
     * Get the value of price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of nontax_price
     */
    public function getNontaxPrice()
    {
        return $this->nontax_price;
    }

    /**
     * Get the value of tax_price
     */
    public function getTaxPrice()
    {
        return $this->tax_price;
    }

    /**
     * Set the value of exchange_rate
     *
     * @return  self
     */
    public function setExchangeRate($exchange_rate)
    {
        $this->exchange_rate = $exchange_rate;

        return $this;
    }

    /**
     * Set the value of unit_price
     *
     * @return  self
     */
    public function setUnitPrice($unit_price)
    {
        $this->unit_price = $unit_price;

        return $this;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Set the value of tax_type
     *
     * @return  self
     */
    public function setTaxType($tax_type)
    {
        $this->tax_type = $tax_type;

        return $this;
    }

    /**
     * 計算原幣金額
     */
    public function calculateOriginalPrice()
    {
        $this->original_price = $this->unit_price * $this->quantity;

        return $this;
    }

    /**
     * 計算本幣金額
     */
    public function calculatePrice()
    {
        $this->price = $this->unit_price * $this->quantity * $this->exchange_rate;

        return $this;
    }

    /**
     * 計算原幣未稅金額
     */
    public function calculateOriginalNontaxPrice()
    {
        // 免稅
        if ($this->tax_type === 0 || $this->tax_type === 'NON_TAXABLE') {
            $this->original_nontax_price = $this->original_price;
        }
        // 應稅
        // elseif ($this->tax_type === 1) {

        // }
        // 應稅內含
        elseif ($this->tax_type === 2 || $this->tax_type === 'TAXABLE') {
            $this->original_nontax_price = round($this->original_price / ((100 + 5) / 100));
        }
        // 零稅率
        elseif ($this->tax_type === 3 || $this->tax_type === 'ZERO_RATED') {
            $this->original_nontax_price = round($this->original_price / ((100 + 0) / 100));
        }

        return $this;
    }

    /**
     * 計算本幣未稅金額
     */
    public function calculateNontaxPrice()
    {
        // 免稅
        if ($this->tax_type === 0 || $this->tax_type === 'NON_TAXABLE') {
            $this->nontax_price = $this->price;
        }
        // 應稅
        // elseif ($this->tax_type === 1) {

        // }
        // 應稅內含
        elseif ($this->tax_type === 2 || $this->tax_type === 'TAXABLE') {
            $this->nontax_price = round($this->price / ((100 + 5) / 100));
        }
        // 零稅率
        elseif ($this->tax_type === 3 || $this->tax_type === 'ZERO_RATED') {
            $this->nontax_price = round($this->price / ((100 + 0) / 100));
        }

        return $this;
    }

    /**
     * 計算原幣稅額
     */
    public function calculateOriginalTaxPrice()
    {
        $this->original_tax_price = $this->original_price - $this->original_nontax_price;

        return $this;
    }

    /**
     * 計算本幣稅額
     */
    public function calculateTaxPrice()
    {
        $this->tax_price = $this->price - $this->nontax_price;

        return $this;
    }
}
