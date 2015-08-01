<?php
/**
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class VC_GiftWrap_Model_Sales_Order_Pdf_Items_Invoice_Giftwrap extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{

    /**
     * Draw item line
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();

        // draw
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split('#'.$item->getPosition(), 35, true, true),
            'feed' => 40,
			'align' => 'right'
        ));

        $lines[0][] = array(
			'renderer' => 'vc_giftwrap/sales_order_pdf_renderer_image',
			'info' => array('image' => $item->getBoxImagePath()),
            'text' => array($item->getBoxTitle(),
				$order->formatPriceTxt($item->getBoxPrice())),
            'feed' => 60,
			'align' => 'left'
        );

        $lines[0][]= array(
			'renderer' => 'vc_giftwrap/sales_order_pdf_renderer_image',
			'info' => array('image' => $item->getCardImagePath()),
            'text' => array($item->getCardTitle(),
				$order->formatPriceTxt($item->getCardPrice())),
            'feed' => 120,
			'align' => 'left'
        );

        $lines[0][] = array(
            'text' => Mage::helper('core/string')->str_split($item->getCardMessage(), 30, true, true),
            'feed' => 180,
			'align' => 'left'
        );

        $lines[0][] = array(
			'renderer' => 'vc_giftwrap/sales_order_pdf_renderer_invoice_wrappeditems',
			'info' => array('order' => $order, 'group' => $item->getGroupItem()),
            'text' => '',
            'feed' => 300,
			'align' => 'left'
        );
		
        $lines[0][] = array(
			'renderer' => 'vc_giftwrap/sales_order_pdf_renderer_invoice_subtotal',
			'info' => array('order' => $order, 'group' => $item->getGroupItem()),
            'text' => '',
            'feed' => 565,
			'font'  => 'bold',
			'align' => 'right'
        );
		

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true, 'type_header' => 'giftwrap'));
        $this->setPage($page);
    }
}
