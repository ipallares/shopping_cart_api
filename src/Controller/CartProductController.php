<?php

declare(strict_types=1);

namespace App\Controller;

use App\UseCase\CartGetter;
use App\UseCase\CartProduct\CartProductCreator;
use App\UseCase\CartProduct\CartProductDeleter;
use Exception;
use InvalidArgumentException;
use JsonSchema\Exception\InvalidSchemaException;
use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartProductController extends AbstractController
{
    /**
     * @Route("/api/v1.0/cart/{cartId}/cartProduct/{cartProductId}", name="delete_cartproduct", methods="DELETE")
     *
     * @SWG\Delete(
     *     summary="Removes a product from a Cart.",
     *     produces={"application/json"},
     *     @SWG\Parameter(name="cartId", in="path", required=true, type="string", description="The id of the Cart where the CartProduct belongs to."),
     *     @SWG\Parameter(name="cartProductId", in="path", required=true, type="string", description="The id of the CartProduct to be removed"),
     *     @SWG\Response(
     *         response=200,
     *         description="The Cart Product was properly removed from the Cart"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returns status 404 if there is no CartProduct with given id."
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Returns status 404 if there is an internal error."
     *     )
     * )
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
     * @Route("/api/v1.0/cart/{cartId}/cartProduct", name="create_cartproduct", methods="POST")
     *
     * @SWG\Post(
     *     summary="Adds a product in the given quantity to the Cart.",
     *     produces={"application/json"},
     *     @SWG\Parameter(name="cartId", in="path", required=true, type="string", description="The id of the Cart where the CartProduct is going to be added to."),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          description="The quantity of the Product to be added to the Cart",
     *          @SWG\Schema(
     *             type="object",
     *             required={"quantity", "productId"},
     *             @SWG\Property(
     *                 property="quantity",
     *                 type="integer",
     *                 example=3
     *             ),
     *             @SWG\Property(
     *                 property="productId",
     *                 type="string",
     *                 format="uuid",
     *                 example="B896C7C9-5AB8-4BA4-B8E4-68D61AEE3109"
     *             )
     *         )
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="Adds the Product to the Cart.",
     *         @SWG\Schema(
     *             type="object",
     *             ref="#/definitions/ExistingCartRequest"
     *         )
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returns status 404 if there is no CartProduct or Product with given ids."
     *     ),
     *     @SWG\Response(
     *          response=422,
     *          description="Returns status 422 if there is some invalid input data."
     *     ),
     *     @SWG\Response(
     *          response=500,
     *          description="Returns status 500 if internal error."
     *     )
     * )
     *
     * @param Request $request
     * @param string $cartId
     * @param LoggerInterface $logger
     * @param CartProductCreator $cartProductCreator
     *
     * @param CartGetter $cartGetter
     *
     * @return JsonResponse
     */
    public function createAction(
        Request $request,
        string $cartId,
        LoggerInterface $logger,
        CartProductCreator $cartProductCreator,
        CartGetter $cartGetter
    ): JsonResponse {
        try {
            $this->validateCreateCartProductInput($request);
            $cartProductCreator->create(
                $this->getPostParameter($request, 'quantity'),
                $cartId,
                $this->getPostParameter($request, 'productId')
            );

            $cart = json_decode($cartGetter->get($cartId));
            return $this->json($cart, 200);
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
     * @Route("/api/v1.0/cart/{cartId}/cartProduct", name="list_cartproduct", methods="GET")
     *
     * @SWG\Get(
     *     summary="Shows the CartProducts in a Cart",
     *     produces={"application/json"},
     *     @SWG\Parameter(name="cartId", in="path", required=true, type="string", description="The id of the Cart to retrieve the CartProducts from."),
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Cart info.",
     *     @SWG\Schema(
     *         type="object",
     *         ref="#/definitions/ExistingCartRequest"
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returns status 404 if there is no CartProduct with given id."
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Returns status 500 if internal error."
     * )
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
