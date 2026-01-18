<?php

namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:add-categories',
    description: 'Ajoute les catégories de base pour AsalaCraft',
)]
class AddCategoriesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $categoriesNames = ['Poterie', 'Tissage', 'Bijoux', 'Cuir', 'Bois'];

        foreach ($categoriesNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug(strtolower($this->slugger->slug($name)));
            
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        $io->success('Les catégories ont été ajoutées avec succès !');

        return Command::SUCCESS;
    }
}