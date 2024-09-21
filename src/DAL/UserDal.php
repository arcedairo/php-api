<?php
namespace PH7\ApiSimpleMenu\Dal;

use PH7\ApiSimpleMenu\Entity\User as UserEntity;
use PH7\ApiSimpleMenu\Service\User;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

final class UserDal
{

    public const TABLE_NAME = 'users';

    public static function create(UserEntity $userEntity): string|false
    {
       $userBean = R::dispense(self::TABLE_NAME);
       $userBean->user_uuid = $userEntity->getUserUuid();
       $userBean->first_name = $userEntity->getFirstName();
       $userBean->last_name = $userEntity->getLastName();
       $userBean->email = $userEntity->getEmail();
       $userBean->phone = $userEntity->getPhone();
       $userBean->password = $userEntity->getPassword();
       $userBean->created_date = $userEntity->getCreationDate();

       try{
            $redBeanIncrementId = R::store($userBean);
        } catch(SQL $e){
            return false;
        } finally {
            R::close();
        }

        $userBean = R::load(self::TABLE_NAME, $redBeanIncrementId);

        return $userBean->user_uuid;
    }

    public static function update(string $userUuid, UserEntity $userEntity): int|string|false
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

            try{
                return R::store($userBean);
            } catch (SQL $e){
                return false;
            } finally {
                R::close();
            }
        }

        return false;

    }

    public static function getById(string $userUuid): UserEntity
    {
        $bindings = ['userUuid' => $userUuid];
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = :userUuid', $bindings);
        return (new UserEntity())->unserialize($userBean?->export());
    }

    public static function getByEmail(string $email): UserEntity
    {
        $bindings = ['email' => $email];
        $userBean = R::findOne(self::TABLE_NAME, 'email = :email', $bindings);
        return (new UserEntity())->unserialize($userBean?->export());
    }

    public static function setToken(string $jwtToken, string $userUuid): void 
    {
        $bindings = [
            'userUuid' => $userUuid
        ];
        
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = :userUuid', $bindings);

        $userBean->session_token = $jwtToken;
        $userBean->last_session_time = time();

        R::store($userBean);

        R::close();
    }

    public static function getAll(): ?array {
        $usersBean = R::findAll(self::TABLE_NAME);

        $areAnyUsers = $usersBean && count($usersBean);

        if(!$areAnyUsers){
            return [];
        }

        return array_map(function(object $userBean): array{
            $userEntity = (new UserEntity())->unserialize($userBean?->export());
            return [
                'userUuid' => $userEntity->getUserUuid(),
                'first' => $userEntity->getFirstName(),
                'last' => $userEntity->getLastName(),
                'email' => $userEntity->getEmail(),
                'phone' => $userEntity->getPhone(),
                'creationDate' => $userEntity->getCreationDate()
            ];
        }, $usersBean);
    }

    public static function remove(string $userUuid): int {
       $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = :userUuid', ['userUuid' => $userUuid]);
       if($userBean){
            return (bool)R::trash($userBean);
        }

        return false; 
    }

    public static function doesEmailExist(string $email): bool
    {
        return $userBean = R::findOne(self::TABLE_NAME, 'email = :email', ['email' => $email]) !==null;
    }
}