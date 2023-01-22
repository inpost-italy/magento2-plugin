<?php

namespace InPost\Shipment\Api\Data;

use Magento\Framework\DataObject;

class PointsServiceRequest extends DataObject
{
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->setData('name', $name);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->setData('type', $type);
    }

    /**
     * @return mixed
     */
    public function getFunctions()
    {
        return $this->getData('functions');
    }

    /**
     * @param mixed $functions
     */
    public function setFunctions($functions): void
    {
        $this->setData('functions', $functions);
    }

    /**
     * @return mixed
     */
    public function getPartnerId()
    {
        return $this->getData('partner_id');
    }

    /**
     * @param mixed $partner_id
     */
    public function setPartnerId($partner_id): void
    {
        $this->setData('partner_id', $partner_id);
    }

    /**
     * @return mixed
     */
    public function getIsNext()
    {
        return $this->getData('is_next');
    }

    /**
     * @param mixed $is_next
     */
    public function setIsNext($is_next): void
    {
        $this->setData('is_next', $is_next);
    }

    /**
     * @return mixed
     */
    public function getPaymentAvailable()
    {
        return $this->getData('payment_available');
    }

    /**
     * @param mixed $payment_available
     */
    public function setPaymentAvailable($payment_available): void
    {
        $this->setData('payment_available', $payment_available);
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->getData('city');
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->setData('city', $city);
    }

    /**
     * @return mixed
     */
    public function getProvince()
    {
        return $this->getData('province');
    }

    /**
     * @param mixed $province
     */
    public function setProvince($province): void
    {
        $this->setData('province', $province);
    }

    /**
     * @return mixed
     */
    public function getRelativePoint()
    {
        return $this->getData('relative_point');
    }

    /**
     * @param mixed $relative_point
     */
    public function setRelativePoint($relative_point): void
    {
        $this->setData('relative_point', $relative_point);
    }

    /**
     * @return mixed
     */
    public function getRelativePostCode()
    {
        return $this->getData('relative_post_code');
    }

    /**
     * @param mixed $relative_post_code
     */
    public function setRelativePostCode($relative_post_code): void
    {
        $this->setData('relative_post_code', $relative_post_code);
    }

    /**
     * @return mixed
     */
    public function getMaxDistance()
    {
        return $this->getData('max_distance');
    }

    /**
     * @param mixed $max_distance
     */
    public function setMaxDistance($max_distance): void
    {
        $this->setData('max_distance', $max_distance);
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->getData('limit');
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->setData('limit', $limit);
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->getData('sort_by');
    }

    /**
     * @param mixed $sort_by
     */
    public function setSortBy($sort_by): void
    {
        $this->setData('sort_by', $sort_by);
    }

    /**
     * @return mixed
     */
    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    /**
     * @param mixed $sort_order
     */
    public function setSortOrder($sort_order): void
    {
        $this->setData('sort_order', $sort_order);
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->getData('page');
    }

    /**
     * @param mixed $page
     */
    public function setPage($page): void
    {
        $this->setData('page', $page);
    }

    /**
     * @return mixed
     */
    public function getPerPage()
    {
        return $this->getData('per_page');
    }

    /**
     * @param mixed $per_page
     */
    public function setPerPage($per_page): void
    {
        $this->setData('per_page', $per_page);
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->getData('fields');
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields): void
    {
        $this->setData('fields', $fields);
    }

}
