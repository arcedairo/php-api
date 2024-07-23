<?php
namespace PH7\ApiSimpleMenu\Dal;

use PH7\ApiSimpleMenu\Entity\User as UserEntity;
use RedBeanPHP\R;

final class UserDal
{

    public const TABLE_NAME = 'users';

    /**
     * @throws \RedBeanPHP\RedException\SQL
     */

    public static function create(UserEntity $userEntity): int|string
    {
       $userBean = R::dispense(self::TABLE_NAME);
       $userBean->user_uuid = $userEntity->getUserUuid();
       $userBean->first_name = $userEntity->getFirstName();
       $userBean->last_name = $userEntity->getLastName();
       $userBean->email = $userEntity->getEmail();
       $userBean->phone = $userEntity->getPhone();
       $userBean->created_date = $userEntity->getCreationDate();

       $id = R::store($userBean);

       R::close();
       
       return $id;
    }

    public static function update(string $userUuid, UserEntity $userEntity): int|string
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = :userUuid', ['userUuid' => $userUuid]);

        if($userBean){

            $firstName = $userEntity->getFirstName();
            $lastName = $userEntity->getLastName();
            $phone = $userEntity->getPhone();

            if($firstName){
                $userBean->firstName = $firstName;
            }

            if($lastName){
                $userBean->lastName = $lastName;
            }

            if($phone){
                $userBean->phone = $phone;
            }

            return R::store($userBean);
        }

        return 0;

    }

    public static function get(string $userUuid): ?array
    {
        $bindings = ['userUuid' => $userUuid];
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = :userUuid', $bindings);
        return $userBean?->export();
    }

    public static function getAll(): array {
        return R::findAll(self::TABLE_NAME);
    }

    public static function remove(string $userUuid): int {
       $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = :userUuid', ['userUuid' => $userUuid]);
       if($userBean){
            return (bool)R::trash($userBean);
        }

        return false; 
    }
}