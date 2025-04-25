<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FileField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            // Tes autres champs
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('nom'),
            TextField::new('password')
                ->setFormTypeOption('mapped', false) // Ne pas mapper ce champ à l'entité
                ->setRequired($pageName === 'new') // Requis uniquement lors de la création
                ->setHelp('Laissez vide pour ne pas changer le mot de passe'),
                //  champ d'imortaion image de profile
                ImageField::new('profilePicture')
                ->setBasePath('uploads/photoprofile/')  // Chemin public pour afficher
                ->setUploadDir('public/uploads/photoprofile/')  // Dossier d'upload
                ->setSortable(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? '/uploads/photoprofile/' . $value : '/uploads/photoprofile/default.png';
                }),
            // Affichage ou modification des rôles
            ChoiceField::new('roles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Restaurateur' => 'ROLE_RESTAURATEUR',
                ])
                ->allowMultipleChoices()
                ->renderExpanded()  // Affiche des cases à cocher
                ->setRequired(true),
        ];
    }
    
}
