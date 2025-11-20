<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class ProductService
{
    private string $locale;

    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProductRepository $productRepository
    ) {
        $this->locale = 'bg'; // default
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get all active products
     */
    public function getProducts(): array
    {
        $products = $this->productRepository->findActive();

        return array_map(fn($p) => $this->localizeProduct($p), $products);
    }

    /**
     * Get products grouped by category
     */
    public function getProductsByCategory(): array
    {
        $grouped = $this->productRepository->findGroupedByCategory();
        $result = [];

        foreach ($grouped as $group) {
            $category = $group['category'];
            $result[] = [
                'category' => [
                    'id' => $category->getId(),
                    'slug' => $category->getSlug(),
                    'name' => $this->locale === 'en' ? $category->getNameEn() : $category->getNameBg()
                ],
                'products' => array_map(fn($p) => $this->localizeProduct($p), $group['products'])
            ];
        }

        return $result;
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        $categories = $this->categoryRepository->findActive();

        return array_map(function(Category $category) {
            return [
                'id' => $category->getId(),
                'slug' => $category->getSlug(),
                'name' => $this->locale === 'en' ? $category->getNameEn() : $category->getNameBg()
            ];
        }, $categories);
    }

    /**
     * Get product by ID
     */
    public function getProduct(int $id): ?array
    {
        $product = $this->productRepository->find($id);

        if (!$product || !$product->isActive()) {
            return null;
        }

        return $this->localizeProduct($product);
    }

    /**
     * Get products for dropdown (id => name)
     */
    public function getProductsForDropdown(): array
    {
        $products = $this->productRepository->findActive();
        $result = [];

        foreach ($products as $product) {
            $name = $this->locale === 'en' ? $product->getNameEn() : $product->getNameBg();

            // Add manufacturer to distinguish similar products
            if ($product->getManufacturer()) {
                $name .= ' (' . $product->getManufacturer() . ')';
            }

            $result[$product->getId()] = $name;
        }

        return $result;
    }

    /**
     * Localize product data from entity
     */
    private function localizeProduct(Product $product): array
    {
        $isEn = $this->locale === 'en';

        return [
            'id' => $product->getId(),
            'slug' => $product->getSlug(),
            'category_id' => $product->getCategory()->getId(),
            'name' => $isEn ? $product->getNameEn() : $product->getNameBg(),
            'short_description' => $isEn ? $product->getShortDescriptionEn() : $product->getShortDescriptionBg(),
            'description' => $isEn ? $product->getDescriptionEn() : $product->getDescriptionBg(),
            'features' => $isEn ? $product->getFeaturesEn() : $product->getFeaturesBg(),
            'suitable_for' => $isEn ? $product->getSuitableForEn() : $product->getSuitableForBg(),
            'how_to_use' => $isEn ? $product->getHowToUseEn() : $product->getHowToUseBg(),
            'image' => $product->getImage(),
            'manufacturer' => $product->getManufacturer(),
            'nhif_code' => $product->getNhifCode(),
            'quantity' => $product->getQuantity(),
        ];
    }
}
