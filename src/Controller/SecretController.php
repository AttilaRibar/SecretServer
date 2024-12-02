<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class SecretController extends AbstractController
{
    #[Route('/secret', name: 'add_secret', methods: ['POST'])]
    #[OA\Post(
        description: 'Save a secret with restrictions on views or expiration time.',
        summary: 'Add a new secret',
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                'application/x-www-form-urlencoded' => new OA\MediaType(
                    mediaType: 'application/x-www-form-urlencoded',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(
                                property: 'secret',
                                description: 'This text will be saved as a secret',
                                type: 'string'
                            ),
                            new OA\Property(
                                property: 'expireAfterViews',
                                description: 'The secret won\'t be available after the given number of views. Must be greater than 0.',
                                type: 'integer',
                                format: 'int32'
                            ),
                            new OA\Property(
                                property: 'expireAfter',
                                description: 'The secret won\'t be available after the given time in minutes. 0 means never expires.',
                                type: 'integer',
                                format: 'int32'
                            )
                        ],
                        type: 'object'
                    )
                )
            ]
        ),
        tags: ['secret'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: [
                    'application/json' => new OA\JsonContent(
                        ref: new Model(type: \App\Entity\Secret::class)
                    ),
                    'application/xml' => new OA\XmlContent(
                        ref: new Model(type: \App\Entity\Secret::class)
                    ),
                ]
            ),
            new OA\Response(
                response: 405,
                description: 'Invalid input'
            )
        ]
    )]
    public function addSecret(): Response
    {
        // Implement the addSecret logic here
    }
}
