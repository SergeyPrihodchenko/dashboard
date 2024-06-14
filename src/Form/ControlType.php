<?php

namespace App\Form;

use App\Entity\Site;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ControlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'label' => false,
                'class' => Site::class,
                'choice_label' => 'name',
                'attr' => [
                    'data-action' => 'change->control#selectSite',
                ],
            ])
            ->add('dateStart', DateType::class, [
                'row_attr' => [
                    'class' => 'control-group'
                ],
                'label' => 'Дата начала отбора',
                'required' => false,
                'attr' => [
                    'data-action' => 'change->control#selectDate',
                ]
            ])
            ->add('dateEnd', DateType::class, [
                'row_attr' => [
                    'class' => 'control-group'
                ],
                'label' => 'Дата конца отбора',
                'required' => false,
                'attr' => [
                    'data-action' => 'change->control#selectDate',
                ]
            ])
            ->add('today', ButtonType::class, [
                'label' => 'Сегодня',
                'attr' => [
                    'data-action' => 'click->control#selectProgram',
                    'value' => 0
                ]
            ])
            ->add('yesterday', ButtonType::class, [
                'label' => 'Вчера',
                'attr' => [
                    'data-action' => 'click->control#selectProgram',
                    'value' => -1
                ]
            ])
            ->add('sevenDays', ButtonType::class, [
                'label' => 'За 7 дней',
                'attr' => [
                    'data-action' => 'click->control#selectProgram',
                    'value' => -7
                ]
            ])
            ->add('thirtyDays', ButtonType::class, [
                'label' => 'За 30 дней',
                'attr' => [
                    'data-action' => 'click->control#selectProgram',
                    'value' => -30
                ]
            ])
            ->add('thisMonth', ButtonType::class, [
                'label' => 'В этом месяце',
                'attr' => [
                    'data-action' => 'click->control#selectProgram',
                    'value' => 30
                ]
            ])
            ->add('program', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'data-control-target' => 'form',
            ],
            'method' => 'POST'
        ]);
    }
}
