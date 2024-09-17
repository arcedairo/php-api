<?php

namespace PH7\ApiSimpleMenu\Service;
use PH7\ApiSimpleMenu\Dal\FoodItemDal;
use PH7\ApiSimpleMenu\Entity\Item as ItemEntity;
use Ramsey\Uuid\Uuid;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use Respect\Validation\Validator as v;

class FoodItem 
{
    public function retrieve(string $itemUuid): array {
        if(v::uuid(version:4)->validate($itemUuid)){
            
            if($item = FoodItemDal::get($itemUuid)){
               if($item->getItemUuid()){
                    return [
                        'itemUuid' => $item->getItemUuid(),
                        'name' => $item->getName(),
                        'price' => $item->getPrice(),
                        'available' => $item->getAvailable() 
                    ];
               }
            } 

            return []; 
        } 
        
        throw new InvalidValidationException("Invalid user UUID");
    }

    public function retrieveAll(): array {
        $items = FoodItemDal::getAll();

        if(count($items) === 0){

            $itemUuid = Uuid::uuid4()->toString();
            $itemEntity = new ItemEntity();
            $itemEntity->setItemUuid($itemUuid);
            $itemEntity->setName('Burrito Cheese Chips');
            $itemEntity->setPrice(19.99);
            $itemEntity->setAvailable(true);

            FoodItemDal::createDefaultItem($itemEntity);

            $items = FoodItemDal::getAll();
        }

        return $items;
    }

}

