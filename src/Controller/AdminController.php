<?php
/**
 * Admin controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;
use Repository\OrderRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Utils\Categories;


class AdminController implements ControllerProviderInterface
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
            ->bind('admin_index');
        $controller->get('/users', [$this, 'userAction'])
            ->bind('admin_user_index');
        $controller->get('/users/{id}', [$this, 'editUserAction']) //przeglądanie kategorii, pierwsza strona
            ->bind('admin_user_edit');
        $controller->get('/order', [$this, 'orderAction'])
            ->bind('admin_order_index');
        $controller->get('/order/{id}', [$this, 'orderViewAction'])
            ->bind('admin_order_view');

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

        return $app['twig']->render(
            'admin/index.html.twig',
            [
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }

    public function orderAction(Application $app) //funkcja renderuje widok wszystkich zamowień
    {
        $orderRepository = new OrderRepository($app['db']);
        $categories = new Categories($app['db']);

        return $app['twig']->render(
            'admin/order.html.twig',
            [   'orders' => $orderRepository->findAll(),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }



}