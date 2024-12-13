<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomGetCollectionProvider implements ProviderInterface
{
    public function __construct(private ArticleRepository $articleRepository, #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')] private ProviderInterface $providerInterface) {}



    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récuperer les données
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        // Mettre en majuscule chaque titre de chaque ressource Article recupéréee
        foreach ($data as $key => $value) {
            $value->setTitle(strtoupper($value->getTitle()));
        }
        return $data;
    }
}
