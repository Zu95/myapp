<?php
/**
 * Category controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\CategoryRepository;
use Utils\Categories;


class CategoryController implements ControllerProviderInterface
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
            ->bind('category_index'); //przeglądanie wszystkich, pierwsza strona
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->value('page', 1)
            ->bind('category_index_paginated');
        $controller->get('/{id}', [$this, 'viewAction']) //przeglądanie kategorii, pierwsza strona
            ->bind('category_view');
        $controller->get('/{id}/page/{page}', [$this, 'viewAction'])
            ->value('page', 1)
            ->bind('category_view_paginated');

        return $controller;
    }
    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */
    public function indexAction(Application $app, $page = 1) //funkcja renderuje widok wszystkich produktów
    {
        $categoryRepository = new CategoryRepository($app['db']);
        $categories = new Categories($app['db']);
        dump($app['session']->get('cart'));
        return $app['twig']->render(
            'category/index.html.twig',
            ['paginator' => $categoryRepository->findAllPaginated($page),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }
    public function viewAction(Application $app, $id, $page = 1) //funkcja renderuje produkty z danej kategorii
    {
        $categoryRepository = new CategoryRepository($app['db']);
        $categories = new Categories($app['db']);

        return $app['twig']->render(
            'category/view.html.twig',
            ['paginator' => $categoryRepository->findAllByCategoryPaginated($page, $id),
                'current_category' => $id,
                'category_data' => $categoryRepository->queryCategory($id),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
        ]
        );

    }



}