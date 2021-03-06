<?php
/**
 * Category repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
use Utils\Paginator;
/**
 * Class CategoryRepository.
 *
 * @package Repository
 */
class CategoryRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 8;
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;
    /**
     * CategoryRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    /**
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll()
    {
        $queryBuilder = $this->queryAll();
        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Find all records by category
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findAllByCategory($id)
    {
        $queryBuilder = $this->queryAll();
        return $queryBuilder->where('c.category_id = :id')
            ->orWhere('c.parent_id = :id')
            ->setParameter(':id', $id);

    }


    /**
     * Find all products by category paginated
     * @param int $page
     * @param $id
     * @return array
     */
    public function findAllByCategoryPaginated($page = 1, $id)
    {
        $countQueryBuilder = $this->findAllByCategory($id)
                        ->select('COUNT(DISTINCT p.product_id) AS total_results')
                        ->setMaxResults(1);

        $paginator = new Paginator($this->findAllByCategory($id), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Find all records paginated
     * @param int $page
     * @return array
     */
    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll()
                    ->select('COUNT(DISTINCT p.product_id) AS total_results')
                    ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll(), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Query category data
     * @param $id
     * @return array|mixed
     */
    public function queryCategory($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('c.category_name', 'c.parent_id', 'pname.category_name AS parent_name')
            ->from('categories', 'c')
            ->innerJoin('c', 'categories', 'pname', 'c.parent_id = pname.category_id')
            ->where('c.category_id = :id')
            ->setParameter(':id', $id);
        $result = $queryBuilder->execute()->fetch(); //zwraca fałsz gdy parent_id = null
        if ($result == true) {
            return !$result ? [] : $result;
        }

        else {
            $newQueryBuilder = $this->db->createQueryBuilder();
            $newQueryBuilder->select('category_name', 'parent_id', 'parent_id AS parent_name')
                ->from('categories')
                ->where('category_id = :id')
                ->setParameter(':id', $id);
            $result = $newQueryBuilder->execute()->fetch();
            return !$result ? [] : $result;
        }
    }

    /**
     * Query all records
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('p.product_id', 'p.name','p.description','p.price','p.img','p.qty','p.qty_available', 'p.FK_category_id', 'c.category_name', 'c.parent_id', 'pcname.category_name AS parent_name')
            ->from('products', 'p')
            ->innerJoin('p', 'categories', 'c', 'p.FK_category_id = c.category_id')
            ->innerJoin('c', 'categories', 'pcname', 'c.parent_id = pcname.category_id')
            ;
    }


}