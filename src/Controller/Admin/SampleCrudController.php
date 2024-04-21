<?php

namespace App\Controller\Admin;

use App\Entity\Sample;
use App\Entity\SampleType;
use App\Entity\SampleSeries;
use App\Entity\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use PhpParser\Node\Expr\Assign;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Traversable;

class SampleCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator, private EntityManagerInterface $entityManager)
    {
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $entityManager = $this->container->get('doctrine')->getManager();
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Assuming 'Sample' entity has a 'createdAt' field to determine the latest sample per series
        // and 'sampleSeries' is a ManyToOne relation in 'Sample'
        $subQb = $entityManager->createQueryBuilder();

        $subQb->select('MAX(s.id)')
            ->from('App\Entity\Sample', 's')
            ->where('s.sampleSeries IS NOT NULL')
            ->groupBy('s.sampleSeries');

        // Main query adjustment to include:
        // 1. Samples with ids that match the subquery (part of a sampleSeries).
        // 2. Samples where sampleSeries is NULL (handled individually).
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->in('entity.id', $subQb->getDQL()),
                $qb->expr()->isNull('entity.sampleSeries')
            )
        );

        return $qb;
    }

    
    public function createEntity(string $entityFqcn) {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $data = $request->request->all();

        if (isset($data['Sample'])) {
            $data = $data['Sample'];
        }

        if (isset($data['createSampleSeries']) && $data['createSampleSeries'] == true) {
            $name = $data['name'];
            $sampleType = $this->entityManager->getRepository(SampleType::class)->findOneBy(['id' => $data['sampleType']]);
            $storage = $this->entityManager->getRepository(Storage::class)->findOneBy(['id' => $data['storage']]);
            $sampleCounts = intval($data['sampleCounts']);

            $samples = [];
            for ($i = 0; $i < $sampleCounts; $i++) {
                $sample = new Sample();
                $sample->setName($name);
                $sample->setSampleType($sampleType);
                $sample->setStorage($storage);

                $this->entityManager->persist($sample);

                $samples[] = $sample;
            }

            $this->entityManager->flush();

            $series = new SampleSeries();
            $series->setName($samples[0]->getName());
            foreach ($samples as $sample) {
                $series->addSample($sample);
            }

            $this->entityManager->persist($series);
            $this->entityManager->flush();

            foreach ($samples as $sample) {
                $sample->setSampleSeries($series);
                $this->entityManager->persist($sample);
            }

            $this->entityManager->flush();

            $redirectPath = '/?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5CSampleSeriesCrudController&entityId=' . $series->getId();

            return $this->json(['url' => $redirectPath]);
        
        } else {
            $sample = new Sample();
            $sample->setBarcode('BRCD-' . uniqid());
            return $sample;
        }
    }

    public static function getEntityFqcn(): string
    {
        return Sample::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('description')
            ->add('barcode')
            ->add('createdAt')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // crud/detail.html.twig
            ->overrideTemplates([
                'crud/index' => 'admin/sample/index.html.twig',
                'crud/edit' => 'admin/custom_edit.html.twig',
                'crud/new' => 'admin/custom_new.html.twig',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s.id', 's.dimensions', 's.positionTaken')
                    ->from('App\Entity\Storage', 's');
        $queryResult = $queryBuilder->getQuery()->getResult();

        $storageData = [];
        $positionTaken = [];
        foreach ($queryResult as $row) {
            $storageData[$row['id']] = $row['dimensions'];
            $positionTaken[$row['id']] = $row['positionTaken'];// eg. ["1", "2"]
        }

        // loop over $storageDataJson if 1 => "Array" then replace it with null
        foreach ($storageData as $key => $value) {
            if ($value === "Array") {
                $storageData[$key] = null;
            }
        }

        $storageDataJson = json_encode($storageData);
        
        $positionTakenJson = json_encode($positionTaken);

        return [
            IdField::new('id')->hideOnForm(), // Auto-generated, so hide on form
            AssociationField::new('sampleSeries', 'Sample Series')
                ->onlyOnIndex(),

            TextField::new('sampleSeries.name', 'Series Count')
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                    if ($value && $entity && $entity->getSampleSeries() && $entity->getSampleSeries()->getSamples()) {
                        $count = count($entity->getSampleSeries()->getSamples());
                    } else {
                        return 'N/A';
                    }
                    
                    return sprintf('%s (<span class="badge badge-secondary">%d samples</span>)', $value, $count);
                }),

            BooleanField::new('createSampleSeries', 'Create Sample Series')->setFormTypeOption('mapped', false),
            TextField::new('name', 'Sample Name')->formatValue(function ($value, $entity) {
                # add linkto experiment
                $link = $this->adminUrlGenerator	
                    ->setController(self::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($entity->getId())
                    ->generateUrl();
                return sprintf('<a href="%s">%s</a>', $link, $value);
            }),
            TextField::new('description', 'Description')
                ->hideOnIndex(), // Optional: Hide on index view
            TextField::new('barcode', 'Barcode'),
            AssociationField::new('sampleType', 'Sample Type')->formatValue(function ($value, $entity) {

                if ($value === null) {
                    // Handle the case where $sampleType is null, maybe by setting default styles or simply returning
                    return 'No Sample Type'; // or any other fallback logic you prefer
                }

                /** @var SampleType */
                $sampleType = $value;
                $fgColor = $sampleType->getFgColor();
                $bgColor = $sampleType->getBgColor();
                // if color set
                $fgColor = $fgColor ? $fgColor : 'black';
                $bgColor = $bgColor ? $bgColor : 'white';
                $title = $sampleType->getName();

                $style = sprintf('<style>
                    tr[data-id="%s"] td[data-column="sampleType"] {
                        color: %s;
                        background-color: %s;
                        padding: 0.5rem;
                        border-radius: 0.5rem;
                    }</style>', $entity->getId(), $fgColor, $bgColor);

                return $style . sprintf('<span style="color: %s; background-color: %s">%s</span>',  $fgColor, $bgColor, $title);
            }),
            TextField::new('barcode', 'Barcode')
                ->hideOnIndex(), // Optional: Hide on index view
            //AssociationField::new('experiment', 'Experiment'),
            AssociationField::new('storage', 'Storage')
                ->setFormTypeOption('attr', ['class' => 'storage-field']),
            TextField::new('sampleCounts', 'Sample Counts')->onlyWhenCreating(),
            HiddenField::new('storageData')
                // mapped false
                ->setFormTypeOptions(['mapped' => false])
                ->setFormTypeOption('attr', ['class' => 'storage-data-field', 
                    'data-storage-values' => $storageDataJson,
                    'data-position-taken' => $positionTakenJson])
                ->hideOnIndex()
                ->hideOnDetail(),
            /*
            TextField::new('parent', 'Parent Storage')
                ->formatValue(function ($value, $entity) {
                    $location = $entity->getStorage() ? $entity->getStorage()->getParent() : null;
                    return $location ? $location->getName() : 'N/A';
                })
                // set mapped false
                ->setFormTypeOptions(['mapped' => false])
                ->setRequired(false)
                ->onlyOnIndex(),
            */
            TextField::new('position', 'Position')
                ->setFormTypeOption('attr', ['class' => 'position-field'])
                ->hideOnIndex(),
            AssociationField::new('user', 'Owner'),
            //AssociationField::new('links', 'Links'), // Optional: Show only on detail view
            DateTimeField::new('createdAt', 'Created At')->setFormTypeOptions(['disabled' => true]), // Disable on form
        ];

    }
}
