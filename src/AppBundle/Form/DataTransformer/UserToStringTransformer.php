<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 22/01/16
 * Time: 14:32
 */
namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Translation\Translator;

class UserToStringTransformer implements DataTransformerInterface {
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * IssueToNumberTransformer constructor.
     * @param Registry $registry
     * @param Translator $translator
     */
    public function __construct(Registry $registry, Translator $translator) {
        $this->registry = $registry;
        $this->translator = $translator;
    }

    /**
     * @param mixed $value The value in the original representation
     * @return mixed The value in the transformed representation
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($user) {
        if (!$user) {
            return '';
        }

        /* @var $user User */
        return $user->getUsername();
    }

    /**
     * @param mixed $value The value in the transformed representation
     * @return mixed The value in the original representation
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($username) {
        if (!$username) {
            return null;
        }

        $user = $this->registry->getRepository('AppBundle:User')->findOneBy(array('username' => $username));

        if (!$user) {
            throw new TransformationFailedException($this->translator->trans('user.unknownusername'));
        }

        return $user;
    }
}