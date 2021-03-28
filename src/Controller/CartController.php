<?php

namespace App\Controller;

use App\UseCase\CartCreator;
use App\UseCase\CartGetter;
use App\UseCase\CartUpdater;
use Exception;
use InvalidArgumentException;
use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\Exception\ResourceNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/v1.0/cart", name="create_cart", methods="POST")
     *
     * @param LoggerInterface $logger
     * @param Request $request
     * @param CartCreator $cartCreator
     *
     * @return JsonResponse
     */
    public function createAction(
        LoggerInterface $logger,
        Request $request,
        CartCreator $cartCreator
    ): JsonResponse {
        try {
            // IPT: TODO: A converter to jsonObject should be implemented and used to avoid having to manipulate it here.
            $cart = json_decode($cartCreator->create($request->getContent()));
            return $this->json($cart, 200);
        } catch(InvalidSchemaException | InvalidArgumentException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 422);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 500);
        }
    }

    /**
     * @Route("/v1.0/cart", name="update_cart", methods="PUT")
     *
     * @param LoggerInterface $logger
     * @param Request $request
     * @param CartUpdater $cartUpdater
     *
     * @return JsonResponse
     */
    public function updateAction(
        LoggerInterface $logger,
        Request $request,
        CartUpdater $cartUpdater
    ): JsonResponse {
        try {
            $cartJson = json_decode($cartUpdater->update($request->getContent()));
            return $this->json($cartJson, 200);
        } catch(InvalidSchemaException | InvalidArgumentException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 422);
        } catch(ResourceNotFoundException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 404);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json([], 500);
        }
    }

    /**
     * @Route("/v1.0/cart/{cartId}", name="get_cart", methods="GET")
     *
     * @param string $cartId
     * @param LoggerInterface $logger
     * @param CartGetter $cartGetter
     *
     * @return JsonResponse
     */
    public function getAction(
        string $cartId,
        LoggerInterface $logger,
        CartGetter $cartGetter): JsonResponse
    {
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
}
