<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordHasher)
    {
        $this->passwordEncoder = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $email = EmailField::new('email');
        $username = TextField::new('username');
        $isVerified = BooleanField::new('isVerified');

        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $email, $username, $isVerified];
        } elseif (Crud::PAGE_NEW === $pageName) {
            $password = TextField::new('plainPassword')->setLabel("Password")
                ->setFormType(PasswordType::class);
            return [$id, $email, $username, $isVerified, $password];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $password = TextField::new('password')->hideOnForm();
            $plainPassword = Field::new('plainPassword', 'New password')->onlyOnForms()
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'New password'],
                    'second_options' => ['label' => 'Repeat password'],
                ]);
            return [$id, $email, $username, $isVerified, $password, $plainPassword];
        } else { //PAGE_INDEX
            return [$id, $email, $username, $isVerified];
        }
    }

    public function persistEntity(EntityManagerInterface $entityManager,$entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    protected function encodePassword(User $user)
    {
        if ($user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        }
    }
}
