<?php

declare(strict_types=1);

namespace App\Controller;

use App\UseCase\CartGetter;
use App\UseCase\CartProduct\CartProductCreator;
use App\UseCase\CartProduct\CartProductDeleter;
use Exception;
use InvalidArgumentException;
use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\Exception\ResourceNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartProductController extends AbstractController
{
    /**
     * @Route("/v1.0/cart/{cartId}/cartProduct/{cartProductId}", name="delete_cartproduct", methods="DELETE")
     *
     * @param string $cartId
     * @param string $cartProductId
     * @param LoggerInterface $logger
     * @param CartProductDeleter $cartDeleter
     *
     * @return JsonResponse
     */
    public function deleteAction(
        string $cartId,
        string $cartProductId,
        LoggerInterface $logger,
        CartProductDeleter $cartDeleter
    ): JsonResponse {
        try {
            $cartDeleter->delete($cartProductId);

            return $this->json([], 200);
        } catch(ResourceNotFoundException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 404);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 500);
        }
    }

    /**
     * @Route("/v1.0/cart/{cartId}/cartProduct", name="create_cartproduct", methods="POST")
     *
     * @param Request $request
     * @param string $cartId
     * @param LoggerInterface $logger
     * @param CartProductCreator $cartCreator
     *
     * @return JsonResponse
     */
    public function createAction(
        Request $request,
        string $cartId,
        LoggerInterface $logger,
        CartProductCreator $cartCreator
    ): JsonResponse {
        try {
            $this->validateCreateCartProductInput($request);
            $cartCreator->create(
                $this->getPostParameter($request, 'quantity'),
                $cartId,
                $this->getPostParameter($request, 'productId')
            );

            return $this->json([], 200);
        } catch(ResourceNotFoundException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 404);
        } catch(InvalidSchemaException | InvalidArgumentException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 422);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 500);
        }
    }

    /**
     * @Route("/v1.0/cart/{cartId}/cartProduct", name="list_cartproduct", methods="GET")
     *
     * @param string $cartId
     * @param LoggerInterface $logger
     * @param CartGetter $cartGetter
     *
     * @return JsonResponse
     */
    public function listAction(
        string $cartId,
        LoggerInterface $logger,
        CartGetter $cartGetter
    ): JsonResponse {
        try {
            $cartJson = json_decode($cartGetter->get($cartId));

            return $this->json($cartJson, 200);
        } catch(ResourceNotFoundException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 404);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 500);
        }
    }

    /**
     * @param Request $request
     * @param string $parameterName
     *
     * @return mixed
     */
    private function getPostParameter(Request $request, string $parameterName)
    {
        $content = json_decode($request->getContent(), true);

        return $content[$parameterName];
    }

    /**
     * @param Request $request
     */
    private function validateCreateCartProductInput(Request $request): void
    {
        $content = json_decode($request->getContent());
        if (!isset($content->quantity) || !is_int($content->quantity)) {
            throw new InvalidArgumentException('A numeric quantity must be sent to add a product to the cart');
        }
        if (!isset($content->quantity)) {
            throw new InvalidArgumentException('A product id must be sent to add a product to the cart');
        }

    }
}
