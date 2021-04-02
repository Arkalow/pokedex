<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\Type;
use App\Entity\Generation;
use App\Form\PokemonType;
use App\Repository\PokemonRepository;
use App\Repository\TypeRepository;
use App\Repository\GenerationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class PokemonController extends AbstractController
{
    /**
      * Liste les pokemons triés par date de création pour une page.
      *
      * @Route("/{page}", requirements={"page" = "\d+"}, name="pokemon_index", methods={"GET", "POST"})
      *
      * @param int $page Le numéro de la page (par defaut 1)
      *
      */
    public function index(int $page = 1, PokemonRepository $pokemonRepository, TypeRepository $typeRepository, GenerationRepository $generationRepository): Response
    {

        $nbPokemonByPage = $this->getParameter('NB_POKEMON_BY_PAGE');

        $name = null;
        $type = null;
        $generation = null;
        $legendaire = null;

        if (isset($_POST['search'])) 
        {
            // Validation du formulaire de recherche
            if (isset($_POST['name'])) $name = trim($_POST['name']);
            if (isset($_POST['type'])) $type = trim($_POST['type']);
            if (isset($_POST['generation'])) $generation = trim($_POST['generation']);
            if (isset($_POST['legendaire'])) {
                $legendaire = ($_POST['legendaire'] == "1") ? true : false;
            }
        }

        $pokemons = $pokemonRepository->findAllPagedAndSorted($page, $nbPokemonByPage, $name, $type, $generation, $legendaire);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($pokemons) / $nbPokemonByPage),
            'nomRoute' => 'pokemon_index',
            'paramsRoute' => array()
        );

        $types = $typeRepository->findBy([], ['name' => 'ASC']);
        $generations = $generationRepository->findBy([], ['name' => 'ASC']);

        return $this->render('pokemon/index.html.twig', [
            'pokemons' => $pokemons,
            'pagination' => $pagination,
            'types' => $types,
            'generations' => $generations,
            'filter' => [
                'name' => $name,
                'type' => $type,
                'generation' => $generation,
                'legendaire' => $legendaire,
            ],
        ]);
    }

    /**
     * @Route("/new", name="pokemon_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pokemon);
            $entityManager->flush();

            return $this->redirectToRoute('pokemon_index');
        }

        return $this->render('pokemon/new.html.twig', [
            'pokemon' => $pokemon,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="pokemon_show", methods={"GET"})
     */
    public function show(Pokemon $pokemon): Response
    {
        return $this->render('pokemon/show.html.twig', [
            'pokemon' => $pokemon,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pokemon_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pokemon $pokemon): Response
    {
        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pokemon_index');
        }

        return $this->render('pokemon/edit.html.twig', [
            'pokemon' => $pokemon,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pokemon_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Pokemon $pokemon): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pokemon->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pokemon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pokemon_index');
    }
}
