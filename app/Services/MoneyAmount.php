<?php

namespace App\Services;

class MoneyAmount
{
    /**
     * 原幣金額
     *
     * @var float
     */
    private $originalPrice = 0;

    /**
     * 原幣未稅金額
     *
     * @var float
     */
    private $originalNontaxPrice = 0;

    /**
     * 原幣稅額
     *
     * @var float
     */
    private $originalTaxPrice = 0;

    /**
     * 本幣金額
     *
     * @var float
     */
    private $price = 0;

    /**
     * 本幣未稅金額
     *
     * @var float
     */
    private $nontaxPrice = 0;

    /**
     * 本幣稅額
     *
     * @var float
     */
    private $taxPrice = 0;

    /**
     * 匯率
     *
     * @var float
     */
    private $exchangeRate;

    /**
     * 單價
     *
     * @var float
     */
    private $unitPrice;

    /**
     * 數量
     *
     * @var integer
     */
    private $quantity;

    /**
     * 稅別
     *
     * @var string|integer
     */
    private $taxType;

    public function __construct(float $unitPrice = 0, int $quantity = 0, $taxType = 'TAXABLE', float $exchangeRate = 1)
    {
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
        $this->taxType = $taxType;
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * create instance
     *
     * @param float $unitPrice
     * @param integer $quantity
     * @param string|integer $taxType
     * @param float $exchangeRate
     * @return static
     */
    public static function make(float $unitPrice = 0, int $quantity = 0, $taxType = 'TAXABLE', float $exchangeRate = 1)
    {
        return new static($unitPrice, $quantity, $taxType, $exchangeRate);
    }

    /**
     * create local instance
     *
     * @param float $price
     * @param string|integer $taxType
     * @return static
     */
    public static function makeByPrice(float $price, $taxType = 'TAXABLE')
    {
        $moneyAmount = new static(0, 0, $taxType);
        $moneyAmount->price = $price;

        return $moneyAmount;
    }

    /**
     * Get the value of originalPrice
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * Set the value of originalPrice
     *
     * @return  self
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    /**
     * Get the value of originalNontaxPrice
     */
    public function getOriginalNontaxPrice()
    {
        return $this->originalNontaxPrice;
    }

    /**
     * Get the value of originalTaxPrice
     */
    public function getOriginalTaxPrice()
    {
        return $this->originalTaxPrice;
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
     * Get the value of nontaxPrice
     */
    public function getNontaxPrice()
    {
        return $this->nontaxPrice;
    }

    /**
     * Get the value of taxPrice
     */
    public function getTaxPrice()
    {
        return $this->taxPrice;
    }

    /**
     * Set the value of exchangeRate
     *
     * @return  self
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * Set the value of unitPrice
     *
     * @return  self
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

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
     * Set the value of taxType
     *
     * @return  self
     */
    public function setTaxType($taxType)
    {
        $this->taxType = $taxType;

        return $this;
    }

    /**
     * 計算原幣金額
     *
     * @return self
     */
    public function calculateOriginalPrice()
    {
        $this->originalPrice = $this->unitPrice * $this->quantity;

        return $this;
    }

    /**
     * 計算本幣金額
     *
     * @return self
     */
    public function calculatePrice()
    {
        $this->price = $this->unitPrice * $this->quantity * $this->exchangeRate;

        return $this;
    }

    /**
     * 計算原幣未稅金額
     *
     * @return self
     */
    public function calculateOriginalNontaxPrice()
    {
        // 免稅
        if ($this->taxType === 0 || $this->taxType === 'NON_TAXABLE') {
            $this->originalNontaxPrice = $this->originalPrice;
        }
        // 應稅
        // elseif ($this->taxType === 1) {

        // }
        // 應稅內含
        elseif ($this->taxType === 2 || $this->taxType === 'TAXABLE') {
            $this->originalNontaxPrice = round($this->originalPrice / ((100 + 5) / 100));
        }
        // 零稅率
        elseif ($this->taxType === 3 || $this->taxType === 'ZERO_RATED') {
            $this->originalNontaxPrice = round($this->originalPrice / ((100 + 0) / 100));
        }

        return $this;
    }

    /**
     * 計算本幣未稅金額
     *
     * @return self
     */
    public function calculateNontaxPrice()
    {
        // 免稅
        if ($this->taxType === 0 || $this->taxType === 'NON_TAXABLE') {
            $this->nontaxPrice = $this->price;
        }
        // 應稅
        // elseif ($this->taxType === 1) {

        // }
        // 應稅內含
        elseif ($this->taxType === 2 || $this->taxType === 'TAXABLE') {
            $this->nontaxPrice = round($this->price / ((100 + 5) / 100));
        }
        // 零稅率
        elseif ($this->taxType === 3 || $this->taxType === 'ZERO_RATED') {
            $this->nontaxPrice = round($this->price / ((100 + 0) / 100));
        }

        return $this;
    }

    /**
     * 計算原幣稅額
     *
     * @return self
     */
    public function calculateOriginalTaxPrice()
    {
        $this->originalTaxPrice = $this->originalPrice - $this->originalNontaxPrice;

        return $this;
    }

    /**
     * 計算本幣稅額
     *
     * @return self
     */
    public function calculateTaxPrice()
    {
        $this->taxPrice = $this->price - $this->nontaxPrice;

        return $this;
    }

    /**
     * 計算金額
     *
     * @param string $type all(全部), original(原幣), local(本幣)
     * @param boolean $hasPrice
     * @return self
     */
    public function calculate(string $type = 'all', bool $hasPrice = false): self
    {
        switch ($type) {
            case 'original':
                if (!$hasPrice) {
                    $this->calculateOriginalPrice();
                }

                $this->calculateOriginalNontaxPrice()
                    ->calculateOriginalTaxPrice();
                break;

            case 'local':
                if (!$hasPrice) {
                    $this->calculatePrice();
                }

                $this->calculateNontaxPrice()
                    ->calculateTaxPrice();
                break;

            default:
                if (!$hasPrice) {
                    $this->calculateOriginalPrice()
                        ->calculatePrice();
                }

                $this->calculateOriginalNontaxPrice()
                    ->calculateOriginalTaxPrice()
                    ->calculateNontaxPrice()
                    ->calculateTaxPrice();
                break;
        }

        return $this;
    }
}
