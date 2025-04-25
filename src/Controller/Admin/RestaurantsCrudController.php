<?php

namespace App\Controller\Admin;

use App\Entity\Restaurants;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class RestaurantsCrudController extends AbstractCrudController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Restaurants) return;

        if ($entityInstance->getUser() === null) {
            $entityInstance->setUser($this->security->getUser());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public static function getEntityFqcn(): string
    {
        return Restaurants::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('adresse'),
            TextField::new('cuiqinz'),
            TextareaField::new('description'),
            ImageField::new('imageurl')
                ->setBasePath('uploads/restaurants')
                ->setUploadDir('public/uploads/restaurants')
                ->setSortable(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? '/uploads/restaurants/' . $value : '/uploads/defaults.png';
                }),
            DateTimeField::new('created_at')->hideOnForm(),
            AssociationField::new('user')->hideOnForm(),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->security->getUser();
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($user && in_array('ROLE_RESTAURATEUR', $user->getRoles(), true)) {
            $qb->andWhere('entity.user = :user')
               ->setParameter('user', $user);
        }

        return $qb;
    }
}
