<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Commercetools\Core\Request\CustomField\Command;

use Commercetools\Core\Model\Common\Context;
use Commercetools\Core\Request\AbstractAction;
use Commercetools\Core\Model\CustomField\FieldContainer;

/**
 * @package Commercetools\Core\Request\CustomField\Command
 *
 * @method string getAction()
 * @method SetCustomTypeAction setAction(string $action = null)
 * @method string getTypeId()
 * @method SetCustomTypeAction setTypeId(string $typeId = null)
 * @method string getTypeKey()
 * @method SetCustomTypeAction setTypeKey(string $typeKey = null)
 * @method FieldContainer getFields()
 * @method SetCustomTypeAction setFields(FieldContainer $fields = null)
 */
class SetCustomTypeAction extends AbstractAction
{
    public function fieldDefinitions()
    {
        return [
            'action' => [static::TYPE => 'string'],
            'typeId' => [static::TYPE => 'string'],
            'typeKey' => [static::TYPE => 'string'],
            'fields' => [static::TYPE => '\Commercetools\Core\Model\CustomField\FieldContainer'],
        ];
    }

    /**
     * @param array $data
     * @param Context|callable $context
     */
    public function __construct(array $data = [], $context = null)
    {
        parent::__construct($data, $context);
        $this->setAction('setCustomType');
    }
}