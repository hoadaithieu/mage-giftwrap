<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Renderer_Wrappeditems {
	protected function _setItem($item) {
		return $item;
	}
	
	public function process($page, &$column, &$top, $lineSpacing, $pdf) {
		$info = $column['info'];
		//$pdf->_setFontRegular($page, 10);
		$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setLineWidth(0.5);
		$page->drawRectangle($column['feed'], $pdf->y + 10, $column['feed'] + 210, $pdf->y - 5);
		$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
	
	
	
		$page->drawText('Product', $column['feed'] + 5, $pdf->y, 'UTF-8');
		$page->drawText('Price', $column['feed'] + 140, $pdf->y, 'UTF-8');
		$page->drawText('Qty', $column['feed'] + 190, $pdf->y, 'UTF-8');
		
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		
		// begin border
		$_top = $top;
		$_loop = 0;
		foreach ($info['group'] as $sItem) {
			$nameAr = Mage::helper('core/string')->str_split($sItem['product_name'], 30, true, true);
			$_top += 2*$lineSpacing + (count($nameAr) - 1) * $lineSpacing + 10;
			$_loop++;
		}
		
		$page->setFillColor(new Zend_Pdf_Color_RGB(255, 255, 255));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setLineWidth(0.5);
		$page->drawRectangle($column['feed'], $pdf->y - 5, $column['feed'] + 210, $pdf->y - $_top);
		$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		// end border
		
		$i = 0;
		foreach ($info['group'] as $sItem) {
			$sItem = $this->_setItem($sItem);
			$i++;
			$top += $lineSpacing;
			
			$nameAr = Mage::helper('core/string')->str_split($sItem['product_name'], 30, true, true);
			for ($j = 0; $j < count($nameAr); $j++) {
				$page->drawText($nameAr[$j], $column['feed'] + 5, $pdf->y - $top - ($j * $lineSpacing), 'UTF-8');
			}
			//$page->drawText($sItem['product_name'], $column['feed'] + 5, $pdf->y - $top, 'UTF-8');
			$page->drawText($info['order']->formatPriceTxt($sItem['price']), $column['feed'] + 140, $pdf->y - $top, 'UTF-8');
			$page->drawText($sItem['product_qty'], $column['feed'] + 190, $pdf->y - $top, 'UTF-8');
			$top += $lineSpacing + (count($nameAr) - 1) * $lineSpacing;
			$page->drawText('SKU: '.$sItem['sku'], $column['feed'] + 5, $pdf->y - $top, 'UTF-8');
			
			// draw separated line
			if ($i < $_loop) {
				$top += 10;
				$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
				$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
				$page->setLineWidth(0.5);
				$page->drawRectangle($column['feed'], $pdf->y - $top, $column['feed'] + 210, $pdf->y - $top);
				$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
			}
		}
	}
}
