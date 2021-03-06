<?php
/**
 * Cart repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Repository\ProductRepository;
use Silex\Application;


/**
 * Class CartRepository
 *
 * @package Repository
 */
class CartRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * CartRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }


    /**
     * Find data for all products in the cart
     * @param Application $app
     * @param $cart
     * @return array
     */
    public function findAllData(Application $app, $cart)
    {
        $productRepository = new ProductRepository($app['db']);
        $cartData = [];

        foreach($cart as $product){
            $id = $product['product_id'];
            $qty = $product['qty'];
            $productData = $productRepository->findOneById($id);
            array_push($cartData, [
                    'product_id' => $id,
                    'name' => $productData['name'],
                    'img' => $productData['img'],
                    'price' => $productData['price'],
                    'qty' => $qty
                ]
            );
        }
        return $cartData;

    }

    /**
     * Count sum for products in the cart
     * @param Application $app
     * @param $cart
     * @return int
     */
    public function countSum(Application $app, $cart){
        $sum = 0;
        $productRepository = new ProductRepository($app['db']);

        foreach($cart as $product){
            $id = $product['product_id'];
            $qty = $product['qty'];
            $productData = $productRepository->findOneById($id);
            $price = $productData['price'];
            $amount = $price * $qty;
            $sum = $sum + $amount;
        }
        return $sum;
    }

    /**
     * Borrow product
     * @param Application $app
     * @param $borrow_data from BorrowType
     * @param $cart
     */
    public function borrow(Application $app, $borrow_data, $cart)
    {
        $userRepository = new UserRepository($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $username = $token->getUser()->getUsername();
        }
        $user_data = $userRepository->getUserByLogin($username);
        $user_id = $user_data['user_id'];
        $order_price = $borrow_data['order_price']*$borrow_data['days'];

        $borrowed = [
            'status' => 1,
            'FK_user_id' => $user_id,
            'order_price' => $order_price,
            'days' => $borrow_data['days'],
        ];

        $this->db->beginTransaction();
        $this->db->insert('borrowed', $borrowed);
        $borrowedId = $this->db->lastInsertId();
        foreach($cart as $product){
            $addProduct = [
                'FK_borrowed_id' => $borrowedId,
                'FK_product_id' => $product['product_id'],
                'qty' => $product['qty'],
            ];
            $this->db->insert('borrowed_data', $addProduct);
        }

        return $this->db->commit();
    }


}