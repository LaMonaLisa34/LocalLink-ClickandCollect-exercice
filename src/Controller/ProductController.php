<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Product;
use App\Repository\BusinessRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use function Symfony\Component\Clock\now;


class ProductController extends AbstractController
{
    public function __construct(private ParameterBagInterface $params)
    {

    }

    #[Route('home/{businessId}/product/{id}', name: 'app_product')]
    public function index(ProductRepository $productRepository, string $id, string $businessId): Response
    {
        $product = $productRepository->find($id);
        $business = $product->getBusiness();
        $businessId = $business->getId();


        $reviews = $product->getProductReviews();

        $productTotalScore = 0;
        $reviewsCount = count($reviews);

        if ($reviewsCount > 0) {
            foreach ($reviews as $review) {
                $productTotalScore += $review->getScore();
            }
            $productAverageScore = $productTotalScore / $reviewsCount;
        } else {
            $productAverageScore = null;
        }

        $promotions = $business->getPromotions();
        $promotionCount = count($promotions);

        $validPromotions = [];
        if ($promotionCount > 0) {
            foreach ($promotions as $promotion) {
                $beginDate = $promotion->getBeginDate();
                $endDate = $promotion->getEndDate();
                if ($beginDate <= now() && $endDate >= now()) {
                    $validPromotions[] = $promotion;
                }
            }
        }

        $productPrice = $product->getPrice();
        $productPromotionPrice = 0;
        if (count($validPromotions) > 0) {
            foreach ($validPromotions as $validPromotion) {
                $promotion = ($productPrice / 100) * $validPromotion->getReduction();
                $productPromotionPrice = $productPrice - $promotion;
            }
        }
        $productQuantity = 0;
        $productQuantity = $product->getQuantity();

        $userConnected = $this->getUser();

        $userId = null;

        if ($userConnected) {
            $userId = $userConnected->getId();
        }



        return $this->render('product/product.html.twig', [
            'product' => $product,
            'userId' => $userId,
            'productAverageScore' => round($productAverageScore),
            'reviewsCount' => $reviewsCount,
            'productPrice' => $productPrice,
            'productQuantity' => $productQuantity,
            'productPromotionPrice' => $productPromotionPrice,
            'business' => $business,
            'busiNameFile' => str_replace(' ','',$business->getName()),
            'businessId' => $businessId
        ]);
    }

    #[Route('/business/{businessId}/products/addProduct', name: 'add_product')]
    public function addProduct(BusinessRepository $businessRepository, string $businessId): Response
    {
        $business = $businessRepository->find($businessId);
        $businessId = $business->getId();

        //PRODUCTS
        $products = $business->getProducts();

        $productData = [];
        foreach ($products as $product) {
            $productPhotos = $product->getPhotos();

            $photoData = [];
            foreach ($productPhotos as $photo) {
                $photoData[] = [
                    'photoData' => $photo,
                ];
            }
            $productFirstPhoto = null;
            if (!$productPhotos->isEmpty()) {
                $productFirstPhoto = $productPhotos->first()->getPhoto();
            }


            $productReviews = $product->getProductReviews();
            $productTotalScore = 0;
            $productCount = count($productReviews);

            if ($productCount > 0) {
                foreach ($productReviews as $review) {
                    $productTotalScore += $review->getScore();
                }
                $productAverageScore = $productTotalScore / $productCount;
            } else {
                $productAverageScore = null;
            }

            $promotions = $business->getPromotions();
            $promotionCount = count($promotions);

            $validPromotions = [];
            if ($promotionCount > 0) {
                foreach ($promotions as $promotion) {
                    $beginDate = $promotion->getBeginDate();
                    $endDate = $promotion->getEndDate();
                    if ($beginDate <= now() && $endDate >= now()) {
                        $validPromotions[] = $promotion;
                    }
                }
            }

            $productPrice = $product->getPrice();
            $productPromotionPrice = 0;
            if (count($validPromotions) > 0) {
                foreach ($validPromotions as $validPromotion) {
                    $promotion = ($productPrice / 100) * $validPromotion->getReduction();
                    $productPromotionPrice = $productPrice - $promotion;
                }
            }


            $productData[] = [
                'product' => $product,
                'productPrice' => $productPrice,
                'productPhotos' => $productPhotos,
                'productPhoto' => $productFirstPhoto,
                'productPromotionPrice' => $productPromotionPrice,
                'productAverageScore' => round($productAverageScore),
            ];
        }



        return $this->render('product/productList.html.twig', [
            'businessId' => $businessId,
            'products' => $products,
            'productData' => $productData,
            'business' => $business,
            'busiNameFile' => str_replace(' ','',$business->getName()),


        ]);
    }

