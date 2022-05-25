<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new("name"),
            BooleanField::new("isBest"),
            SlugField::new("slug")->setTargetFieldName("name"),
            ImageField::new("image")->setUploadDir('public/uploads')->setBasePath('uploads')
                ->setUploadedFileNamePattern('[randomhash],[extension]'),
            TextField::new("subtitle"),
            TextareaField::new("description"),
            MoneyField::new("price")->setCurrency('EUR'),
            AssociationField::new("category")
        ];
    }
}
