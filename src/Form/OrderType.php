<?php
/**
 * Order type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class OrderType.
 *
 * @package Form
 */
class OrderType extends AbstractType
{

    /**
     * Build form
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add(
            'days',
            NumberType::class,
            [
                'label' => 'label.days',
                'required' => true,

                'attr' => array('class' => 'form-control'),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['order-default']]
                    ),
                    new Assert\GreaterThan('from'),
                ],

            ]
        );


    }


    /**
     * Configure options
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'order-default',
                'order_repository' => null,

            ]
        );
    }


    /**
     * Get block prefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'editOrder_type';
    }




}