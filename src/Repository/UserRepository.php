<?php
/**
 * User repository
 */

namespace Repository;

use Silex\Application;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * Class UserRepository.
 *
 * @package Repository
 */
class UserRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * TagRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Loads user by login.
     *
     * @param string $login User login
     * @throws UsernameNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function loadUserByLogin($login)
    {
        try {
            $user = $this->getUserByLogin($login);

            if (!$user || !count($user)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            $roles = $this->getUserRoles($user['user_id']);

            if (!$roles || !count($roles)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            return [
                'username' => $user['username'],
                'password' => $user['password'],
                'roles' => $roles,
            ];
        } catch (DBALException $exception) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)
            );
        } catch (UsernameNotFoundException $exception) {
            throw $exception;
        }
    }

    /**
     * Gets user data by login.
     *
     * @param string $login User login
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserByLogin($login)
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('u.user_id', 'u.username', 'u.password')
                ->from('users', 'u')
                ->where('u.username = :login')
                ->setParameter(':login', $login, \PDO::PARAM_STR);

            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }

    /**
     * Gets user roles by User ID.
     *
     * @param integer $userId User ID
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserRoles($userId)
    {
        $roles = [];

        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('r.role_name')
                ->from('users', 'u')
                ->innerJoin('u', 'roles', 'r', 'u.FK_role_id = r.role_id')
                ->where('u.user_id = :id')
                ->setParameter(':id', $userId, \PDO::PARAM_INT);
            $result = $queryBuilder->execute()->fetchAll();

            if ($result) {
                $roles = array_column($result, 'role_name');
            }

            return $roles;
        } catch (DBALException $exception) {
            return $roles;
        }
    }

    /**
     * Add new user
     * @param Application $app
     * @param $user
     */
    public function add(Application $app, $user)
    {
        if (isset($user['username']) && ctype_digit((string) $user['username'])) {
            throw new CustomUserMessageAuthenticationException(
                sprintf('Username "%s" already exists.', $user['username'])
            );
        } else {
            // add new record
            $user['password'] = $app['security.encoder.bcrypt']->encodePassword($user['password'], '');
            $user['FK_role_id'] = 2;
            $user_base = [
                'username' =>$user['username'],
                'password' => $user['password'],
                'FK_role_id' => $user['FK_role_id']];


            $this->db->beginTransaction();
            $this->db->insert('users', $user_base);
            $userId = $this->db->lastInsertId();
            $user_data = [
                'FK_user_id' => $userId,
                'firstname' => $user['firstname'],
                'surname' => $user['surname'],
                'email' => $user['email'],
                'telephone' => $user['telephone'],
                'adress' => $user['adress'],
            ];
            $this->db->insert('users_data', $user_data);

            return $this->db->commit();
        }
    }

    /**
     * Find for uniqueness.
     *
     * @param string          $name Element name
     * @param int|string|null $id   Element id
     *
     * @return array Result
     */
    public function findForUniqueness($name, $id = null)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('user_id', 'username')
            ->from('users')
            ->where('username = :name')
            ->setParameter(':name', $name, \PDO::PARAM_STR);
        if ($id) {
            $queryBuilder->andWhere('user_id <> :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT);
        }

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Finds all users and their data
     * @return array
     */
    public function findAll(){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('u.user_id', 'u.username', 'r.role_id', 'r.role_name',
            'ud.firstname', 'ud.surname', 'ud.telephone', 'ud.email', 'ud.adress', 'ud.information')
            ->from('users', 'u')
            ->innerJoin('u', 'roles', 'r', 'r.role_id = u.FK_role_id')
            ->innerJoin('u', 'users_data', 'ud', 'ud.FK_user_id = u.user_id');

        $result = $queryBuilder->execute()->fetchAll();
        return !$result ? [] : $result;

    }

    /**
     * Finds one user by id
     * @param $id
     * @return mixed
     */
    public function findOneById($id){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('u.user_id', 'u.username', 'r.role_id', 'r.role_name',
            'ud.firstname', 'ud.surname', 'ud.telephone', 'ud.email', 'ud.adress', 'ud.information')
            ->from('users', 'u')
            ->innerJoin('u', 'roles', 'r', 'r.role_id = u.FK_role_id')
            ->innerJoin('u', 'users_data', 'ud', 'ud.FK_user_id = u.user_id')
            ->where('u.user_id = :id')
            ->setParameter(':id', $id);

        $result = $queryBuilder->execute()->fetch();
        return !$result ? [] : $result;

    }

}