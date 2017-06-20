<?php
/**
 * Order repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;


/**
 * Class OrderRepository.
 *
 * @package Repository
 */
class OrderRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * OrderRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('bd.FK_borrowed_id', 'bd.FK_product_id', 'bd.product_price', 'bd.qty', 'p.name', 'p.img')
            ->from('borrowed_data', 'bd')
            ->innerJoin('bd', 'products', 'p', 'p.product_id = bd.FK_product_id')
            ->where('bd.FK_borrowed_id = :id')
            ->setParameter(':id', $id);
        $result = $queryBuilder->execute()->fetchAll();
        return !$result ? [] : $result;

    }

    public function findAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('b.borrowed_id', 'b.FK_user_id', 'b.date', 'b.order_price', 'b.status', 'b.from', 'b.to', 'ud.firstname', 'ud.surname')
            ->from('borrowed', 'b')
            ->innerJoin('b', 'users_data', 'ud', 'ud.FK_user_id = b.FK_user_id');
        $result = $queryBuilder->execute()->fetchAll();
        return !$result ? [] : $result;

    }

    public function save($Order)
    {
        if (isset($Order['borrowed_id']) && ctype_digit((string) $Order['borrowed_id'])) {
            // update record
            $id = $Order['borrowed_id'];
            unset($Order['borrowed_id']);


            return $this->db->update('borrowed', $Order, ['borrowed_id' => $id]);
        }
        else {
            // add new record
            return $this->db->insert('borrowed', $Order);
        }
    }
    /**
     * Remove record.
     *
     * @param array $Order Order
     *
     * @return boolean Result
     */
    public function delete($Order)
    {
        return $this->db->delete('borrowed', ['borrowed_id' => $Order['borrowed_id']]);
    }


}