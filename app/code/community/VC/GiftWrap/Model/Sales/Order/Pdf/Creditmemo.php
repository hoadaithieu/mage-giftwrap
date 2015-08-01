<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Creditmemo extends VC_GiftWrap_Model_Sales_Order_Pdf_Giftwrap {
	protected $_defaultTotalModel = 'vc_giftwrap/sales_order_pdf_total_creditmemo';
  	
	protected function _drawHeader(Zend_Pdf_Page $page)
    {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return parent::_drawHeader($page);
		
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 30);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 35,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('SKU'), 12, true, true),
            'feed'  => 255,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Total (ex)'), 12, true, true),
            'feed'  => 330,
            'align' => 'right',
            //'width' => 50,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Discount'), 12, true, true),
            'feed'  => 380,
            'align' => 'right',
            //'width' => 50,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Qty'), 12, true, true),
            'feed'  => 445,
            'align' => 'right',
            //'width' => 30,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Tax'), 12, true, true),
            'feed'  => 495,
            'align' => 'right',
            //'width' => 45,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Total (inc)'), 12, true, true),
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
     * @param  array $creditmemos
     * @return Zend_Pdf
     */
    public function getPdf($creditmemos = array())
    {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return parent::getPdf($creditmemos);
		
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($creditmemos as $creditmemo) {
            if ($creditmemo->getStoreId()) {
                Mage::app()->getLocale()->emulate($creditmemo->getStoreId());
                Mage::app()->setCurrentStore($creditmemo->getStoreId());
            }
            $page  = $this->newPage();
            $order = $creditmemo->getOrder();
            /* Add image */
            $this->insertLogo($page, $creditmemo->getStore());
            /* Add address */
            $this->insertAddress($page, $creditmemo->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Credit Memo # ') . $creditmemo->getIncrementId()
            );
			
			
			/* Add table */
            $this->_drawGiftwrapHeader($page);
            /* Add body */
			
			$wrapBlock = new VC_GiftWrap_Block_Adminhtml_Wrap_Creditmemo();
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
			
            /* Add table head */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($creditmemo->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $creditmemo);
        }
        $this->_afterGetPdf();
        if ($creditmemo->getStoreId()) {
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
		
        $page = parent::newPage($settings);
        if (!empty($settings['table_header'])) {
			if (isset($settings['type_header']) && $settings['type_header'] == 'giftwrap') {
				$this->_drawGiftwrapHeader($page);
			} else 
            	$this->_drawHeader($page);
        }
        return $page;
    }
		
}