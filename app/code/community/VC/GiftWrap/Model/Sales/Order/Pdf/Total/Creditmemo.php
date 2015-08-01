<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Total_Creditmemo extends Mage_Sales_Model_Order_Pdf_Total_Default{
    public function getTotalsForDisplay()
    {
	
		$amount = Mage::helper('vc_giftwrap')->getAmountCreditmemoByOrderId($this->getOrder()->getId());
        $amount = $this->getOrder()->formatPriceTxt($amount);
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix().$amount;
        }
        $title = $this->_getSalesHelper()->__($this->getTitle());
        if ($this->getTitleSourceField()) {
            $label = $title . ' (' . $this->getTitleDescription() . '):';
        } else {
            $label = $title . ':';
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = array(
            'amount'    => $amount,
            'label'     => $label,
            'font_size' => $fontSize
        );
        return array($total);
    }
}