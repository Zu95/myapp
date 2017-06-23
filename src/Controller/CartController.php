<?php
/**
 * Customer controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;

use Form\AddToCartType;
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

        return $controller;
    }
    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */
    public function indexAction(Application $app) //funkcja renderuje widok wszystkich zamowień
    {
        $categories = new Categories($app['db']);
        $cartRepository = new CartRepository($app['db']);
        $cartProvider = new CartProvider($app['db']);
        $cart = $cartProvider->findAll($app);
        dump($cartProvider->findAll($app));

        return $app['twig']->render(
            'cart/index.html.twig',
            [
                'cart' => $cartRepository->findAllData($app, $cart),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }

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

            dump($app['session']->get('cart'));
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.successfully_added',
                ]
            );
            /*return $app->redirect($app['url_generator']->generate('cart_index'), 301);*/
        }
        /*return $app->redirect($app['url_generator']->generate(('cart_index')), 301);*/
        return $app['twig']->render(
            'cart/index.html.twig',
            [
                'cart' => $cartRepository->findAllData($app, $cart),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }



}