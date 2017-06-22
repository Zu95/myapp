<?php
/**
 * Admin controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;
use Form\EditOrderType;
use Symfony\Component\HttpFoundation\Request;
use Repository\OrderRepository;
use Repository\UserRepository;
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
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
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

    public function orderViewAction(Application $app, $id, Request $request) //edycja jednego zamówienia
    {
        $orderRepository = new OrderRepository($app['db']);
        $categories = new Categories($app['db']);
        $order = $orderRepository->findOneById($id);
        if (!$order) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('admin_order_index'));
        }

        $form = $app['form.factory']->createBuilder(
            EditOrderType::class,
            $order)->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order  = $form->getData();
            $orderRepository = new OrderRepository($app['db']);
            $orderRepository->save($order);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('admin_order_index'), 301);
        }

        return $app['twig']->render(
            'admin/orderView.html.twig',
            [   'order_data' => $orderRepository->findDataById($id),
                'order' => $orderRepository->findOneById($id),
                'form' => $form->createView(),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }

    public function userAction(Application $app) //funkcja renderuje widok wszystkich userów
    {
        $userRepository = new UserRepository($app['db']);
        $categories = new Categories($app['db']);


        return $app['twig']->render(
            'admin/user.html.twig',
            [   'users' => $userRepository->findAll(),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }



}