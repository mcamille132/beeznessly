<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;

class ModerationExpertController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_DETAIL === $pageName) {
            $expertiseField = CollectionField::new('expertise', "Expertises");
        } else {
            $expertiseField = AssociationField::new('expertise', "Expertises")->hideOnIndex();
        }

        return [
            AssociationField::new('provider', "Type d'acteur")->hideOnIndex(),
            BooleanField::new('isValidated', "Validation"),
            Field::new('firstname', "Prénom"),
            Field::new('lastname', "Nom"),
            Field::new('email', "Email"),
            Field::new('phone', "Téléphone")->hideOnIndex(),
            Field::new('adress', "Adresse")->hideOnIndex(),
            Field::new('zipcode', "Code postal")->hideOnIndex(),
            Field::new('town', "Ville")->hideOnIndex(),
            Field::new('description', "Description")->hideOnIndex(),
            Field::new('companyName', "Nom de l'entreprise"),
            Field::new('siretNumber', "Numéro de SIRET")->hideOnIndex(),
            $expertiseField,
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $deleteAction = Action::new('Delete', '')
        ->setIcon('fas fa-trash')
        ->linkToCrudAction('deleteAction');
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, $deleteAction)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $response = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $search = $searchDto->getQuery();
        $response->andwhere('entity.isValidated = 0')
        ->andWhere('entity.roles LIKE :roles')
        ->setParameter('roles', '%"' . 'ROLE_EXPERT' . '"%');
        if (isset($search) && !empty($search)) {
            $response->andWhere("entity.lastname LIKE :search 
            OR entity.firstname LIKE :search 
            OR entity.email LIKE :search
            OR entity.companyName LIKE :search
            ")
            ->setParameter('search', '%' . $search . '%');
        }

        return $response;
    }

    public function deleteAction(AdminContext $context, Request $request)
    {
        $id = $context->getRequest()->query->get('entityId');
        $entity = $this->getDoctrine()->getRepository(User::class)->find($id);

        $this->deleteEntity($this->get('doctrine')->getManagerForClass($context->getEntity()->getFqcn()), $entity);
        $this->addFlash('success', 'Expert supprimé');
        // ici modifier la redirection selon ou l'admin doit être redirigé après l'action delete
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
