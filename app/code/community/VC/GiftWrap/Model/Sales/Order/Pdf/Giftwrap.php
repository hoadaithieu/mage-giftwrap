<?php
class VC_GiftWrap_Model_Sales_Order_Pdf_Giftwrap extends Mage_Sales_Model_Order_Pdf_Abstract {
 	public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }
					

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
					
					if (isset($column['renderer'])) {
						Mage::getModel($column['renderer'])->process($page, $column, $top, $lineSpacing, $this);
					}
					
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;

                        }
						
						
                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }
 	/**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
	protected function _drawGiftwrapHeader(Zend_Pdf_Page $page) {
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
            'text' => Mage::helper('vc_giftwrap')->__('NO'),
            'feed' => 35
        );

        $lines[0][] = array(
            'text'  => Mage::helper('vc_giftwrap')->__('GIFT BOX'),
            'feed'  => 60,
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('vc_giftwrap')->__('GIFT CARD'),
            'feed'  => 120,
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('vc_giftwrap')->__('CARD MESSAGE'),
            'feed'  => 180,
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('vc_giftwrap')->__('WRAPPED ITEMS'),
            'feed'  => 300,
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('vc_giftwrap')->__('SUBTOTAL'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true, 'type_header' => 'giftwrap'));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;	
	}
	 
   
	
	protected function _drawGiftwrapItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order) {
        $orderItem = $item->getOrderItem();
        $renderer = $this->_getRenderer($item->getType());
		
        $this->renderItem($item, $page, $order, $renderer);
		
        $transportObject = new Varien_Object(array('renderer_type_list' => array()));
        Mage::dispatchEvent('pdf_item_draw_after', array(
            'transport_object' => $transportObject,
            'entity_item'      => $item
        ));

        foreach ($transportObject->getRendererTypeList() as $type) {
            $renderer = $this->_getRenderer($type);
            if ($renderer) {
                $this->renderItem($orderItem, $page, $order, $renderer);
            }
        }
		
        return $renderer->getPage();
	}

    public function getPdf() {
	}

}