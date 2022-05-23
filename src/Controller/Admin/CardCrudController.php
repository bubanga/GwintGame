<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Service\Game\Card\AbstractCard;
use App\Service\Game\Engine;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Card::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name')
            ->setMaxLength(255)
            ->setColumns(6)
        ;

        yield TextareaField::new('description')
            ->setColumns(6)
        ;

        yield ChoiceField::new('unitType')
            ->setChoices([
                'SWORD' => 1,
                'BOW' => 2,
                'CATAPULT' => 4,
                'SWORD/BOW' => 3,
                'WEATHER' => 8,
                'COMMANDER' => 9
            ])
            ->setColumns(3)
        ;

        yield ChoiceField::new('fraction')
            ->setChoices([
                'UNIVERSAL' => Engine::DECK_UNIVERSAL,
                'KINGDOM NORTH' => Engine::DECK_KINGDOM_NORTH,
                'MONSTER' => Engine::DECK_MONSTER,
                'SKELLIGE' => Engine::DECK_SKELLIGE,
                'SCOIA\'TAEL' => Engine::DECK_SCOIATAEL,
            ])
            ->setColumns(3)
        ;

        yield ChoiceField::new('power')
            ->setChoices(function () {
                $r = [];
                for ($i = 0; $i <= 15; $i++)
                    $r[] = $i;

                return $r;
            })

            ->setColumns(3)
        ;
        yield ChoiceField::new('skill')
            ->setChoices(function (): array {
                $result = [];
                foreach (AbstractCard::TYPE_CARD as $id => $class) {
                    $bits = explode('\\', $class);
                    $result[end($bits)] = $id;
                }

                return $result;
            })
            ->setColumns(4)
        ;

        yield BooleanField::new('special')
            ->setColumns(2);



    }
}
