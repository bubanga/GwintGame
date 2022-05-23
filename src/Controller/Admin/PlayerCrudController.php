<?php

namespace App\Controller\Admin;

use App\Entity\Deck;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PhpParser\Node\NullableType;

class PlayerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Player::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('username'),
            AssociationField::new('deck')->hideOnForm(),
            BooleanField::new('searchStatus')
        ];
    }


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Player) {
            $deck = new Deck();
            $entityInstance->setDeck($deck);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
