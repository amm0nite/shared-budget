<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 21/01/16
 * Time: 16:47
 */
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BudgetType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, array('label' => 'budget.name'))
            ->add('description', TextareaType::class, array('label' => 'budget.description'))
            ->add('save', SubmitType::class, array('label' => 'budget.save'));
    }
}