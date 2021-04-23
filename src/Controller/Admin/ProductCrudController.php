<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name'),
            AssociationField::new('category'),
            TextField::new('short_description'),
            TextField::new('mainPictureFile')->setFormType(VichImageType::class)->hideOnIndex(),
            ImageField::new('mainPicture')->setBasePath('/uploads/images/')->onlyOnIndex(),
            DateField::new('createdAt')->hideOnForm(),
            MoneyField::new('price')->setCurrency('EUR'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setDefaultSort(['createdAt' => 'DESC']);
    }



}
