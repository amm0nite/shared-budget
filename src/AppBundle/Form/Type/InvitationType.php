<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 21/01/16
 * Time: 16:47
 */
namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\UserToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Translation\Translator;

class InvitationType extends AbstractType {
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * InvitationType constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry, Translator $translator) {
        $this->registry = $registry;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('target', TextType::class, array(
                'label' => 'invitation.target',
                'invalid_message' => $this->translator->trans('user.unknownusername'))
            )
            ->add('save', SubmitType::class, array('label' => 'invitation.send'));
        $builder->get('target')->addModelTransformer(new UserToStringTransformer($this->registry, $this->translator));
    }
}