<?php

namespace PH7\ApiSimpleMenu\Dal;

use Ramsey\Uuid\Uuid;
use RedBeanPHP\R;

class FoodItemDal 
{
    public const TABLE_NAME = 'fooditems';

    public static function get(string $itemUuid): ?array {

        $bindings = ['itemUuid' => $itemUuid];

        $itemBean = R::findOne(self::TABLE_NAME, 'item_uuid = :itemUuid', $bindings);

        return $itemBean?->export();
    }

    public static function getAll(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function createDefaultItem(): int|string
    {
        $itemBean = R::dispense(self::TABLE_NAME);
        $itemBean->item_uuid = Uuid::uuid4()->toString();
        $itemBean->name = 'Burrito chips';
        $itemBean->price = 19.55;
        $itemBean->available = true;

        return R::store($itemBean);
    }
}