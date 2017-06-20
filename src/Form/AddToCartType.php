<?php
/**
 * Product type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class AddToCartType.
 *
 * @package Form
 */
class AddToCartType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'qty',
            IntegerType::class,
            [
                'required' => true,
                'attr' => [
                    'max_length' => 11,
                    'class' => 'form-control',
                    'value' => '1'
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['add-to-cart-default']]
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
                'validation_groups' => 'add-to-cart-default',

            ]
        );
    }


    public function getBlockPrefix()
    {
        return 'add_to_cart_type';
    }


}