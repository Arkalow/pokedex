<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Pokemon;
use App\Entity\Generation;
use App\Entity\Type;
use App\Repository\PokemonRepository;
use App\Repository\TypeRepository;
use App\Repository\GenerationRepository;
use Doctrine\ORM\EntityManagerInterface;

class Import extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'import:pokemon';

    protected $entityManager;
    protected $pokemonRepository;
    protected $typeRepository;
    protected $generationRepository;

    public function __construct(EntityManagerInterface $entityManager, PokemonRepository $pokemonRepository, GenerationRepository $generationRepository, TypeRepository $typeRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->pokemonRepository = $pokemonRepository;
        $this->typeRepository = $typeRepository;
        $this->generationRepository = $generationRepository;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('insert data in database')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to import data.')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $lines = explode("\n", file_get_contents('https://gist.githubusercontent.com/armgilles/194bcff35001e7eb53a2a8b441e8b2c6/raw/92200bc0a673d5ce2110aaad4544ed6c4010f687/pokemon.csv'));

        $nbPokemon = count($lines);

        if ($nbPokemon < 1) 
        {
            $output->writeln('Aucun pokemon disponible.');
            return 0;
        }

        $output->writeln([
            "Il y a $nbPokemon(s) disponible.",
            '=============================================',
            '',
        ]);

        unset($lines[0]);

        $error = 0; // number of error
        foreach ($lines as $key => $line)
        {
            $data = explode(',', $line);

            if (!isset($data[11])) continue;

            if ($this->pokemonRepository->findOneBy(['numero' => $data[0]]) != null) continue;

            $generation = $this->generationRepository->findOneBy(['name' => $data[11]]);
            if ($generation == null) {
                $generation = new Generation();
                $generation->setName($data[11]);
            }
            $type1 = $this->typeRepository->findOneBy(['name' => $data[2]]);
            if ($type1 == null) {
                $type1 = new Type();
                $type1->setName($data[2]);
            }
            $type2 = $this->typeRepository->findOneBy(['name' => $data[3]]);
            if ($type2 == null) {
                $type2 = new Type();
                $type2->setName($data[3]);
            }

            $pokemon = new Pokemon();
            $pokemon->setNumero($data[0]);
            $pokemon->setNom($data[1]);
            $pokemon->setType1($type1);
            $pokemon->setType2($type2);
            $pokemon->setVie($data[5]);
            $pokemon->setAttaque($data[6]);
            $pokemon->setDefense($data[7]);
            $pokemon->setLegendaire(($data[12] == 'False') ? false : true );

            $this->entityManager->persist($pokemon);
            $this->entityManager->persist($type2);
            $this->entityManager->persist($type1);
            $this->entityManager->persist($generation);


            $output->writeln($key . ' - ' . $data[1]);
            
        }

        try{
            $this->entityManager->flush();
        } catch(\Exception $e){
            $errorMessage = $e->getMessage();
            dump($errorMessage());
            $error++;
        }

        $output->writeln($nbPokemon - $error . " pokemons(s) add and $error error(s)");

        return 0;
    }
}
