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
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/api/v1.0/cart", name="create_cart", methods="POST")
     *
     * @SWG\Post(
     *     summary="Creates a Cart, empty or initialized with some CartProducts",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          description="Info for the Cart to be created. May have an emtpy list of CartProducts or some of them to initialize the Cart",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/NewCartRequest"
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Cart info.",
     *     @SWG\Schema(
     *         type="object",
     *         ref="#/definitions/CartResponse"
     *     )
     * )
     *
     * @SWG\Response(
     *     response=422,
     *     description="Returns status 422 if there is some invalid input data."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Returns status 500 if internal error."
     * )
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
            $cart = $cartCreator->create($request->getContent());

            return $this->json($cart, 200);
        } catch(InvalidSchemaException | InvalidArgumentException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 422);
        } catch(ResourceNotFoundException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 404);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/v1.0/cart", name="update_cart", methods="PUT")
     *
     * @SWG\Put(
     *     summary="Updates a Cart providing a JSON with all its info",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          description="Info for the Cart to be update.",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/ExistingCartRequest"
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Cart info.",
     *     @SWG\Schema(
     *         type="object",
     *         ref="#/definitions/CartResponse"
     *     )
     * )
     *
     * @SWG\Response(
     *     response=422,
     *     description="Returns status 422 if there is some invalid input data."
     * )
     *
     *
     * @SWG\Response(
     *      response=404,
     *      description="Returns status 404 if there is no Product with received id."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Returns status 500 if internal error."
     * )
     *
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
            $cartJson = $cartUpdater->update($request->getContent());

            return $this->json($cartJson, 200);
        } catch(InvalidSchemaException | InvalidArgumentException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 422);
        } catch(ResourceNotFoundException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 404);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/v1.0/cart/{cartId}", name="get_cart", methods="GET")
     *
     * @SWG\Get(
     *     summary="Shows the Cart details",
     *     produces={"application/json"},
     *     @SWG\Parameter(name="cartId", in="path", required=true, type="string", description="The id of the Cart to retrieve the CartProducts from."),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns the Cart info.",
     *         @SWG\Schema(
     *             type="object",
     *             ref="#/definitions/CartResponse"
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response=404,
     *         description="Returns status 404 if there is no CartProduct with given id."
     *     )
     * )
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
            $cartJson = $cartGetter->get($cartId);

            return $this->json($cartJson, 200);
        } catch(ResourceNotFoundException $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 404);
        } catch(Exception $e) {
            $logger->error($e->getMessage(), $e->getTrace());

            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
