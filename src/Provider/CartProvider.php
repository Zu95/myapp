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
     * Get array cart from session
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
     * Add record to session array cart
     * @param Application $app
     * @param $id
     * @param $qty
     * @return mixed
     */
    public function addToCart(Application $app, $id, $qty)
    {
        if($app['session']->get('cart') == null) {
            $app['session']->set('cart', []);
        }
        $cart = $app['session']->get('cart');
        array_push($cart, ['product_id' => $id, 'qty' => $qty]);
        return $app['session']->set('cart', $cart);
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

        foreach($cart as $key => $product){
            if($product['product_id'] == $id){
                unset($cart[$key]);
                break;
            }
        }

        return $app['session']->set('cart', $cart);
    }

    /**
     * Clear cart
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearAction(Application $app)
    {
        $app['session']->remove('cart');

        $app['session']->getFlashBag()->add(
            'messages',
            [
                'type' => 'success',
                'message' => 'message.cart_cleared',
            ]
        );

        return $app->redirect($app['url_generator']->generate('cart_index'));
    }



}