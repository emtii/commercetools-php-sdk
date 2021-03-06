<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace Commercetools\Core\Model\ShippingMethod;

use Commercetools\Core\Model\Common\JsonObject;
use Commercetools\Core\Model\Common\Money;

/**
 * @package Commercetools\Core\Model\ShippingMethod
 * @link https://dev.commercetools.com/http-api-projects-shippingMethods.html#shippingrate
 * @method Money getPrice()
 * @method ShippingRate setPrice(Money $price = null)
 * @method Money getFreeAbove()
 * @method ShippingRate setFreeAbove(Money $freeAbove = null)
 */
class ShippingRate extends JsonObject
{
    public function fieldDefinitions()
    {
        return [
            'price' => [static::TYPE => '\Commercetools\Core\Model\Common\Money'],
            'freeAbove' => [static::TYPE => '\Commercetools\Core\Model\Common\Money'],
        ];
    }
}
