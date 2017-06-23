<?php
/**
 * Register form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Validator\Constraints as CustomAssert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegisterType
 *
 * @package Form
 */
class RegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            TextType::class,
            [
                'label' => 'label.username',
                'required' => true,
                'attr' => [
                    'max_length' => 32,
                    'class' => 'form-control',

                ],
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'groups' => ['login-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['login-default'],
                            'min' => 5,
                            'max' => 32,
                        ]
                    ),
                    new CustomAssert\UniqueUsername(
                        [
                            'groups' => ['login-default'],
                            'repository' => isset($options['user_repository']) ? $options['user_repository'] : null,
                            'elementId' => isset($options['data']['id']) ? $options['data']['id'] : null,
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'password',
            PasswordType::class,
            [
                'label' => 'label.password',
                'required' => true,
                'attr' => [
                    'max_length' => 32,
                    'class' => 'form-control',

                ],
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
        $builder->add(
            'firstname',
            TextType::class,
            [
                'label' => 'label.firstname',
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                    'class' => 'form-control',

                ],
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'groups' => ['login-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['login-default'],
                            'min' => 2,
                            'max' => 45,
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'surname',
            TextType::class,
            [
                'label' => 'label.surname',
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                    'class' => 'form-control',

                ],
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'groups' => ['login-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['login-default'],
                            'min' => 2,
                            'max' => 45,
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => 'label.email',
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                    'class' => 'form-control',

                ],
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'groups' => ['login-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['login-default'],
                            'min' => 2,
                            'max' => 45,
                        ]
                    ),
                    new Assert\Email(
                        [
                            'groups' => ['login-default'],
                            'message' => "message.not_email"
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'telephone',
            TextType::class,
            [
                'label' => 'label.telephone',
                'required' => true,
                'attr' => [
                    'max_length' => 9,
                    'class' => 'form-control',

                ],
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'groups' => ['login-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['login-default'],
                            'min' => 9,
                            'max' => 9,
                        ]
                    ),
                    new Assert\Type(
                        [
                            'groups' => ['login-default'],
                            'type' => 'numeric',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
                    'adress',
                    TextType::class,
                    [
                        'label' => 'label.adress',
                        'required' => true,
                        'attr' => [
                            'max_length' => 100,
                            'class' => 'form-control',

                        ],
                        'constraints' => [
                            new Assert\NotBlank(
                                [
                                    'groups' => ['login-default'],
                                ]
                            ),
                            new Assert\Length(
                                [
                                    'groups' => ['login-default'],
                                    'min' => 5,
                                    'max' => 100,
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