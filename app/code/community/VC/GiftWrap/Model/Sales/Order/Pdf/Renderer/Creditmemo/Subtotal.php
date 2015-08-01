<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Renderer_Creditmemo_Subtotal {
	public function process($page, &$column, &$top, $lineSpacing, $pdf) {
		$info = $column['info'];
		$price = 0.00;
		foreach ($info['group'] as $sItem) {
			$price += $sItem['product_qty_refunded'] * $sItem['price'];
		}
		
		$column['text'] = array($info['order']->formatPriceTxt($price));												
	}
}
