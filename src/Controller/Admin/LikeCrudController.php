<?php

namespace App\Controller\Admin;

use App\Entity\Like;
use Doctrine\ORM\QueryBuilder; // Assurez-vous que c'est bien Doctrine\ORM\QueryBuilder
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Importation correcte
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Bundle\SecurityBundle\Security;


class LikeCrudController extends AbstractCrudController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Like::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user')
                ->setLabel('Utilisateur')
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getNom() : 'Utilisateur non spécifié';
                })
                ->hideOnForm(),
            DateTimeField::new('createdAt')
                ->setLabel('Date du Like')
                ->setFormat('yyyy-MM-dd HH:mm:ss') 
                ->hideOnForm(),
            AssociationField::new('restaurant')->setLabel('Restaurant')->hideOnForm(),
        ];
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto, 
        EntityDto $entityDto, 
        FieldCollection $fields, 
        FilterCollection $filters
    ): QueryBuilder {
        $user = $this->security->getUser();
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Vérifie si l'utilisateur est admin ou restaurateur
        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // Si l'utilisateur est un admin, on ne filtre pas et on voit tous les likes
                return $qb;
            } elseif (in_array('ROLE_RESTAURATEUR', $user->getRoles(), true)) {
                // Si l'utilisateur est un restaurateur, on filtre par les restaurants associés à cet utilisateur
                $qb->andWhere('entity.restaurant IN (:restaurants)')
                   ->setParameter('restaurants', $user->getRestaurants()) // Liste des restaurants associés à l'utilisateur
                   ->setMaxResults(20); // Limite à 20 résultats
            } else {
                // Si l'utilisateur n'est pas admin ou restaurateur, il ne voit aucun like
                $qb->andWhere('1 = 0');
            }
        }

        return $qb;
    }
}