    #[Route('/business/{businessId}/products/productAdded', name: 'added_product')]
    public function addedProduct(Request $request, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $entityManager, BusinessRepository $businessRepository, string $businessId): Response
    {
        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('added_product', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        $uploadDirCommerce = $this->params->get('uploadsDirectory');
        $business = $businessRepository->find($businessId);
        $businessName = $business->getName();
        $busiNameFile = str_replace(' ','',$businessName);

        $businessId = $business->getId();

        $userConnected = $this->getUser();
        $getall = $request->request->all();
        $product = new Product();
        $product->setName($getall['name']);
        $product->setDescription($getall['description']);


        $product->setPrice($getall['price']);
        $product->setQuantity($getall['quantity']);
        $entityManager->persist($product);
        $business->addProduct($product);
        $entityManager->persist($business);
        $photoAdded = $request->files;

        if (is_null($photoAdded->get('photo1'))) {

            $this->addFlash('error', 'Vous devez ajouter au moins une photo.');
            return $this->redirectToRoute('add_product', ['businessId' => $businessId]);
        } else {

            foreach ($photoAdded as $fileName) {
                if ($fileName) {

                    $photoName = uniqid() . "." . $fileName->guessExtension();

                    // Déplacer le fichier dans le répertoire des uploads/businessName
                    $fileName->move(
                        $this->getParameter('uploadsDirectory') . "/" . $busiNameFile . "/products",
                        $photoName
                    );

                    // Créer une nouvelle entité Photos dans bdd
                    $photo = new Photo();
                    $photo->setPhoto($photoName);
                    $photo->setProduct($product);
                    $product->addPhoto($photo);
                    $entityManager->persist($photo);
                }
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Produit ajouté avec succès !');

        return $this->redirectToRoute('add_product', ['businessId' => $businessId]);
    }

    #[Route('/business/{businessId}/products/editProduct', name: 'edit_product')]
    public function editProduct(Request $request, CsrfTokenManagerInterface $csrfTokenManager, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('edit_product', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        $uploadDirCommerce = $this->params->get('uploadsDirectory');
        $businessName = $request->request->get('name');
        $product = $productRepository->find($request->request->get('id'));
        $business = $product->getBusiness();
        $businessId = $business->getId();
        $businessName = $business->getName();
        $busiNameFile = str_replace(' ','',$businessName);

        $getall = $request->request->all();
        $product->setName($getall['name']);
        $product->setDescription($getall['description']);

        $product->setPrice($getall['price']);
        $product->setQuantity($getall['quantity']);

        $photoAdded = $request->files;
        $productPhotos = $productRepository->find($request->request->get('id'))->getPhotos();
        $photosRequest = $getall['photos'] ?? [];
        if (count($photosRequest) === 0 && $photoAdded->get('photo1') === null) {
            $this->addFlash('error', 'Vous devez conserver au moins une photo.');
            return $this->redirectToRoute('add_product', ['businessId' => $businessId]);
        } else {

            foreach ($productPhotos as $photo) {
                $idPhoto = $photo->getId();
                if (!in_array($idPhoto, $photosRequest)) {
                    $filePath = $uploadDirCommerce . '/' . $busiNameFile . '/products/' . $photo->getPhoto();

                    // Supprimer le fichier du dossier uploads
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    // supprime les photos de la bdd
                    $product->removePhoto($photo);
                    $entityManager->remove($photo);
                    $entityManager->persist($product);
                }

                foreach ($photoAdded as $fileName) {
                    if ($fileName) {
                        $photoName = uniqid() . "." . $fileName->guessExtension();
                        $path = $uploadDirCommerce . "/" . $busiNameFile . "/products";
    
                        // Déplacer le fichier dans le répertoire des uploads/businessName
                        $fileName->move(
                            $path,
                            $photoName
                        );
    
                        // Créer une nouvelle entité Photos dans bdd
                        $photo = new Photo();
                        $photo->setPhoto($photoName);
                        $photo->setProduct($product);
                        $product->addPhoto($photo);
                        $entityManager->persist($photo);
                    }
                }
                
            }

        }

        $entityManager->flush();

        return $this->redirectToRoute('add_product', ['businessId' => $businessId]);
    }


    #[Route('/business/{businessId}/products/deleteProduct', name: 'delete_product')]
    public function deleteProduct(Request $request, CsrfTokenManagerInterface $csrfTokenManager, ProductRepository $productRepository, BusinessRepository $businessRepository, EntityManagerInterface $entityManager, string $businessId): Response
    {
        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_product', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $business = $businessRepository->find($businessId);
        $businessId = $business->getId();
        $selectedProductsIds = $request->request->all('selected_products');
        $businessName = $business->getName();
        $busiNameFile = str_replace(' ','',$businessName);


        if (!empty($selectedProductsIds)) {
            $products = $productRepository->findBy(['id' => $selectedProductsIds]);
            
            foreach ($products as $product) {
                $productPhotos = $product->getPhotos();
                foreach ($productPhotos as $photo) {
                    $filePath = $this->getParameter('uploadsDirectory') . '/' . $busiNameFile . '/products/' . $photo->getPhoto();
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }


                $entityManager->remove($product);
                $business->removeProduct($product);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Les produits sélectionnés ont été supprimés avec succès.');
        } else {
            $this->addFlash('warning', 'Aucun produit sélectionné.');
        }

        return $this->redirectToRoute('add_product', ['businessId' => $businessId]);

    }
}
