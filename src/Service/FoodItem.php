<?php

namespace PH7\ApiSimpleMenu\Service;
use PH7\ApiSimpleMenu\Dal\FoodItemDal;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use Respect\Validation\Validator as v;

class FoodItem 
{
    public function retrieve(string $itemUuid): array {
        if(v::uuid(version:4)->validate($itemUuid)){
            
            if($item = FoodItemDal::get($itemUuid)){
                unset($item['id']);
                return $item;
            } 

            return []; 
        } 
        
        throw new InvalidValidationException("Invalid user UUID");
    }

    public function retrieveAll(): array {
        $items = FoodItemDal::getAll();

        if(count($items) === 0){
            FoodItemDal::createDefaultItem();

            $items = FoodItemDal::getAll();
        }
        return array_map(function(object $item): object{
            unset($item['id']);
            return $item;
        }, $items);
    }

}

