<?php
/**
 * Register form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EditUserType
 *
 * @package Form
 */
class EditUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

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
                            'groups' => ['edit-user-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['edit-user-default'],
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
                            'groups' => ['edit-user-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['edit-user-default'],
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
                            'groups' => ['edit-user-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['edit-user-default'],
                            'min' => 2,
                            'max' => 45,
                        ]
                    ),
                    new Assert\Email(
                        [
                            'groups' => ['edit-user-default'],
                            'message' => "This is not an email"
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
                            'groups' => ['edit-user-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['edit-user-default'],
                            'min' => 9,
                            'max' => 9,
                        ]
                    ),
                    new Assert\Type(
                        [
                            'groups' => ['edit-user-default'],
                            'type' => 'numeric',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'FK_role_id',
            ChoiceType::class,
            [
                'label' => 'Status ',
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'choices' => $this->prepareStatus(),
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['edit-user-default']]
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
                            'groups' => ['edit-user-default'],
                        ]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['edit-user-default'],
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
        return 'edit_user_type';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'edit-user-default',
                'user_repository' => null,
            ]
        );
    }

    /**
     * @return array
     */
    protected function prepareStatus()
    {
        $choices = [
            'Admin' => 1,
            'User' => 2,
        ];

        return $choices;
    }

}