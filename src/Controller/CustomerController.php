<?php
/**
 * Customer controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;
use Form\ChangePasswordType;
use Form\CustomerDataType;
use Symfony\Component\HttpFoundation\Request;
use Repository\OrderRepository;
use Repository\UserRepository;
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
        $controller->get('/account', [$this, 'userAction'])
            ->bind('customer_account_index');
        $controller->get('/password', [$this, 'passwordAction'])
            ->method('GET|POST')
            ->bind('customer_password_index');
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


    public function userAction(Application $app, Request $request) //funkcja edytuje usera
    {

        $userRepository = new UserRepository($app['db']);
        $categories = new Categories($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $username = $token->getUser()->getUsername();
        }
        $user_data = $userRepository->getUserByLogin($username);
        $id = $user_data['user_id'];
        $user = $userRepository->findOneById($id);
        if (!$user) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('customer_index'));
        }

        $form = $app['form.factory']->createBuilder(
            CustomerDataType::class,
            $user)->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user  = $form->getData();
            $userRepository = new UserRepository($app['db']);
            $userRepository->save($user);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('customer_index'), 301);
        }

        return $app['twig']->render(
            'customer/userView.html.twig',
            [   'user' => $userRepository->findOneById($id),
                'form' => $form->createView(),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }

    public function passwordAction(Application $app, Request $request) //funkcja renderuje widok wszystkich zamowień
    {
        $userRepository = new UserRepository($app['db']);
        $categories = new Categories($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $username = $token->getUser()->getUsername();
        }
        $user_data = $userRepository->getUserByLogin($username);
        $id = $user_data['user_id'];
        $user = $userRepository->findOneById($id);

        $form = $app['form.factory']->createBuilder(
            ChangePasswordType::class,
            $user)->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user  = $form->getData();
            $userRepository = new UserRepository($app['db']);
            $userRepository->changePassword($app, $user);
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.password_successfully_changed',
                ]
            );
            return $app->redirect($app['url_generator']->generate('customer_index'), 301);
        }

        return $app['twig']->render(
            'customer/changePassword.html.twig',
            [   'user' => $userRepository->findOneById($id),
                'form' => $form->CreateView(),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }


}