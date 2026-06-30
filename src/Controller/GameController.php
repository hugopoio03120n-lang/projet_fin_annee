<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\History;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/game')]
final class GameController extends AbstractController
{
    #[Route('/favorites', name: 'app_game_favorites', methods: ['GET'])]
    public function favorites(): Response
    {
        $user = $this->getUser();
        $games = $user->getFavoritesGames();

        return $this->render('game/favorites.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/history', name: 'app_game_history', methods: ['GET'])]
    public function history(): Response
    {
        $user = $this->getUser();
        $histories = $user->getHistories();

        // map histories to games
        $games = $histories->map(function ($history) {
            return $history->getGame();
        });

        return $this->render('game/history.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
    public function show(Game $game, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user) { 
            $histories = $user->getHistories(); 
            $history = $histories->filter(function ($history) use ($game) {
                return $history->getGame()->getId() === $game->getId();
            })->first();    
            if (!$history) {
                $history = new History();
                $history->setUser($user);
                $history->setGame($game);
                $entityManager->persist($history);
            }
            $history->setLastVisit(new \DateTime());
            $entityManager->flush();
        }
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-favorite', name: 'app_game_toggle_favorite', methods: ['GET'])]
    public function toggleFavorite(int $id, EntityManagerInterface $entityManager): Response
    {
        $game = $entityManager->getRepository(Game::class)->find($id);
        $user = $this->getUser();

        if (!$game || !$user) {
            return $this->redirectToRoute('app_game_index');
        }

        if ($user->getFavoritesGames()->contains($game)) {
            $user->removeFavoritesGame($game);
        } else {
            $user->addFavoritesGame($game);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
    }
}
