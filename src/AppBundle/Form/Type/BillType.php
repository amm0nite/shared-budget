<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 22/01/16
 * Time: 11:58
 */
namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, array('label' => 'bill.name'))
            ->add('description', TextareaType::class, array('label' => 'bill.description'))
            ->add('price', MoneyType::class, array('label' => 'bill.price'))
            ->add('payer', EntityType::class, array(
                'label' => 'bill.payer',
                'choices_as_values' => true,
                'class' => 'AppBundle:User',
                'choices' => $options['members'],
                'choice_label' => 'username'
            ))
            ->add('guests', EntityType::class, array(
                'label' => 'bill.guests',
                'choices_as_values' => true,
                'class' => 'AppBundle:User',
                'choices' => $options['members'],
                'choice_label' => 'username',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('save', SubmitType::class, array('label' => 'bill.save'));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'members' => array()
        ));
    }
}