<?php
/**
 * Customer controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;
use Symfony\Component\HttpFoundation\Request;
use Repository\OrderRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Utils\Categories;


class CustomerController implements ControllerProviderInterface
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
            ->bind('customer_index');
        $controller->get('/user', [$this, 'userAction'])
            ->bind('customer_user_index');
        $controller->get('/order', [$this, 'orderAction'])
            ->bind('customer_order_index');
        $controller->get('/order/{id}', [$this, 'orderViewAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('customer_order_view');

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
            'customer/index.html.twig',
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
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $username = $token->getUser()->getUsername();
        }

        return $app['twig']->render(
            'customer/order.html.twig',
            [   'orders' => $orderRepository->findAllByUsername($username),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }

    public function orderViewAction(Application $app, $id) //podgląd jednego zamówienia
    {
        $orderRepository = new OrderRepository($app['db']);
        $categories = new Categories($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $username = $token->getUser()->getUsername();
        }
        //else wyrzuć błąd!

        $order = $orderRepository->findOneByUserById($id, $username);

        if (!$order) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('customer_order_index'));
        }


        return $app['twig']->render(
            'customer/orderView.html.twig',
            [   'order_data' => $orderRepository->findDataByUserById($id, $username),
                'order' => $order,
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }



}