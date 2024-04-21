<?php

namespace App\Controller\Admin;

use App\Entity\Experiment;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\Form\FormBuilderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FormFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Form\FormFactoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use App\Form\CustomSectionType;
use App\Form\CustomSectionCollectionType;
use App\Controller\Admin\SubAdmin\ExperimentSectionCrudController;

class ExperimentCrudController extends AbstractCrudController
{

    public function __construct(private AdminUrlGenerator $adminUrlGenerator, private CacheInterface $cache, 
        private EntityManagerInterface $entityManager, private FormFactoryInterface $symfonyFormFactory)
    {
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('description')
            ->add('createdAt')
            ->add('signedAt')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // crud/detail.html.twig
            ->overrideTemplates([
                'crud/new' => 'admin/custom_new.html.twig',
                'crud/edit' => 'admin/custom_edit.html.twig',
                'crud/field/collection' => 'admin/field/experiment_section_collection.html.twig'
            ])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $signAction = Action::new('sign', 'Sign')
            ->linkToCrudAction('sign')
            ->addCssClass('action-sign')
            ->displayIf(static function ($entity) {
                return $entity->getSignedAt() === null;
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $signAction)
            ->add(Crud::PAGE_DETAIL, $signAction)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->displayIf(static function ($entity) {
                    return $entity->getSignedAt() === null;
                });
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(static function ($entity) {
                    return $entity->getSignedAt() === null;
                });
            });
    }
    
    public function edit(AdminContext $context)
    {
        //dump('$context');
        //dd($context);
        $page = parent::edit($context);
        return $page;
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $cacheTitle = 'entity_dto__' . '_clientId-' . $_ENV['CLIENT_ID'] . '_entityId-' . $entityDto->getPrimaryKeyValue();

        // Cache miss, so fetch or compute the data
        $data = $this->container->get(FormFactory::class)->createEditFormBuilder($entityDto, $formOptions, $context);  // Replace this with your actual data fetching or computation logic

        return $data;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $cacheTitle = 'queryBuider__' . '_clientId-' . $_ENV['CLIENT_ID'] . '_entityId-' . $entityDto->getPrimaryKeyValue();
        //$queryBuilder = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $queryBuilderCached = $this->container->get(EntityRepository::class)
            ->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilderCached;
    }

    public function editFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $cacheTitle = 'entity_dto__' . '_clientId-' . $_ENV['CLIENT_ID'] . '_entityId-' . $entityDto->getPrimaryKeyValue();
    
        $cachedFormOptions = $this->cache->get($cacheTitle, function (ItemInterface $item) use ($entityDto, $formOptions, $context) {
            $cssClass = sprintf('ea-%s-form', $context->getCrud()->getCurrentAction());
    
            // Create a new array with the options you want to cache
            $optionsToCache = [
                'attr.class' => trim(($formOptions->get('attr.class') ?? '').' '.$cssClass),
                'attr.id' => sprintf('edit-%s-form', $entityDto->getName()),
                'translation_domain' => $formOptions->get('translation_domain') ?? $context->getI18n()->getTranslationDomain(),
            ];
    
            return $optionsToCache;
        });
    
        // Merge the cached options with the original form options
        $formOptions = array_merge($formOptions->all(), $cachedFormOptions);
    
        return $this->symfonyFormFactory->createNamedBuilder($entityDto->getName(), CrudFormType::class, $entityDto->getInstance(), $formOptions);
    }

    public function sign(AdminContext $context, EntityManagerInterface $entityManager)
    {
        $entity = $context->getEntity()->getInstance();
        $entity->setSignedAt(new \DateTimeImmutable());
        $entity->setSignedBy($this->getUser());
    
        $entityManager->flush();
    
        // Use AdminUrlGenerator to generate a fallback URL
        $fallbackUrl = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX) // or use Action::DETAIL and setEntityId($entity->getId()) for detail page
            ->generateUrl();
    
        // Redirect to the referrer if available, otherwise use the fallback URL
        $redirectUrl = $context->getReferrer() ?? $fallbackUrl;
    
        return $this->redirect($redirectUrl);
    }

    public static function getEntityFqcn(): string
    {
        return Experiment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            FormField::addTab('Experiment Detail'),

            FormField::addColumn(12),
            # add text field for sections
            
            CollectionField::new('sections')
                ->useEntryCrudForm(ExperimentSectionCrudController::class)
                ->hideOnIndex(),
            

            FormField::addTab('Settings'),

            FormField::addColumn(7),
            TextField::new('name')
                ->formatValue(function ($value, $entity) {
                    # add linkto experiment
                    $link = $this->adminUrlGenerator	
                        ->setController(self::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($entity->getId())
                        ->generateUrl();
                    return sprintf('<a href="%s">%s</a>', $link, $value);
                }),
            TextareaField::new('description')
                ->setFormTypeOption('attr', ['class' => 'my-ckeditor-textarea'])
                ->hideOnIndex(),

            FormField::addColumn(5),
            AssociationField::new('protocol'),
            AssociationField::new('study'),
            AssociationField::new('project'),
            AssociationField::new('researchers'),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('signedAt')->hideOnForm(),
        ];

    }

    public function index(AdminContext $context)
    {
        $page = parent::index($context);
        return $page;
    }

    public function detail(AdminContext $context)
    {
        //dd('$context', $context);
        $page = parent::detail($context);
        return $page;
    }
}
