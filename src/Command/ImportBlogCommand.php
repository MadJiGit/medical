<?php

namespace App\Command;

use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-blog',
    description: 'Import hardcoded blog categories and posts into the database',
)]
class ImportBlogCommand extends Command
{
    public function __construct(
        private BlogCategoryRepository $categoryRepository,
        private BlogPostRepository $postRepository,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $categoriesData = [
            ['slug' => 'how-to-use', 'label_bg' => 'Как се използва',  'label_en' => 'How to Use',       'sort_order' => 1],
            ['slug' => 'care',       'label_bg' => 'Грижа и хигиена',  'label_en' => 'Care & Hygiene',    'sort_order' => 2],
            ['slug' => 'life',       'label_bg' => 'Живот с остома',    'label_en' => 'Life with Ostomy',  'sort_order' => 3],
        ];

        $postsData = [
            [
                'slug'       => 'kak-pravilno-da-postavite-stomna-torbichka',
                'category'   => 'how-to-use',
                'date'       => '2025-01-10',
                'title_bg'   => 'Как правилно да поставите стомна торбичка',
                'excerpt_bg' => 'Стъпка по стъпка ръководство за правилното поставяне на стомна торбичка за максимален комфорт и сигурност.',
                'content_bg' => '<p>Правилното поставяне на стомна торбичка е умение, което се усвоява с практика. Следването на правилната последователност от стъпки гарантира комфорт и предотвратява течове.</p><h5>Необходими материали</h5><ul><li>Нова торбичка или основа</li><li>Почистващ спрей или топла вода</li><li>Меко кърпиче</li><li>Ножица за подрязване (ако основата не е предварително изрязана)</li><li>Паста или пръстен за запълване (при нужда)</li></ul><h5>Стъпки</h5><ol><li><strong>Подгответе материалите</strong> — наредете всичко необходимо преди да свалите старата торбичка.</li><li><strong>Свалете старата торбичка внимателно</strong> — придържайте кожата с едната ръка и отлепяйте бавно отгоре надолу.</li><li><strong>Почистете кожата</strong> — измийте с топла вода и подсушете добре. Кожата трябва да е напълно суха преди поставяне.</li><li><strong>Проверете кожата</strong> — огледайте за зачервяване или раздразнение около стомата.</li><li><strong>Изрежете отвора</strong> — отворът трябва да е с 2-3 мм по-голям от стомата.</li><li><strong>Поставете торбичката</strong> — започнете отдолу нагоре, притискайте равномерно към кожата.</li><li><strong>Затоплете с ръка</strong> — задръжте дланта върху основата за 1-2 минути за по-добро залепване.</li></ol><p>При въпроси или затруднения не се колебайте да се свържете с нас чрез контактната форма.</p>',
            ],
            [
                'slug'       => 'vidove-stomni-torbichki',
                'category'   => 'how-to-use',
                'date'       => '2025-01-18',
                'title_bg'   => 'Видове стомни торбички — кой е подходящ за вас?',
                'excerpt_bg' => 'Сравнение между едноделни и двуделни системи, дренируеми и затворени торбички — предимства и недостатъци.',
            ],
            [
                'slug'       => 'kak-da-izmerite-stomata',
                'category'   => 'how-to-use',
                'date'       => '2025-02-03',
                'title_bg'   => 'Как да измерите стомата правилно',
                'excerpt_bg' => 'Точното измерване е ключово за избор на подходяща основа. Научете как да го направите сами у дома.',
            ],
            [
                'slug'       => 'smyana-na-torbichkata-kolko-chesto',
                'category'   => 'how-to-use',
                'date'       => '2025-02-14',
                'title_bg'   => 'Смяна на торбичката — колко често?',
                'excerpt_bg' => 'Честотата на смяна зависи от типа торбичка и индивидуалните нужди. Ето как да намерите своя ритъм.',
            ],
            [
                'slug'       => 'adaptirane-na-osnovata',
                'category'   => 'how-to-use',
                'date'       => '2025-03-01',
                'title_bg'   => 'Адаптиране на основата към формата на корема',
                'excerpt_bg' => 'При неравна кожа около стомата правилното адаптиране на основата предотвратява течове.',
            ],
            [
                'slug'       => 'kak-da-poiskate-bezplatna-mostra',
                'category'   => 'how-to-use',
                'date'       => '2025-03-20',
                'title_bg'   => 'Как да поискате безплатна мостра',
                'excerpt_bg' => 'Всички производители предлагат безплатни мостри. Обясняваме как да се свържете с тях и какво да очаквате.',
            ],
            [
                'slug'       => 'patuvane-s-ostoma',
                'category'   => 'how-to-use',
                'date'       => '2025-04-05',
                'title_bg'   => 'Пътуване с остома — практични съвети',
                'excerpt_bg' => 'Как да се подготвите за пътуване, какво да носите в ръчния багаж и как да се справите в непознати бани.',
            ],
            [
                'slug'       => 'grizha-za-kozha-okolo-stomata',
                'category'   => 'care',
                'date'       => '2025-01-12',
                'title_bg'   => 'Грижа за кожата около стомата',
                'excerpt_bg' => 'Кожата около стомата е чувствителна и изисква специална грижа. Научете кои продукти са подходящи.',
                'content_bg' => '<p>Кожата около стомата (перистомалната кожа) е изложена на постоянен контакт с адхезивни материали и секрети. Правилната грижа е от ключово значение за предотвратяване на усложнения.</p><h5>Защо е важна грижата за кожата?</h5><p>Раздразнената или увредена перистомална кожа затруднява залепването на торбичката, причинява болка и може да доведе до течове. Превенцията е значително по-лесна от лечението.</p><h5>Подходящи продукти</h5><ul><li><strong>Почистващи спрейове</strong> — специално разработени за перистомална кожа, без алкохол</li><li><strong>Защитни филмове</strong> — създават бариера между кожата и адхезива</li><li><strong>Хидроколоидни пасти</strong> — запълват неравности и осигуряват по-добро залепване</li><li><strong>Абсорбиращи пудри</strong> — при мокра или раздразнена кожа</li></ul><h5>Какво да избягвате</h5><ul><li>Кремове и лосиони с масла — нарушават адхезията</li><li>Мокри кърпички с алкохол или парфюм</li><li>Груби движения при почистване</li></ul><h5>Кога да потърсите помощ</h5><p>Ако зачервяването не изчезне след 2-3 смени или се появят рани, консултирайте се с ентеростомен терапевт.</p>',
            ],
            [
                'slug'       => 'kak-da-predotvratite-drazneneto',
                'category'   => 'care',
                'date'       => '2025-01-25',
                'title_bg'   => 'Как да предотвратите дразнене на кожата',
                'excerpt_bg' => 'Червенина и сърбеж около стомата са чести, но предотвратими. Ето проверени методи за защита.',
            ],
            [
                'slug'       => 'pochistvane-i-higiena-pri-smyana',
                'category'   => 'care',
                'date'       => '2025-02-08',
                'title_bg'   => 'Почистване и хигиена при смяна',
                'excerpt_bg' => 'Правилното почистване при всяка смяна е основата на здрава кожа и дълготрайно прилепване на торбичката.',
            ],
            [
                'slug'       => 'kakvi-produkti-ne-tryabva-da-izpolzvate',
                'category'   => 'care',
                'date'       => '2025-02-22',
                'title_bg'   => 'Какви продукти НЕ трябва да използвате',
                'excerpt_bg' => 'Някои кремове, масла и почистващи средства увреждат адхезивния слой. Вижте кои са забранени.',
            ],
            [
                'slug'       => 'grizha-za-stomata-pri-toplo-vreme',
                'category'   => 'care',
                'date'       => '2025-03-10',
                'title_bg'   => 'Грижа за стомата при топло време',
                'excerpt_bg' => 'Лятото носи специфични предизвикателства — изпотяване, слънце и плуване. Как да се справите.',
            ],
            [
                'slug'       => 'dieta-i-hidratatsia',
                'category'   => 'care',
                'date'       => '2025-03-28',
                'title_bg'   => 'Диета и хидратация при остома',
                'excerpt_bg' => 'Храненето влияе пряко на обема и консистенцията на отделянията. Какво да ядете и какво да избягвате.',
            ],
            [
                'slug'       => 'sport-i-fizicheska-aktivnost',
                'category'   => 'care',
                'date'       => '2025-04-15',
                'title_bg'   => 'Спорт и физическа активност с остома',
                'excerpt_bg' => 'Остомата не е пречка за активен живот. Кои спортове са безопасни и как да се предпазите.',
            ],
            [
                'slug'       => 'zhivota-sled-operatsia-pyrvite-sedmitsi',
                'category'   => 'life',
                'date'       => '2025-01-15',
                'title_bg'   => 'Животът след операция — първите седмици',
                'excerpt_bg' => 'Периодът на адаптация е труден, но временен. Споделяме опита на хора, преминали същия път.',
                'content_bg' => '<p>Първите седмици след операцията са едновременно физически и емоционално предизвикателни. Нормално е да изпитвате объркване, тъга или притеснение — това преживяват почти всички.</p><h5>Физическата адаптация</h5><p>Тялото се нуждае от време за възстановяване. През първите 6-8 седмици:</p><ul><li>Стомата може да е по-голяма — размерът й се стабилизира след около 6 седмици</li><li>Изходът може да е нередовен — това е нормално в началото</li><li>Умората е естествена — не се форсирайте</li><li>Болката около разреза намалява постепенно</li></ul><h5>Емоционалната адаптация</h5><p>Много хора описват период на скръб — за загубената "нормалност". Това е здравословна реакция. Важно е да:</p><ul><li>Говорите открито с близките си</li><li>Потърсите подкрепа от хора в подобна ситуация</li><li>Не се изолирате</li></ul><h5>Практични съвети за първите седмици</h5><ol><li>Носете свободни дрехи без колан около стомата</li><li>Дръжте резервни материали винаги под ръка</li><li>Научете смяната на торбичката докато сте в болницата — не тръгвайте без да сте уверени</li><li>Запишете телефона на ентеростомния терапевт</li></ol><p>Помнете: милиони хора по света живеят пълноценно с остома. Адаптацията отнема време, но идва.</p>',
            ],
            [
                'slug'       => 'kak-da-kazhete-na-blizkite',
                'category'   => 'life',
                'date'       => '2025-02-01',
                'title_bg'   => 'Как да кажете на близките си',
                'excerpt_bg' => 'Разговорът с партньора, децата и приятелите може да е труден. Ето как да подходите.',
            ],
            [
                'slug'       => 'vrashane-kam-rabotata',
                'category'   => 'life',
                'date'       => '2025-02-18',
                'title_bg'   => 'Връщане към работата след операция',
                'excerpt_bg' => 'Кога и как да се върнете на работа, какви права имате и как да организирате работния ден.',
            ],
            [
                'slug'       => 'intimnost-i-vzaimootnoshenia',
                'category'   => 'life',
                'date'       => '2025-03-05',
                'title_bg'   => 'Интимност и взаимоотношения с остома',
                'excerpt_bg' => 'Темата е деликатна, но важна. Практични съвети за поддържане на близостта с партньора.',
            ],
            [
                'slug'       => 'psihologicheska-podkrepa',
                'category'   => 'life',
                'date'       => '2025-03-22',
                'title_bg'   => 'Психологическа подкрепа — къде да потърсите помощ',
                'excerpt_bg' => 'Емоционалното справяне е толкова важно, колкото физическото. Групи за подкрепа и специалисти в България.',
            ],
            [
                'slug'       => 'ostoma-i-sotsialen-zhivot',
                'category'   => 'life',
                'date'       => '2025-04-10',
                'title_bg'   => 'Остома и социален живот — без ограничения',
                'excerpt_bg' => 'Ресторанти, театри, пътувания — с правилна подготовка животът продължава пълноценно.',
            ],
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

            $category = new BlogCategory();
            $category->setSlug($data['slug']);
            $category->setLabelBg($data['label_bg']);
            $category->setLabelEn($data['label_en']);
            $category->setSortOrder($data['sort_order']);
            $this->em->persist($category);
            $categoryMap[$data['slug']] = $category;
            $io->text(sprintf('Created category: %s', $data['label_bg']));
        }

        $this->em->flush();

        // Import posts
        $created = 0;
        $skipped = 0;
        foreach ($postsData as $data) {
            $existing = $this->postRepository->findOneBy(['slug' => $data['slug']]);
            if ($existing) {
                $skipped++;
                continue;
            }

            $category = $categoryMap[$data['category']] ?? null;
            if (!$category) {
                $io->warning(sprintf('Unknown category "%s" for post "%s", skipping.', $data['category'], $data['slug']));
                continue;
            }

            $post = new BlogPost();
            $post->setCategory($category);
            $post->setSlug($data['slug']);
            $post->setTitleBg($data['title_bg']);
            $post->setTitleEn($data['title_bg']); // EN placeholder — same as BG until translated
            $post->setExcerptBg($data['excerpt_bg'] ?? null);
            $post->setContentBg($data['content_bg'] ?? null);
            $post->setCreatedAt(new \DateTimeImmutable($data['date']));
            $this->em->persist($post);
            $created++;
        }

        $this->em->flush();

        $io->success(sprintf('Import complete. Created: %d posts, skipped: %d (already exist).', $created, $skipped));

        return Command::SUCCESS;
    }
}
