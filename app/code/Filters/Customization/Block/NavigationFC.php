<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog layered navigation view block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Filters\Customization\Block;

use Magento\LayeredNavigation\Block\Navigation;

class NavigationFC extends Navigation
{

    public function getScopeConfig(){
        return $this->_scopeConfig;
    }
}
