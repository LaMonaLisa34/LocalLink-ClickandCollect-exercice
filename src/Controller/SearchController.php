<?php

namespace App\Controller;


use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, CategoriesRepository $categoriesRepository): Response
    {
        // Recuperer requete du user
        $query = $request->query->get('query', '');

        // si requete vide
        if (empty(trim($query))) {
            return $this->render('search/index.html.twig', [
                'items' => [],
                'query' => $query,
                'message' => 'Veuillez entrer un mot-clé pour effectuer une recherche.',
            ]);
        }

        // recherche dans la fonction du repository
        $categories = $categoriesRepository->findByQuery($query);
        // dd($categories);
        $items = [];

        // pour chaque category recuperer commerce associé et verification que le commerce soit validés
        foreach ($categories as $category) {
            $validBusinesses = $category->getBusiness()->filter(function ($business) {
                return $business->isValidated();
            });

            // dd($validBusinesses);

            // Photos du commerce -> a ajuster apres gestion uploads photos
            $businessesWithPhotos = [];
            $businessFirstPhoto = null;
            foreach ($validBusinesses as $business) {

                // Récupérer la première photo du commerce
                if (!$business->getBusinessPhotos()->isEmpty()) {
                    $businessFirstPhoto = $business->getBusinessPhotos()->first()->getPhoto();
                    $products = $business->getProducts();
                }
                $productList = [];

                foreach ($products as $product) {
                    if ($product !== null) {
                        $photo = $product->getPhotos() ? $product->getPhotos()->first()->getPhoto() : 'pasContent';

                        $productList[] = [
                            'product' => $product,
                            // 'busiNameFile' => str_replace(' ', '', $business->getName()),
                            'productFirstPhoto' => $photo,
                        ];
                    }
                }

                $businessesWithPhotos[] = [
                    'business' => $business,
                    'busiNameFile' => str_replace(' ', '', $business->getName()),
                    'firstPhoto' => $businessFirstPhoto,
                    'productList' => $productList
                ];
            }
            
            $items[] = [
                'category' => $category,
                'businesses' => $businessesWithPhotos,
            ];
        }

        // dd($items);

        return $this->render('search/index.html.twig', [
            'items' => $items,
            'query' => $query,
        ]);
    }
}
