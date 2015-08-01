<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Invoice extends VC_GiftWrap_Model_Sales_Order_Pdf_Giftwrap {
	protected $_defaultTotalModel = 'vc_giftwrap/sales_order_pdf_total_invoice';
  	protected function _drawHeader(Zend_Pdf_Page $page)
    {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return parent::_drawHeader($page);
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 35
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 290,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 435,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Price'),
            'feed'  => 360,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Tax'),
            'feed'  => 495,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Subtotal'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
	
/**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return parent::getPdf($invoices);
		
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page  = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
            );
			
			
			
			
			
            /* Add table */
            $this->_drawGiftwrapHeader($page);
            /* Add body */
			
			$wrapBlock = new VC_GiftWrap_Block_Adminhtml_Wrap_Invoice();
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
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
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