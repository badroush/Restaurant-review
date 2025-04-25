<?php

namespace App\Controller\Admin;

use App\Entity\Evaluation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Entity\Restaurants;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Bundle\SecurityBundle\Security;

class EvaluationCrudController extends AbstractCrudController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Evaluation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextareaField::new('commentaire')->setLabel('Commentaire'),
            // Affichage du nom de l'utilisateur au lieu de l'ID
            AssociationField::new('user')
                ->setLabel('Utilisateur')
                ->formatValue(function ($value, $entity) {
                    // Affiche le nom de l'utilisateur plutôt que son ID
                    return $value ? $value->getNom() : 'Utilisateur non spécifié';
                })
                ->hideOnForm(),
            // Affichage de la date de création, en s'assurant que la date n'est pas null
            DateTimeField::new('datePublication')
                ->setLabel('Date du Commentaire')
                ->setFormat('yyyy-MM-dd HH:mm:ss') // Formate la date
                ->hideOnForm(),
            AssociationField::new('restaurant')->setLabel('Restaurant')->hideOnForm(),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->security->getUser();
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Vérifie si l'utilisateur est admin ou restaurateur
        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // Si l'utilisateur est un admin, on ne filtre pas et on voit toutes les évaluations
                return $qb;
            } elseif (in_array('ROLE_RESTAURATEUR', $user->getRoles(), true)) {
                // Si l'utilisateur est un restaurateur, on filtre par les restaurants associés à cet utilisateur
                $qb->andWhere('entity.restaurant IN (:restaurants)')
                   ->setParameter('restaurants', $user->getRestaurants()) // Liste des restaurants associés à l'utilisateur
                   ->setMaxResults(20); // Limite à 20 résultats
            }
        }

        return $qb;
    }
}
