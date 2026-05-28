<?php

namespace App\Command;

use App\Entity\Video;
use App\Entity\VideoCategory;
use App\Repository\VideoCategoryRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-videos',
    description: 'Import sample video categories and videos into the database',
)]
class ImportVideosCommand extends Command
{
    public function __construct(
        private VideoCategoryRepository $categoryRepository,
        private VideoRepository $videoRepository,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $categoriesData = [
            ['slug' => 'how-to-use', 'label_bg' => 'Как се използва',  'label_en' => 'How to Use',      'sort_order' => 1],
            ['slug' => 'care',       'label_bg' => 'Грижа и хигиена',  'label_en' => 'Care & Hygiene',   'sort_order' => 2],
            ['slug' => 'life',       'label_bg' => 'Живот с остома',    'label_en' => 'Life with Ostomy', 'sort_order' => 3],
        ];

        $videosData = [
            ['category' => 'how-to-use', 'title_bg' => 'Какво представляват стомните торбички?', 'title_en' => 'What are ostomy bags?',           'source_type' => 'youtube', 'source_id' => 'jNQXAC9IVRw', 'sort_order' => 1],
            ['category' => 'how-to-use', 'title_bg' => 'Как се поставя стомна торбичка',          'title_en' => 'How to apply an ostomy bag',      'source_type' => 'youtube', 'source_id' => 'jNQXAC9IVRw', 'sort_order' => 2],
            ['category' => 'how-to-use', 'title_bg' => 'Избор на правилния продукт',              'title_en' => 'Choosing the right product',      'source_type' => 'youtube', 'source_id' => 'jNQXAC9IVRw', 'sort_order' => 3],
            ['category' => 'care',       'title_bg' => 'Грижа за стомата у дома',                 'title_en' => 'Ostomy care at home',             'source_type' => 'youtube', 'source_id' => 'jNQXAC9IVRw', 'sort_order' => 1],
            ['category' => 'care',       'title_bg' => 'Съвети за ежедневна хигиена',             'title_en' => 'Daily hygiene tips',              'source_type' => 'youtube', 'source_id' => 'jNQXAC9IVRw', 'sort_order' => 2],
            ['category' => 'life',       'title_bg' => 'Живот след операция',                     'title_en' => 'Life after surgery',              'source_type' => 'youtube', 'source_id' => 'jNQXAC9IVRw', 'sort_order' => 1],
            ['category' => 'life',       'title_bg' => 'Как да поискате безплатна мостра',        'title_en' => 'How to request a free sample',    'source_type' => 'youtube', 'source_id' => 'jNQXAC9IVRw', 'sort_order' => 2],
        ];

        // Import categories
        $categoryMap = [];
        foreach ($categoriesData as $data) {
            $existing = $this->categoryRepository->findOneBy(['slug' => $data['slug']]);
            if ($existing) {
                $io->note(sprintf('Category "%s" already exists, skipping.', $data['slug']));
                $categoryMap[$data['slug']] = $existing;
                continue;
            }

            $category = new VideoCategory();
            $category->setSlug($data['slug']);
            $category->setLabelBg($data['label_bg']);
            $category->setLabelEn($data['label_en']);
            $category->setSortOrder($data['sort_order']);
            $this->em->persist($category);
            $categoryMap[$data['slug']] = $category;
            $io->text(sprintf('Created category: %s', $data['label_bg']));
        }

        $this->em->flush();

        // Import videos
        $created = 0;
        foreach ($videosData as $data) {
            $category = $categoryMap[$data['category']] ?? null;
            if (!$category) {
                $io->warning(sprintf('Unknown category "%s", skipping.', $data['category']));
                continue;
            }

            $video = new Video();
            $video->setCategory($category);
            $video->setTitleBg($data['title_bg']);
            $video->setTitleEn($data['title_en']);
            $video->setSourceType($data['source_type']);
            $video->setSourceId($data['source_id']);
            $video->setSortOrder($data['sort_order']);
            $this->em->persist($video);
            $created++;
        }

        $this->em->flush();

        $io->success(sprintf('Import complete. Created: %d videos.', $created));

        return Command::SUCCESS;
    }
}
