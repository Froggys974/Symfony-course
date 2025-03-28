<?php

namespace App\Controller\Media;

use App\Entity\Category;
use App\Entity\Movie;
use App\Repository\CategoryRepository;
use App\Service\SvgService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'app_category_')]
final class CategoryController extends AbstractController
{
    private const MEDIA_PER_PAGE = 15;

    public function __construct(private SvgService $svgService, private CategoryRepository $categoryRepository )
    {
    }

    #[Route('/detail/{idCategory}', name: 'detail')]
    public function categoryDetail(Category $idCategory): Response
    {
        $mediaMovies = $idCategory->getMedia()->filter(fn($media) => $media instanceof Movie);
        $mediaCount = $mediaMovies->count();

        return $this->render('media/category.html.twig', [
            'actualCategory' => $idCategory,
            'allCategories' => $this->categoryRepository->findAll(),
            'mediaMovies' => $mediaMovies->slice(0, self::MEDIA_PER_PAGE),
            'showMoreButton' => $mediaCount > self::MEDIA_PER_PAGE,
        ]);
    }

    #[Route(path: '/discover', name: 'discover')]
    public function discover(): Response
    {
        $categories = $this->categoryRepository->findAll();
        $categoriesWithSvg = [];
        foreach ($categories as $category) {
            $categoriesWithSvg[] = [
                'name' => $category->getName(),
                'id' => $category->getId(),
                'svgPath' => $this->svgService->getSvgTemplate($category->getName()),
            ];
        }

        return $this->render('media/discover.html.twig', [
            'title'=> 'Découvrir',
            'categories' => $categoriesWithSvg,
        ]);
    }
}
