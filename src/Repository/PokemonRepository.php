<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Pokemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokemon[]    findAll()
 * @method Pokemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    /**
     * @return Pokemon[] Returns an array of Pokemon objects
    */
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Pokemon
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
      * Récupère une liste de pokemons paginés et triés par date de création.
      *
      * @param int $page Le numéro de la page
      * @param int $nbMaxByPage Nombre maximum de pokemons par page     
      *
      * @throws InvalidArgumentException
      * @throws NotFoundHttpException
      *
      * @return Paginator
      */
    public function findAllPagedAndSorted($page, $nbMaxByPage, $filters = [])
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').'
            );
        }

        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        if (!is_numeric($nbMaxByPage)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $nbMaxByPage . ').'
            );
        }

        /*
            filters = [
                'name' => '%azeaze%',
                'type' => [ 'azeazeaze', 'azeazeaze', 'azeazeaze' ],
                'generation' => [ 'azeazeaze', 'azeazeaze', 'azeazeaze' ],
            ]
        */

        $qb = $this->createQueryBuilder('pokemon');


        $qb->leftJoin('pokemon.generation', 'generation');
        $qb->leftJoin('pokemon.type1', 'type1');
        $qb->leftJoin('pokemon.type2', 'type2');

        if (!isset($filters['generation'])) $filters['generation'] = [];


        if (isset($filters['type1']) AND isset($filters['type2'])) {
            $filters['type'] = array_merge($filters['type1'], $filters['type2']);   
            $filters['type1'];
            $filters['type2'];         
        } else if(isset($filters['type1'])) {
            $filters['type'] = $filters['type1'];
            unset($filters['type1']);
        } else if(isset($filters['type2'])) {
            $filters['type'] = $filters['type2'];
            unset($filters['type2']);
        }

        foreach ($filters as $key => $filter) 
        {
            if (is_array($filter)) {

                $sql = "";
                foreach ($filter as $i => $value) {
                    $sql .= "$key.name = :$key$i OR";
                }

                $sql = substr($sql, 0, strlen($sql) - 3);

                $qb->andWhere($sql);

                foreach ($filter as $i => $value) {
                    $qb->setParameter("$key$i", $value);
                }

            } else {

                $qb->andWhere("pokemon.$key LIKE :$key");
                $qb->setParameter("$key", $filter);

            }
            
        }
        
        $qb->orderBy('pokemon.numero', 'ASC');

        $query = $qb->getQuery();


        $firstResult = ($page - 1) * $nbMaxByPage;
        $query->setFirstResult($firstResult)->setMaxResults($nbMaxByPage);
        $paginator = new Paginator($query);

        if ( ($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
        }

        return $paginator;
    }
}