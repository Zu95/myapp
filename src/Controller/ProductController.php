<?php
/**
 * Product controller.
 *
 * @copyright (c) 2017 Zuzanna Krzysztofik
 */
namespace Controller;
use Form\AddToCartType;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\ProductRepository;
use Utils\Categories;
use Form\ProductType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Service\FileUploader;


/**
 * Class ProductController.
 *
 * @package Controller
 */
class ProductController implements ControllerProviderInterface
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
        $controller->get('/', [$this, 'categoryAction'])->bind('category_index'); //robię przekierowanie na CategoryController
        $controller->get('/{id}', [$this, 'indexAction'])->assert('id', '[1-9]\d*')->bind('product_view');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('product_add');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('product_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('product_delete');

        return $controller;
    }

    /**
     * Index action
     * View one product by id
     * @param Application $app
     * @param $id
     * @return mixed
     */
    public function indexAction(Application $app, $id)
    {
        $productRepository = new ProductRepository($app['db']);
        $product = [];
        $form = $app['form.factory']->createBuilder(
            AddToCartType::class,
            $product
        )->getForm();
        $categories = new Categories($app['db']);

        return $app['twig']->render(
            'product/index.html.twig',
            ['product' => $productRepository->findOneById($id),
                'form' => $form->createView(),
                'current_product' => $id,
                'category_data' => $categories->findOneByProduct($id),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)]
        );
    }


    /**
     * Redirect to category_index
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function categoryAction(Application $app)
    {
        return $app->redirect($app['url_generator']->generate('category_index'));
    }

    /**
     * Add new product
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Application $app, Request $request)
    {
        $product = [];
        $categories = new Categories($app['db']);

           $form = $app['form.factory']->createBuilder(
            ProductType::class,
            $product,
            ['category_repository' => new Categories($app['db'])]
        )->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product  = $form->getData();
            if(isset($form['img'])) {
                $fileUploader = new FileUploader($app['config.photos_directory']);
                $fileName = $fileUploader->upload($product['img']);
                $product['img'] = $fileName;
            }
            $productRepository = new ProductRepository($app['db']);
            $productRepository->save($product);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('category_index'), 301);
        }

        return $app['twig']->render(
            'product/add.html.twig',
            [
                'product' => $product,
                'form' => $form->createView(),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]

        );
    }


    /**
     * Edit one product
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Application $app, $id, Request $request)
    {
        $productRepository = new ProductRepository($app['db']);
        $categories = new Categories($app['db']);
        $product = $productRepository->findOneById($id);
        $img = $product['img'];
        unset($product['img']);

        if (!$product) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('category_index'));
        }

        $form = $app['form.factory']->createBuilder(
            ProductType::class,
                $product,
                ['category_repository' => new Categories($app['db'])]
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product  = $form->getData();
            if(isset($product['img'])) {
                $fileUploader = new FileUploader($app['config.photos_directory']);
                $fileName = $fileUploader->upload($product['img']);
                $product['img'] = $fileName;
            }
            $productRepository = new ProductRepository($app['db']);
            $productRepository->save($product);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('category_index'), 301);
        }

        return $app['twig']->render(
            'product/edit.html.twig',
            [
                'product' => $product,
                'form' => $form->createView(),
                'product_img' => $img,
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }

    /**
     * Delete product
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Application $app, $id, Request $request)
    {
        $productRepository = new ProductRepository($app['db']);
        $categories = new Categories($app['db']);
        $product = $productRepository->findOneById($id);

        if (!$product) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('category_index'));
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $product)->add('id', HiddenType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->delete($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('category_index'),
                301
            );
        }

        return $app['twig']->render(
            'product/delete.html.twig',
            [
                'product' => $product,
                'form' => $form->createView(),
                'climbing' => $categories->findAllByParent(1),
                'winter' => $categories->findAllByParent(2),
                'skitouring' => $categories->findAllByParent(3),
                'camping' => $categories->findAllByParent(4)
            ]
        );
    }
}