<?php

declare(strict_types=1);

namespace Entity;

use PH7\ApiSimpleMenu\Entity\Item as ItemEntity;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class Itemtest extends TestCase
{
    private ItemEntity $itemEntity;

    protected function setUp():void
    {
        $this->itemEntity = new ItemEntity();
    }

    public function testSequentialId():void
    {
        $expectedValue = 1;

        $this->itemEntity->setSequentialId(1);

        $this->assertSame(1, $this->itemEntity->getSequentialId($expectedValue));
    }

    public function testUnserialize():void
    {
        $uuid = Uuid::uuid4()->toString();
        $expectedItemData = [
            'id' => 500,
            'item_uuid' => $uuid,
            'name' => 'blue cheese',
            'price' => 23.0,
            'available' => true
        ];

        $this->itemEntity->unserialize($expectedItemData);

        $this->assertSame($expectedItemData['id'], $this->itemEntity->getSequentialId());
        $this->assertSame($expectedItemData['item_uuid'], $this->itemEntity->getItemUuid());
        $this->assertSame($expectedItemData['name'], $this->itemEntity->getName());
        $this->assertSame($expectedItemData['price'], $this->itemEntity->getPrice());
        $this->assertTrue($expectedItemData['available']);
    }
}