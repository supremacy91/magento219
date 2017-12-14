<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Filter item model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Layer\Filter;

class Item extends \Magento\Framework\DataObject
{
    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Html pager block
     *
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $_htmlPagerBlock;
    private $registry;
    /**
     * Construct
     *
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_url = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->registry     = $registry;
        parent::__construct($data);
    }

    /**
     * Get filter instance
     *
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The filter must be an object. Please set the correct filter.')
            );
        }
        return $filter;
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $registerValue = $this->registry->registry($this->getFilter()->getRequestVar());
        $current_value = '';
        $nextValue = $this->getValue()  ;
        if (!$registerValue) {
            $query = [
                $this->getFilter()->getRequestVar() => $nextValue,
                // exclude current page from urls
                $this->_htmlPagerBlock->getPageVarName() => null,
            ];
            return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        } else {


            $flag = 1;
            $registerValueArray = explode('_', $registerValue);
            foreach ($registerValueArray as $key => $value){
                if ($value == $nextValue) {
                    unset($registerValueArray[$key]);
                    $flag = 0;
                }
            }
            $registerValue = implode('_', $registerValueArray);

            if($flag) {
                $registerValue = $registerValue . '_' . $nextValue;
            }

            if($registerValue) {
                $query = [
                    $this->getFilter()->getRequestVar() => $current_value . $registerValue,
                    // exclude current page from urls
                        $this->_htmlPagerBlock->getPageVarName() => null,
                    ];
                return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
            }

            return $this->getRemoveUrl();

        }


    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $query = [$this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue()];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->_url->getUrl('*/*/*', $params);
    }

    /**
     * Get url for "clear" link
     *
     * @return false|string
     */
    public function getClearLinkUrl()
    {
        $clearLinkText = $this->getFilter()->getClearLinkText();
        if (!$clearLinkText) {
            return false;
        }

        $urlParams = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [$this->getFilter()->getRequestVar() => null],
            '_escape' => true,
        ];
        return $this->_url->getUrl('*/*/*', $urlParams);
    }

    /**
     * Get item filter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFilter()->getName();
    }

    /**
     * Get item value as string
     *
     * @return string
     */
    public function getValueString()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }

    public function getRegistry()
    {
        return $this->registry;
    }
}
