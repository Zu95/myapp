<?php
/**
 * Product repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;


/**
 * Class ProductRepository.
 *
 * @package Repository
 */
class ProductRepository
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
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('p.product_id', 'p.name','p.description','p.price','p.img','p.qty','p.qty_available', 'p.FK_category_id', 'c.category_name', 'c.parent_id', 'pcname.category_name AS parent_name')
            ->from('products', 'p')
            ->innerJoin('p', 'categories', 'c', 'p.FK_category_id = c.category_id')
            ->innerJoin('c', 'categories', 'pcname', 'c.parent_id = pcname.category_id')
            ->where('p.product_id = :id')
            ->setParameter(':id', $id);
        $result = $queryBuilder->execute()->fetch();
        return !$result ? [] : $result;


    }

    public function save($product)
    {
        if (isset($product['product_id']) && ctype_digit((string) $product['product_id'])) {
            // update record
            $id = $product['product_id'];
            unset($product['product_id']);

            $oldData = $this->findOneById($id);
            $oldQty = $oldData['qty'];
            $qty_available = $product['qty_available'] - $oldQty + $product['qty'];

            if($product['img'] == null){
                $product['img'] = $oldData['img'];
            }


            $product_data = [
                'name' => $product['name'],
                'description' => $product['description'],
                'img' => $product['img'],
                'price' => $product['price'],
                'qty' => $product['qty'],
                'qty_available' => $qty_available,
                'FK_category_id' => $product['FK_category_id']

            ];

            return $this->db->update('products', $product_data, ['product_id' => $id]);
        }
        else {
            // add new record

            return $this->db->insert('products', $product);
        }
    }
    /**
     * Remove record.
     *
     * @param array $product PRoduct
     *
     * @return boolean Result
     */
    public function delete($product)
    {
        return $this->db->delete('products', ['product_id' => $product['product_id']]);
    }


}