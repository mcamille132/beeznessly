<?php

namespace App\Controller\Admin;

use App\Entity\Ebook;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;

class ModerationEbookController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ebook::class;
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

    public function configureFields(string $pageName): iterable
    {
        return [
            BooleanField::new('isValidated', "Validation"),
            Field::new('title', "Intitulé"),
            Field::new('description', "Description"),
            Field::new('releaseDate', "Date de sortie"),
            Field::new('editorName', "Nom de l'éditeur"),
            Field::new('author', "Auteur"),
            AssociationField::new('expertise', "Catégorie")->hideOnIndex(),
            AssociationField::new('user', "Expert")
        ];
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $response = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $search = $searchDto->getQuery();
        $response->andwhere('entity.isValidated = 0');
        if (isset($search) && !empty($search)) {
            $response->andWhere("entity.title LIKE :search 
            OR entity.description LIKE :search 
            OR entity.editorName LIKE :search
            OR entity.author LIKE :search
            ")
            ->setParameter('search', '%' . $search . '%');
        }
        return $response;
    }

    public function deleteAction(AdminContext $context, Request $request)
    {
        $id = $context->getRequest()->query->get('entityId');
        $entity = $this->getDoctrine()->getRepository(Ebook::class)->find($id);

        $this->deleteEntity($this->get('doctrine')->getManagerForClass($context->getEntity()->getFqcn()), $entity);
        $this->addFlash('success', 'Ebook supprimé');
        // ici modifier la redirection selon ou l'admin doit être redirigé après l'action delete
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
