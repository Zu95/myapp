<?php
/**
 * Register form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ChangePasswordType
 *
 * @package Form
 */
class ChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('password', RepeatedType::class,[

                'type' => PasswordType::class,
                'first_options'  => array('label' => 'label.new_password'),
                'second_options' => array('label' => 'label.repeat_password',),
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'options' => array('attr' => array(
                    'max_length' => 32,
                    'class' => 'form-control')),

                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'groups' => ['login-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['login-default'],
                            'min' => 8,
                            'max' => 32,
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'register_type';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'login-default',
                'user_repository' => null,
            ]
        );
    }
}