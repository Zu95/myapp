<?php
/**
 * Product type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ProductType.
 *
 * @package Form
 */
class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Nazwa produktu',
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['product-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['product-default'],
                            'min' => 2,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'description',
            TextareaType::class,
            [
                'label' => 'Opis',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'height: 200px',
                ],
                'constraints' => [
                    new Assert\Length(
                        [
                            'groups' => ['product-default'],
                            'min' => 2,
                        ]
                    ),
                ],

            ]
        );
        $builder->add(
            'price',
            NumberType::class,
            [
                'label' => 'Cena w PLN ',
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['product-default']]
                    ),
                    new Assert\Type(
                        [
                            'groups' => ['product-default'],
                            'type' => "float",
                        ]
                    ),
                ],

            ]
        );
        $builder->add(
            'img',
            FileType::class,
            [
                'label' => 'Zdjcie',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Image(
                        [
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/pjpeg',
                                'image/jpeg',
                                'image/pjpeg',
                            ],
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'qty',
            NumberType::class,
            [
                'label' => 'Ilość posiadana ',
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['product-default']]
                    ),
                ],

            ]
        );
        $builder->add(
            'FK_category_id',
            ChoiceType::class,
            [
                'label' => 'Kategoria ',
                'required' => true,
                'attr' => array('class' => 'form-control'),
                /*'placeholder' => 'Wybierz kategorię',*/
                'choices' => $this->prepareCategoriesForChoices($options['category_repository']),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['product-default']]
                    ),

                ],
            ]
        );


    }



    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'product-default',
                'category_repository' => null,

            ]
        );
    }


    public function getBlockPrefix()
    {
        return 'product_type';
    }

    protected function prepareCategoriesForChoices($Categories)
    {
        $categories = $Categories->findAllSub();
        $choices = [];

        foreach ($categories as $category) {
            $choices[$category['category_name']] = $category['category_id'];
        }

        return $choices;
    }


}