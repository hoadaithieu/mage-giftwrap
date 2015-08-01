<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Renderer_Image {
	public function process($page, &$column, &$top, $lineSpacing, $pdf) {
		$info = $column['info'];
		if (isset($info['image']) && strlen($info['image']) > 0) {
			$image = Zend_Pdf_Image::imageWithPath($info['image']);
			$page->drawImage($image, $column['feed'], $pdf->y - 50, $column['feed'] + 50, $pdf->y);
			$top += 60;
		}
	}
}
