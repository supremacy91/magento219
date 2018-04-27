<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Supreme\DisplayOrder\Model\CategoryLanding;

/**
 * Catalog category landing page attribute source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Type extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**a
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => \Magento\Catalog\Model\Category::DM_PRODUCT, 'label' => __('1 Item Display')],
                ['value' => \Magento\Catalog\Model\Category::DM_PAGE, 'label' => __('2 Items Display')],
                ['value' => \Magento\Catalog\Model\Category::DM_MIXED, 'label' => __('3 Items Display')],
                ['value' => \Magento\Catalog\Model\Category::DM_MIXED, 'label' => __('4 Items Display')],
                ['value' => \Magento\Catalog\Model\Category::DM_MIXED, 'label' => __('5 Items Display')],
                ['value' => \Magento\Catalog\Model\Category::DM_MIXED, 'label' => __('6 Items Display')],
                ['value' => \Magento\Catalog\Model\Category::DM_MIXED, 'label' => __('7 Items Display')],
            ];
        }
        return $this->_options;
    }
}
