<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace Commercetools\Core\Model\Common;

class LocalizedEnumTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $context = Context::of()->setLanguages(['en']);

        $this->assertSame(
            'Test',
            (string)LocalizedEnum::fromArray(
                [
                    'key' => 'test',
                    'label' => [
                        'en' => 'Test'
                    ]
                ],
                $context
            )
        );
    }
}
