<?php
/**
 * EditOrder type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class EditOrderType.
 *
 * @package Form
 */
class EditOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'order_price',
            NumberType::class,
            [
                'label' => 'Wartość zamówienia ',
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['editOrder-default']]
                    ),
                    new Assert\Type(
                        [
                            'groups' => ['editOrder-default'],
                            'type' => "float",
                        ]
                    ),
                ],

            ]
        );


        $builder->add(
            'status',
            ChoiceType::class,
            [
                'label' => 'Status ',
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'choices' => $this->prepareStatus(),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['editOrder-default']]
                    ),

                ],
            ]
        );

/*        $builder->add(
            'from',
            DateTimeType::class,
            [
                'label' => 'Od ',
                'required' => true,
                'widget' => 'single_text',

                'input' => 'datetime',
                'attr' => array('class' => 'form-control'),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['editOrder-default']]
                    ),
                ],

            ]
        );
        $builder->add(
            'to',
            DateTimeType::class,
            [
                'label' => 'Do ',
                'required' => true,
                'widget' => 'single_text',

                'input' => 'datetime',
                'attr' => array('class' => 'form-control'),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['editOrder-default']]
                    ),
                ],

            ]
        );*/


    }



    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'editOrder-default',
                'order_repository' => null,

            ]
        );
    }


    public function getBlockPrefix()
    {
        return 'editOrder_type';
    }

    protected function prepareStatus()
    {
            $choices = [
            'Zamówione' => 1,
            'Do odebrania' => 2,
            'Wypożyczone' => 3,
            'Zwrócone' => 4,
            'Anulowane' => 5,
        ];

        return $choices;
    }


}