<?php
/**
 * Order type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'from',
            DateTimeType::class,
            [
                'label' => 'label.from',
                'required' => true,
                'widget' => 'choice',

                'input' => 'datetime',
                'attr' => array('class' => 'form-control'),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['order-default']]
                    ),
                    new Assert\GreaterThan("today"),
                ],

            ]
        );
        $builder->add(
            'to',
            DateTimeType::class,
            [
                'label' => 'label.to',
                'required' => true,
                'widget' => 'choice',

                'input' => 'datetime',
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
     * {@inheritdoc}
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
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'editOrder_type';
    }




}