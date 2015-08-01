<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Shipment extends VC_GiftWrap_Model_Sales_Order_Pdf_Giftwrap {
 	protected function _drawHeader(Zend_Pdf_Page $page)
    {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return parent::_drawHeader($page);
		
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y-15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 100,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 35
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  array $shipments
     * @return Zend_Pdf
     */
    public function getPdf($shipments = array())
    {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return parent::getPdf($shipments);
		
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
            );
			
			
			/* Add table */
            $this->_drawGiftwrapHeader($page);
            /* Add body */
			
			$wrapBlock = new VC_GiftWrap_Block_Adminhtml_Wrap_Shipment();
			$wrapBlock->setOrder($order);
			
			$items = $wrapBlock->processGroup($wrapBlock->getList());
			
			$i = 0;
            foreach ($items as $item){
				$i++;
				
				$item->setType('giftwrap');
				$item->setOrder($order);
				$item->setPosition($i);
				if (strlen($item->getBoxImage()) > 0) {
					$item->setBoxImagePath($wrapBlock->getImagePath($item->getBoxImage()));
				}
				
				if (strlen($item->getCardImage()) > 0) {
					$item->setCardImagePath($wrapBlock->getImagePath($item->getCardImage()));
				}
				
                $this->_drawGiftwrapItem($item, $page, $order);
                $page = end($pdf->pages);
            }			
			
			
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return parent::newPage($settings);
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
			if (isset($settings['type_header']) && $settings['type_header'] == 'giftwrap') {
				$this->_drawGiftwrapHeader($page);
			} else 
				$this->_drawHeader($page);
        }
        return $page;
    } 	
}