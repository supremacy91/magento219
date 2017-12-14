<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSearch\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

/**
 * Layer attribute filter
 */
class Attribute extends AbstractFilter
{
    /**
     * @var \Magento\Framework\Filter\StripTags
     */
    private $tagFilter;
    private $registry;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Filter\StripTags $tagFilter,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->tagFilter = $tagFilter;
        $this->catalogSession = $catalogSession;
        $this->registry     = $registry;
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);

        if (empty($attributeValue)) {
            return $this;
        }
        $this->registry->register($this->_requestVar, $attributeValue);

        /* $flag = 1;
        $requestValues = array();
        if($this->catalogSession->getData($this->_requestVar)){
            $requestValues = $this->catalogSession->getData($this->_requestVar);
            foreach ($requestValues as $key => $requestValue) {
                if($requestValue == $attributeValue){
                    unset($requestValues[$key]);
                    $flag = 0;
                }
            }
        }
        if($flag) {
            $requestValues[] = $attributeValue;
        }
        $this->catalogSession->setData($this->_requestVar, $requestValues);*/

       // $this->catalogSession->clearStorage();

        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()
            ->getProductCollection();
      //  $arrayForFilter = $this->catalogSession->getData($this->_requestVar);
        $arrayForFilter = explode('_', $attributeValue);
        if ($arrayForFilter) {
            $productCollection->addFieldToFilter($attribute->getAttributeCode(),
                [
                    'in' => $arrayForFilter

                ]);
        }


     //   $productCollection->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);

        /*$productCollection->addFieldToFilter($attribute->getAttributeCode(),
            [
                'in' => [11, 12]

            ]);*/
      /*  $productCollection->addAttributeToFilter(
            [

                ['attribute'=>$attribute->getAttributeCode(),'eq'=> 12],

            ]);*/

        $label = $this->getOptionText($attributeValue);
        $this->getLayer()
            ->getState()
            ->addFilter($this->_createItem($label, $attributeValue));

      //  $this->setItems([]); // set items to disable show filtering
        return $this;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()
            ->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

        if (count($optionsFacetedData) === 0
            && $this->getAttributeIsFilterable($attribute) !== static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
        ) {
            return $this->itemDataBuilder->build();
        }

        $productSize = $productCollection->getSize();

        $options = $attribute->getFrontend()
            ->getSelectOptions();
        foreach ($options as $option) {
            if (empty($option['value'])) {
                continue;
            }

            $value = $option['value'];

            $count = isset($optionsFacetedData[$value]['count'])
                ? (int)$optionsFacetedData[$value]['count']
                : 0;
            // Check filter type
               /* if (
                    $this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
                    && (!$this->isOptionReducesResults($count, $productSize) || $count === 0)
                ) {
                    continue;
                }*/
            $this->itemDataBuilder->addItemData(
                $this->tagFilter->filter($option['label']),
                $value,
                $count
            );
        }

            return $this->itemDataBuilder->build();
    }
}
