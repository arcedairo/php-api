<?php

namespace PH7\ApiSimpleMenu\Dal;

use PH7\ApiSimpleMenu\Entity\Item as ItemEntity;
use Ramsey\Uuid\Uuid;
use RedBeanPHP\R;

final class FoodItemDal 
{
    public const TABLE_NAME = 'fooditems';

    public static function get(string $itemUuid): ItemEntity
    {

        $bindings = ['itemUuid' => $itemUuid];

        $itemBean = R::findOne(self::TABLE_NAME, 'item_uuid = :itemUuid', $bindings);

        return (new ItemEntity())->unserialize($itemBean?->export());
    }

    public static function getAll(): array
    {
        $itemsBean = R::findAll(self::TABLE_NAME);

        $areAnyItems = $itemsBean && count($itemsBean);

        if(!$areAnyItems){
            return [];
        }

        return array_map(
            function (object $itemBean): array{
                $itemEntity = (new ItemEntity())->unserialize($itemBean?->export());

                return [
                    'food_Uuid' => $itemEntity->getItemUuid(),
                    'name' => $itemEntity->getName(),
                    'price' => $itemEntity->getPrice(),
                    'available' => $itemEntity->getAvailable()
                ];
            }, $itemsBean);
    }

    public static function createDefaultItem(ItemEntity $itemEntity): int|string
    {
        $itemBean = R::dispense(self::TABLE_NAME);
        
        $itemBean->itemUuid = $itemEntity->getItemUuid();
        $itemBean->name = $itemEntity->getName();
        $itemBean->price = $itemEntity->getPrice();
        $itemBean->available = $itemEntity->getAvailable();

        return R::store($itemBean);
    }
}