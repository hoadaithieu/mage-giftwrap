<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Renderer_Invoice_Wrappeditems extends  VC_GiftWrap_Model_Sales_Order_Pdf_Renderer_Wrappeditems{
	protected function _setItem($item) {
		$item['product_qty'] = $item['product_qty_invoiced'];
		return $item;
	}
}
