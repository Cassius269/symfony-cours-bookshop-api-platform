<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\ArticleDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ArticleAuthorStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de tous les articles présend dans la base de données
        $data = $this->collectionProvider->provide($operation, $uriVariables, $context);

        // Création d'un tableau vide pour stocker les données à rendre à l'interface API
        $response = [];

        foreach ($data as $key => $value) {
            $article = new ArticleDto(); // Création d'un objet ArticleDto
            // Remplissage de l'objet ArticleDto avec les données de l'entité Article
            $article->setTitle($value->getTitle());
            $author = $value->getAuthor() ?? null;

            $article->setAuthor($author ? $author->getFirstname() . ' ' . $author->getLastname() : 'inconnu');

            // Ajout de l'objet ArticleDto dans le tableau $response à rendre à l'interface API
            $response[] = $article;
        }

        return $response;
    }
}
