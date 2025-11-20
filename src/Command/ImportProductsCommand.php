<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import-products',
    description: 'Import products from JSON file to database',
)]
class ImportProductsCommand extends Command
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProductRepository $productRepository,
        private ParameterBagInterface $params
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Load JSON file
        $jsonPath = $this->params->get('kernel.project_dir') . '/config/products.json';

        if (!file_exists($jsonPath)) {
            $io->error('products.json file not found!');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!$data) {
            $io->error('Failed to parse products.json!');
            return Command::FAILURE;
        }

        $io->section('Importing Categories');

        $categoryMap = [];
        foreach ($data['categories'] as $categoryData) {
            $category = new Category();
            $category->setSlug($categoryData['slug']);
            $category->setNameBg($categoryData['name_bg']);
            $category->setNameEn($categoryData['name_en']);
            $category->setIsActive(true);

            $this->categoryRepository->save($category);
            $categoryMap[$categoryData['id']] = $category;

            $io->writeln("✓ Created category: {$categoryData['name_bg']} / {$categoryData['name_en']}");
        }

        $this->categoryRepository->save(null, true); // Flush categories

        $io->section('Importing Products');

        foreach ($data['products'] as $productData) {
            $product = new Product();

            // Get category
            $categoryId = $productData['category_id'];
            if (!isset($categoryMap[$categoryId])) {
                $io->warning("Category ID {$categoryId} not found for product {$productData['name_bg']}");
                continue;
            }

            $product->setCategory($categoryMap[$categoryId]);
            $product->setSlug($productData['slug']);
            $product->setNameBg($productData['name_bg']);
            $product->setNameEn($productData['name_en']);
            $product->setShortDescriptionBg($productData['short_description_bg'] ?? null);
            $product->setShortDescriptionEn($productData['short_description_en'] ?? null);
            $product->setDescriptionBg($productData['description_bg'] ?? null);
            $product->setDescriptionEn($productData['description_en'] ?? null);
            $product->setFeaturesBg($productData['features_bg'] ?? []);
            $product->setFeaturesEn($productData['features_en'] ?? []);
            $product->setSuitableForBg($productData['suitable_for_bg'] ?? null);
            $product->setSuitableForEn($productData['suitable_for_en'] ?? null);
            $product->setHowToUseBg($productData['how_to_use_bg'] ?? null);
            $product->setHowToUseEn($productData['how_to_use_en'] ?? null);
            $product->setImage($productData['image'] ?? null);
            $product->setManufacturer($productData['manufacturer'] ?? null);
            $product->setNhifCode($productData['nhif_code'] ?? null);
            $product->setQuantity($productData['quantity'] ?? 0);
            $product->setIsActive($productData['is_active'] ?? true);

            $this->productRepository->save($product);

            $io->writeln("✓ Created product: {$productData['name_bg']} / {$productData['name_en']}");
        }

        $this->productRepository->save(null, true); // Flush products

        $io->success('Products imported successfully!');
        $io->info('Categories: ' . count($data['categories']));
        $io->info('Products: ' . count($data['products']));

        return Command::SUCCESS;
    }
}
