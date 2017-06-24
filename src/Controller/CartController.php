<?php
/**
 * Customer controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;

use Form\AddToCartType;
use Form\OrderType;
use Repository\CartRepository;
use Symfony\Component\HttpFoundation\Request;
use Provider\CartProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Utils\Categories;


class CartController implements ControllerProviderInterface
{
    /**
     * Routing settings.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Silex\ControllerCollection Result
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])
            ->method('GET|POST')
            ->bind('cart_index');
        $controller->get('/add/{id}', [$this, 'addToCartAction'])
            ->method('GET|POST')
            ->bind('cart_add');
        $controller->get('/delete/{id}', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->bind('cart_delete');
        $controller->get('/clear', [$this, 'clearAction'])
            ->method('GET|POST')
            ->bind('cart_clear');
        $controller->get('/order', [$this, 'orderAction'])
            ->method('GET|POST')
            ->bind('cart_order');


        return $controller;
    }
    /**
     * Index action.
     * Order form
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */
    public function indexAction(Application $app, Request $request) //funkcja renderuje widok wszystkich zamowień
    {
        $categories = new Categories($app['db']);
        $cartRepository = new CartRepository($app['db']);
        $cartProvider = new CartProvider($app['db']);
        $cart = $cartProvider->findAll($app);
        $form = $app['form.factory']->createBuilder(
            OrderType::class,
            $cart)->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $borrow_data = $form->getData();
            $sum = $cartRepository->countSum($app, $cart);
            $order_data = [
                'days' => $borrow_data['days'],
                'order_price' => $sum,
            ];
            $cartRepository->borrow($app, $order_data, $cart);
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.order_successfully_made',
                ]
            );

            return $app->redirect($app['url_generator']->generate('customer_order_index'), 301);
        }


        return $app['twig']->render(
            'cart/index.html.twig',
            [
                'form' => $form->createView(),
                'cart' => $cartRepository->findAllData($app, $cart),
                'sum' => $cartRepository->countSum($app, $cart),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }



    /**
     * Add to cart action
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCartAction(Application $app, $id, Request $request)
    {
        $form = $app['form.factory']->createBuilder(
            AddToCartType::class)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $qty = $product['qty'];
            $cartProvider = new CartProvider($app['db']);
            $cartProvider->addToCart($app, $id, $qty);
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.successfully_added',
                ]
            );
            return $app->redirect($app['url_generator']->generate('cart_index'), 301);
        }
        return $app->redirect($app['url_generator']->generate(('cart_index')), 301);
    }

    /**
     * Remove one item from cart
     * @param Application $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Application $app, $id){
        $cartProvider = new CartProvider($app['db']);
        $cartProvider->delete($app, $id);
        $app['session']->getFlashBag()->add(
            'messages',
            [
                'type' => 'success',
                'message' => 'message.successfully_deleted',
            ]
        );
        return $app->redirect($app['url_generator']->generate(('cart_index')), 301);
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