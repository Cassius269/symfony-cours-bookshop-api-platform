<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ArticleRepository;

class CustomGetCollectionProvider implements ProviderInterface
{
    public function __construct(private ArticleRepository $articleRepository) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->articleRepository->findOneByTitle("L'Importance de l'Hydratation");
    }
}
