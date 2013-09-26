<?php
namespace Svea;

/**
 * @author anne-hal
 */
class OrderRow {

    /**
     * Contains empty string if not set
     * @var string
     */
    public $unit = "";

    /**
     * Contains int 0 if not set
     * @var int
     */
    public $vatDiscount = 0;

    /**
     * Optional
     * @param string $articleNumberAsString
     * @return Svea\OrderRow
     */
    public function setArticleNumber($articleNumberAsString) {
        $this->articleNumber = $articleNumberAsString;
        return $this;
    }

    /**
     * Required
     * @param int $quantityAsInt
     * @return Svea\OrderRow
     */
    public function setQuantity($quantityAsInt) {
        $this->quantity = $quantityAsInt;
        return $this;
    }

    /**
     * Optional
     * @param string $unitAsString
     * @return Svea\OrderRow
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }

    /**
     * Optional
     * @param float $AmountAsFloat
     * @return Svea\OrderRow
     */
    public function setAmountExVat($AmountAsFloat) {
        $this->amountExVat = $AmountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * @param float $AmountAsFloat
     * @return Svea\OrderRow
     */
    public function setAmountIncVat($AmountAsFloat) {
        $this->amountIncVat = $AmountAsFloat;
        return $this;
    }

    /**
     * Optional
     * @param string $nameAsString
     * @return Svea\OrderRow
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return Svea\OrderRow
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param int $percentAsInt
     * @return Svea\OrderRow
     */
    public function setVatPercent($percentAsInt) {
        $this->vatPercent = $percentAsInt;
        return $this;
    }

    /**
     * Optional
     * @param int $discountPercentAsInteger
     * @return Svea\OrderRow
     */
    public function setDiscountPercent($discountPercentAsInteger) {
        $this->discountPercent = $discountPercentAsInteger;
        return $this;
    }
}
