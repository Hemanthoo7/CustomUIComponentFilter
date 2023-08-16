<?php

declare(strict_types=1);

namespace ShippingAddress\PostCode\UiComponent\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;
//use Magento\Tests\NamingConvention\true\bool;

class LikeFilter implements FilterApplierInterface
{
    public function apply(Collection $collection, Filter $filter)
    {
        if($this->isShippingAddressField($filter)){
            $postCodeCondition = $this->getPostCodeCondition($filter);
            $collection->addFieldToFilter($this->getPostCodeField($filter,$postCodeCondition),$postCodeCondition);
        }else{
            $collection->addFieldToFilter($filter->getField(), [$filter->getConditionType() => $filter->getValue()]);
        }
    }

    private function isShippingAddressField(Filter $filter)
    {
        return 'shipping_address' === $filter->getField();
    }

    private function getPostCodeCondition(Filter $filter)
    {
        $postCodes = $this->getPostCodeValues($filter->getValue());

        $result = [];
        foreach ($postCodes as $key => $value){
            $result['key_'.$key] = [$filter->getConditionType() => sprintf('%%s%%', $value)];
        }
        return $result;
    }

    private function getPostCodeValues(string $postCode):array
    {
        $cleanPostCode = preg_replace("/[^A-Za-z\d]/", '', $postCode);

        $result[] = $cleanPostCode;

        if (strlen($cleanPostCode) === 5) {
            $postCode = substr($cleanPostCode, 0, 2) . ' ' . substr($cleanPostCode, 2, 3,);
        } elseif (strlen($cleanPostCode) === 6) {
            $postCode = substr($cleanPostCode, 0, 3) . ' ' . substr($cleanPostCode, 3, 3,);
        } elseif (strlen($cleanPostCode) === 7) {
            $postCode = substr($cleanPostCode, 0, 4) . ' ' . substr($cleanPostCode, 4, 3,);
        }
        $result[] = $postCode;
        return $result;
    }

    private function getPostCodeField(Filter $filter, $condition):array
    {
        $result =[];
        foreach (array_keys($condition) as $key){
            $result[$key] = $filter->getField();
        }

    return $result;
    }
}

