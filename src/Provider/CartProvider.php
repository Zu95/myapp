<?php
/**
 * Cart repository.
 */
namespace Provider;

use Doctrine\DBAL\Connection;
use Repository\ProductRepository;
use Silex\Application;
use Repository\UserRepository;


/**
 * Class CartProvider.
 *
 * @package Repository
 */
class CartProvider
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
    public function findAll(Application $app)
    {
        return $cart = $app['session']->get('cart');

    }

    /**
     * @param Application $app
     * @return array
     */
    public function findAllData(Application $app)
    {
        $productRepository = new ProductRepository($app['db']);
        $cart = $app['session']->get('cart');
        $cartData = [];

        foreach($cart as $product){
            $id = $product['product_id'];
            $productData = $productRepository->findOneById($id);
            array_push($cartData, [
                'product_id' => $id,
                'name' => $productData['name'],
                'img' => $productData['img'],
                'price' => $productData['price'],
                ]
            );
        }

        return $cartData;

    }

    public function addToCart(Application $app, $id, $qty)
    {
        $cart = $app['session']->get('cart');

        array_push($cart, ['product_id' => $id, 'qty' => $qty]);

        return $app['session']->set('cart', $cart);


    }

    public function borrow(Application $app, $borrow_data)
    {
        $cart = $app['session']->get('cart');
        $userRepository = new UserRepository($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $username = $token->getUser()->getUsername();
        }
        $user_data = $userRepository->getUserByLogin($username);
        $user_id = $user_data['user_id'];
        $borrowed = [
            'FK_user_id' => $user_id,
            'date' => date(),
            'order_price' => $borrow_data['order_price'],
            'from' => $borrow_data['from'],
            'to' => $borrow_data['to'],
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
    /**
     * Remove record from cart.
     *
     * @param array $product PRoduct
     *
     * @return boolean Result
     */
    public function delete(Application $app, $id)
    {
        $cart = $app['session']->get('cart');
        foreach($cart as $product){
            if($product['product_id'] == $id){
                unset($product);
            }
        }
        return true;
    }


}