<?php
/**
 * Cart repository.
 */
namespace Provider;

use Doctrine\DBAL\Connection;
use Silex\Application;



/**
 * Class CartProvider.
 *
 * @package Provider
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
     * CartProvider constructor.
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


    public function addToCart(Application $app, $id, $qty)
    {

        $cart = $app['session']->get('cart');

        array_push($cart, ['product_id' => $id, 'qty' => $qty]);

        return $app['session']->set('cart', $cart);
        dump($app['session']->get('cart'));

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
        return $app['session']->set('cart', $cart);
    }


}