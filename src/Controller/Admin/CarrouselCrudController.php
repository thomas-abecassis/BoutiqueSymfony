<?php

namespace App\Controller\Admin;

use App\Entity\Carrousel;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CarrouselCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carrousel::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextareaField::new('content'),
            TextField::new('btnTitle'),
            TextField::new('btnUrl'),
            ImageField::new("illustration")->setUploadDir('public/uploads')->setBasePath('uploads')
                ->setUploadedFileNamePattern('[randomhash],[extension]')
        ];
    }
}
