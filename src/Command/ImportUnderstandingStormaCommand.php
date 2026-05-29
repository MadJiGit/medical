<?php

namespace App\Command;

use App\Entity\BlogCategory;
use App\Entity\BlogCategoryTranslation;
use App\Entity\BlogPost;
use App\Entity\BlogPostTranslation;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-understanding-stoma',
    description: 'Import "All About the Stoma" category and articles (idempotent)',
)]
class ImportUnderstandingStormaCommand extends Command
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

        $category = $this->categoryRepository->findOneBy(['slug' => 'understanding-stoma']);

        if (!$category) {
            $category = new BlogCategory();
            $category->setSlug('understanding-stoma');
            $category->setSortOrder(5);
            $category->setIsActive(true);
            $this->em->persist($category);

            $bgTrans = new BlogCategoryTranslation();
            $bgTrans->setCategory($category);
            $bgTrans->setLocale('bg');
            $bgTrans->setLabel('Всичко за стомата');
            $this->em->persist($bgTrans);

            $enTrans = new BlogCategoryTranslation();
            $enTrans->setCategory($category);
            $enTrans->setLocale('en');
            $enTrans->setLabel('Understanding Stomas');
            $this->em->persist($enTrans);

            $this->em->flush();
            $io->text('Created category: Всичко за стомата / Understanding Stomas');
        } else {
            $io->text('Category already exists, skipping creation.');
        }

        foreach ($this->getArticles() as $data) {
            $post = $this->postRepository->findOneBy(['slug' => $data['slug']]);

            if (!$post) {
                $post = new BlogPost();
                $post->setSlug($data['slug']);
                $post->setCategory($category);
                $post->setIsActive(true);
                $post->setCreatedAt(new \DateTimeImmutable($data['date']));
                $this->em->persist($post);
            }

            $post->setTitleBg($data['title_bg']);
            $post->setExcerptBg($data['excerpt_bg']);
            $post->setContentBg($data['content_bg']);
            $post->setTitleEn($data['title_en']);
            $post->setExcerptEn($data['excerpt_en']);
            $post->setContentEn($data['content_en']);

            $io->text(sprintf('Imported: %s', $data['title_bg']));
        }

        $this->em->flush();
        $io->success('All "Understanding Stomas" articles imported successfully.');

        return Command::SUCCESS;
    }

    private function getArticles(): array
    {
        return [
            [
                'slug'       => 'podgotovka-predi-operatsia',
                'date'       => '2025-05-01',
                'title_bg'   => 'Подготовка преди операцията',
                'excerpt_bg' => 'Какво да очаквате преди операцията за стома — видове стоми, среща с ентеростомен терапевт, маркиране на място и какво да вземете в болницата.',
                'content_bg' => '<p>Независимо дали операцията за стома е планирана отдавна или е спешна, нормално е да се чувствате притеснени. Добрата подготовка намалява несигурността и ви помага да навлезете в процеса с по-ясна представа за това, което предстои.</p>

<h5>Видове стоми</h5>
<p>Стомата е частта от червото, изведена на повърхността на коремната стена чрез малък хирургичен отвор. Съществуват три основни вида:</p>
<ul>
<li><strong>Колостома</strong> — формира се от дебелото черво. Торбичката се сменя веднъж или два пъти дневно. Отделянията са по-оформени.</li>
<li><strong>Илеостома</strong> — формира се от тънкото черво. Торбичката се изпразва четири до шест пъти дневно и се сменя на всеки един до три дни. Отделянията са течни или пастообразни.</li>
<li><strong>Урострома</strong> — свързва бъбреците с тънкото черво за отвеждане на урина. Торбичката се изпразва до шест пъти дневно.</li>
</ul>

<h5>Среща с ентеростомен терапевт</h5>
<p>Преди операцията ще се срещнете с ентеростомен терапевт — специализирана медицинска сестра, обучена в грижата за стоми. Тази среща е важна: терапевтът обяснява какво да очаквате, отговаря на въпросите ви и ви запознава с различните видове торбички чрез практически примери. Не се притеснявайте да задавате въпроси — колкото повече знаете предварително, толкова по-уверени ще се чувствате след операцията.</p>

<h5>Маркиране на місто за стомата</h5>
<p>Един от най-важните елементи на предоперативната подготовка е маркирането на оптималното място за стомата върху корема. Ентеростомният терапевт определя това место внимателно — вземат се предвид кожни гънки, белези, костни издатини и начинът, по който седите, стоите и се навеждате. Правилно избраното място значително улеснява поставянето на торбичката и намалява риска от изтичане. Мястото се маркира с мастило преди операцията.</p>

<h5>Какво да вземете в болницата</h5>
<p>Престоят в болницата след операция за стома е обикновено между три и четиринадесет дни. Препоръчително е да вземете:</p>
<ul>
<li>Свободни дрехи — без ластик или колан на нивото на стомата</li>
<li>Удобни домашни дрехи и обувки</li>
<li>Тоалетни принадлежности и хигиенни продукти</li>
<li>Нещо за четене или забавление</li>
<li>Телефон и зарядно устройство</li>
</ul>

<h5>Възстановяване след изписване</h5>
<p>Пълното възстановяване отнема около осем седмици след изписването от болницата. През това време ентеростомните терапевти осигуряват редовни проверки — по телефон, онлайн или на живо — за да оценят зарастването и да отговорят на въпроси. Не тръгвайте от болницата, преди да се чувствате уверени в смяната на торбичката самостоятелно.</p>',
                'title_en'   => 'Preparing for Stoma Surgery',
                'excerpt_en' => 'What to expect before stoma surgery — types of stoma, meeting your stoma care nurse, site marking, and what to pack for hospital.',
                'content_en' => '<p>Whether your stoma surgery is long-planned or arrives as an emergency, it is normal to feel anxious. Good preparation reduces uncertainty and helps you go into the process with a clearer picture of what lies ahead.</p>

<h5>Types of stoma</h5>
<p>A stoma is the part of the bowel brought to the surface of the abdominal wall through a small surgically formed opening. There are three main types:</p>
<ul>
<li><strong>Colostomy</strong> — formed from the large bowel. The pouch is changed once or twice daily. Output is more formed.</li>
<li><strong>Ileostomy</strong> — formed from the small bowel. The pouch is emptied four to six times daily and replaced every one to three days. Output is liquid or paste-like.</li>
<li><strong>Urostomy</strong> — connects the kidneys to the small bowel to divert urine. The pouch is emptied up to six times daily.</li>
</ul>

<h5>Meeting your stoma care nurse</h5>
<p>Before surgery you will meet a stoma care nurse — a specialist nurse trained in stoma management. This appointment is important: the nurse explains what to expect, answers your questions, and introduces you to different types of pouches with hands-on examples. Do not hesitate to ask questions — the more you understand beforehand, the more confident you will feel after surgery.</p>

<h5>Stoma site marking</h5>
<p>One of the most important parts of pre-operative preparation is marking the optimal location for the stoma on the abdomen. The stoma care nurse determines this position carefully, taking into account skin folds, scars, bony prominences, and how you sit, stand, and bend. A correctly chosen site makes pouch application significantly easier and reduces the risk of leakage. The site is marked with ink before surgery.</p>

<h5>What to pack for hospital</h5>
<p>A hospital stay after stoma surgery is typically between three and fourteen days. It is advisable to bring:</p>
<ul>
<li>Loose-fitting clothing — without elastic or a waistband at stoma level</li>
<li>Comfortable indoor clothes and non-slip slippers</li>
<li>Toiletries and personal hygiene products</li>
<li>Something to read or for entertainment</li>
<li>Phone and charger</li>
</ul>

<h5>Recovery after discharge</h5>
<p>Full recovery takes approximately eight weeks after discharge from hospital. During this time, stoma care nurses provide regular check-ups — by phone, online, or in clinic — to assess healing and answer questions. Do not leave hospital before you feel confident changing the pouch independently.</p>',
            ],
            [
                'slug'       => 'vazstanovyavane-sled-operatsia',
                'date'       => '2025-05-08',
                'title_bg'   => 'Възстановяване след операцията',
                'excerpt_bg' => 'Какво да очаквате в първите седмици след операцията — активност, вдигане на тежести, шофиране, как изглежда здрава стома и кога да се свържете с терапевта.',
                'content_bg' => '<p>Първите осем седмици след операцията за стома са период на физическо и емоционално възстановяване. Тялото се нуждае от почивка, за да се върне към нормалното си функциониране. Следването на препоръките за активност намалява риска от усложнения и ускорява заздравяването.</p>

<h5>Как изглежда здрава стома</h5>
<p>В първите дни след операцията стомата е оточна — по-голяма от окончателния си размер. Постепенно намалява и достига нормалните си размери обикновено около осмата седмица. Здравата стома е:</p>
<ul>
<li>Розова или червена на цвят — подобно на вътрешността на устата</li>
<li>Мека и влажна на допир</li>
<li>Приблизително с размера на монета от 50 стотинки</li>
<li>Леко изпъкнала над повърхността на кожата</li>
</ul>
<p>Лекото кървене по повърхността на стомата при почистване е нормално — тя има богато кръвоснабдяване. Ако кървенето е обилно или не спира, свържете се с ентеростомния терапевт.</p>

<h5>Ограничения при вдигане на тежести</h5>
<p>В продължение на осем до дванадесет седмици след операцията избягвайте вдигане на предмети, по-тежки от пълна кана с вода. Прекомерното натоварване на коремните мускули в ранния период увеличава значително риска от развитие на парасторнална херния — усложнение, което може да изисква допълнителна операция.</p>

<h5>Упражнения</h5>
<p>Леката разходка е препоръчителна от първите дни след операцията и спомага за възстановяването. Упражнения, натоварващи коремните мускули — коремни преси, вдигане на тежести, интензивни кардио тренировки — трябва да се избягват минимум дванадесет седмици след операцията. Преди да се върнете към спорт или фитнес, консултирайте се с хирурга или ентеростомния терапевт.</p>

<h5>Шофиране</h5>
<p>Шофирането не е разрешено в продължение на шест седмици след операцията. Преди да се върнете зад волана, трябва да сте уверени, че можете да реагирате бързо при спешно спиране — без болка или ограничение. Проверете и условията на застраховката си.</p>

<h5>Кога да се свържете с ентеростомния терапевт</h5>
<p>Свържете се незабавно при:</p>
<ul>
<li>Обилно или непрекратяващо се кървене от стомата</li>
<li>Промяна в цвета на стомата — потъмняване или посиняване</li>
<li>Силна болка около стомата</li>
<li>Пълно спиране на отделянията за повече от четири до шест часа при илеостома</li>
<li>Видимо издуване или промяна на формата около стомата</li>
<li>Признаци на инфекция — зачервяване, топлина, секрет от раната</li>
</ul>',
                'title_en'   => 'Recovery After Stoma Surgery',
                'excerpt_en' => 'What to expect in the weeks following surgery — activity restrictions, lifting, driving, what a healthy stoma looks like, and when to contact your nurse.',
                'content_en' => '<p>The first eight weeks after stoma surgery are a period of physical and emotional recovery. The body needs rest to return to normal function. Following activity guidance reduces the risk of complications and supports healing.</p>

<h5>What a healthy stoma looks like</h5>
<p>In the first days after surgery the stoma is swollen — larger than its final size. It gradually reduces and reaches its permanent size at around eight weeks. A healthy stoma is:</p>
<ul>
<li>Pink or red in colour — similar to the inside of the mouth</li>
<li>Soft and moist to touch</li>
<li>Approximately the size of a 50 pence coin</li>
<li>Slightly raised above the skin surface</li>
</ul>
<p>Light bleeding from the stoma surface during cleaning is normal — it has a rich blood supply. If bleeding is heavy or does not stop, contact your stoma care nurse.</p>

<h5>Lifting restrictions</h5>
<p>For eight to twelve weeks after surgery, avoid lifting anything heavier than a full kettle. Excessive strain on the abdominal muscles in the early post-operative period significantly increases the risk of developing a parastomal hernia — a complication that may require further surgery.</p>

<h5>Exercise</h5>
<p>Light walking is recommended from the first days after surgery and supports recovery. Exercises that load the abdominal muscles — sit-ups, weightlifting, intense cardio — should be avoided for a minimum of twelve weeks after surgery. Before returning to sport or gym training, consult your surgeon or stoma care nurse.</p>

<h5>Driving</h5>
<p>Driving is not permitted for six weeks after surgery. Before returning to the wheel, you must be confident that you can react quickly in an emergency stop — without pain or restriction. Check your insurance policy conditions as well.</p>

<h5>When to contact your stoma care nurse</h5>
<p>Contact your nurse immediately if you notice:</p>
<ul>
<li>Heavy or persistent bleeding from the stoma</li>
<li>Change in stoma colour — darkening or turning blue/purple</li>
<li>Severe pain around the stoma</li>
<li>Complete absence of output for more than four to six hours with an ileostomy</li>
<li>Visible bulging or change in shape around the stoma</li>
<li>Signs of infection — redness, warmth, or discharge from the wound</li>
</ul>',
            ],
            [
                'slug'       => 'problemi-s-kozha-okolo-stomata',
                'date'       => '2025-05-15',
                'title_bg'   => 'Проблеми с кожата около стомата',
                'excerpt_bg' => 'Дразнене, изтичане, панкейкинг — най-честите кожни предизвикателства при стома и как да ги преодолеете.',
                'content_bg' => '<p>Кожните проблеми около стомата са едни от най-честите оплаквания при хора с остома. Повечето от тях имат конкретна и разрешима причина. Разпознаването на проблема рано и правилната реакция предотвратява влошаването му.</p>

<h5>Изтичане под основата</h5>
<p>Изтичането на отделяния под основата на торбичката е основната причина за кожно дразнене. Когато отделянията достигат кожата, причиняват химическо дразнене, зачервяване и болка — а увредената кожа от своя страна затруднява залепването на следващата основа, създавайки порочен кръг.</p>
<p>Честите причини за изтичане:</p>
<ul>
<li>Отвърстието на основата е прекалено голямо — пролуката между основата и стомата трябва да е не повече от 2–3 мм</li>
<li>Промяна в размера или формата на стомата, без актуализиране на изрязването</li>
<li>Промяна в телесното тегло, влияеща върху позицията на стомата</li>
<li>Неравна кожна повърхност около стомата — гънки, белези или неравности</li>
</ul>

<h5>Панкейкинг</h5>
<p>Панкейкингът се случва, когато двете страни на торбичката се залепят една за друга, предотвратявайки нормалното събиране на отделянията. Това може да доведе до обратно налягане върху стомата и изтичане под основата. За предотвратяване:</p>
<ul>
<li>Поставете малко количество лубрикант или специализиран гел в торбичката</li>
<li>Осигурете лек въздушен поток в торбичката при поставянето</li>
<li>Изберете торбичка с вграден филтър за газове, за да предотвратите вакуумен ефект</li>
</ul>

<h5>Кожно дразнене и зачервяване</h5>
<p>Ако зачервяването следва точно контура на основата, вероятната причина е алергична реакция към адхезива. При по-широко разпространено дразнене — най-вероятно е контакт с отделяния или задържана влага. При гъбична инфекция (гъста, червена обрив с ясни очертания) се прилага антимикотична пудра.</p>

<h5>Честота на смяна</h5>
<p>Прекалено честата смяна на основата увеличава механичното дразнене и наранява кожата. При всяко отлепване ползвайте спрей или кърпичка за отстраняване на адхезив. Никога не дърпайте суха основа.</p>

<h5>Кога да потърсите помощ</h5>
<p>Никое кожно дразнене около стомата не е „нормално". Ако зачервяването или болката не се подобрят в рамките на две до три смени, или ако забележите открити рани, повишена температура на кожата или секрет — свържете се с ентеростомния терапевт. Всеки проблем има конкретна причина и конкретно решение.</p>',
                'title_en'   => 'Stoma Skin Challenges',
                'excerpt_en' => 'Irritation, leakage, pancaking — the most common peristomal skin problems and how to address them.',
                'content_en' => '<p>Skin problems around the stoma are among the most commonly reported issues for people with an ostomy. Most have a specific and resolvable cause. Recognising the problem early and responding correctly prevents it from worsening.</p>

<h5>Leakage under the baseplate</h5>
<p>Output leaking under the baseplate is the primary cause of peristomal skin irritation. When output reaches the skin it causes chemical irritation, redness, and pain — and damaged skin in turn makes the next baseplate harder to seal, creating a cycle.</p>
<p>Common causes of leakage:</p>
<ul>
<li>The baseplate opening is too large — the gap between the baseplate and the stoma edge should be no more than 2 to 3 mm</li>
<li>Change in stoma size or shape without updating the cut size</li>
<li>Change in body weight affecting stoma position</li>
<li>Uneven skin surface around the stoma — folds, scars, or contour changes</li>
</ul>

<h5>Pancaking</h5>
<p>Pancaking occurs when the two sides of the pouch stick together, preventing output from collecting normally. This can cause back-pressure on the stoma and leakage under the baseplate. To prevent it:</p>
<ul>
<li>Place a small amount of lubricant or stoma-specific gel inside the pouch</li>
<li>Ensure a small amount of air is in the pouch when fitting</li>
<li>Choose a pouch with an integrated gas filter to prevent a vacuum effect</li>
</ul>

<h5>Skin irritation and redness</h5>
<p>If redness follows the exact outline of the baseplate, the likely cause is an adhesive reaction. More widespread irritation suggests output contact or trapped moisture. Fungal infection presents as a dense red rash with clear borders and is treated with antifungal powder — not cream, which would disrupt adhesion.</p>

<h5>Frequency of changes</h5>
<p>Changing the baseplate too frequently increases mechanical trauma and damages the skin. Use an adhesive remover spray or wipe at every change. Never pull a dry baseplate off.</p>

<h5>When to seek help</h5>
<p>No peristomal skin irritation is "normal." If redness or discomfort does not improve within two to three changes, or if you notice open sores, skin warmth, or discharge, contact your stoma care nurse. Every problem has a specific cause and a specific solution.</p>',
            ],
            [
                'slug'       => 'premahvane-na-meditsinsko-lepilo',
                'date'       => '2025-05-22',
                'title_bg'   => 'Средства за отстраняване на медицинско лепило',
                'excerpt_bg' => 'Защо средствата за отстраняване на адхезив са важна част от грижата за стомата и как да ги използвате правилно.',
                'content_bg' => '<p>Отстраняването на основата на торбичката без помощни средства е едно от най-честите причини за увреждане на кожата около стомата. Дърпането на суха основа отнася повърхностния слой на кожата — процес, наречен „стрипинг". Средствата за отстраняване на медицинско лепило са разработени именно за да предотвратят това.</p>

<h5>Какво представляват</h5>
<p>Средствата за отстраняване на медицинско лепило се предлагат под формата на спрей или кърпичка. Те съдържат разтворители, които разграждат адхезива между основата и кожата, позволявайки бавно и безболезнено отлепване без механично натоварване на кожата.</p>

<h5>Защо са важни</h5>
<p>Редовното отлепване на основата без помощни средства постепенно уврежда кожната бариера. Увредената кожа:</p>
<ul>
<li>Е по-чувствителна и болезнена</li>
<li>Залепва по-слабо при следващата основа, увеличавайки риска от изтичане</li>
<li>Е по-предразположена към гъбични инфекции и дразнене</li>
</ul>
<p>Употребата на средство за отстраняване на адхезив при всяка смяна е превантивна мярка, а не само реакция при проблем.</p>

<h5>Как се използват</h5>
<p><strong>Спрей:</strong> Нанесете спрея под ръба на основата, докато отлепвате бавно. Спреят действа незабавно — не е необходимо да изчаквате. Поддържайте кожата с другата ръка при отлепването.</p>
<p><strong>Кърпичка:</strong> Плъзнете кърпичката между основата и кожата, като отлепвате постепенно. Особено удобна за по-прецизно нанасяне около стомата.</p>

<h5>Допълнителни ползи</h5>
<p>Някои средства за отстраняване на адхезив имат и допълнителни функции:</p>
<ul>
<li>Намаляват неприятната миризма при смяна</li>
<li>Почистват остатъци от лепило върху кожата</li>
<li>Успокояват раздразнена кожа</li>
</ul>

<h5>Важно</h5>
<p>Изчакайте кожата да изсъхне напълно след употреба на средство за отстраняване на адхезив, преди да поставите новата основа. Остатъчен разтворител върху кожата намалява адхезията на следващата основа. Повечето продукти изсъхват за 30–60 секунди.</p>',
                'title_en'   => 'Medical Adhesive Removers',
                'excerpt_en' => 'Why adhesive remover products are an essential part of stoma care and how to use them correctly at every change.',
                'content_en' => '<p>Removing the pouch baseplate without an adhesive remover is one of the most common causes of peristomal skin damage. Pulling off a dry baseplate strips the surface layer of the skin — a process called skin stripping. Medical adhesive removers are specifically designed to prevent this.</p>

<h5>What they are</h5>
<p>Medical adhesive removers are available as sprays or wipes. They contain solvents that dissolve the adhesive bond between the baseplate and the skin, allowing slow, painless removal without mechanical trauma to the skin surface.</p>

<h5>Why they matter</h5>
<p>Regularly removing the baseplate without an adhesive remover gradually damages the skin barrier. Damaged peristomal skin:</p>
<ul>
<li>Is more sensitive and painful</li>
<li>Bonds less effectively with the next baseplate, increasing leak risk</li>
<li>Is more susceptible to fungal infection and irritation</li>
</ul>
<p>Using an adhesive remover at every change is a preventive measure — not just a response to existing problems.</p>

<h5>How to use them</h5>
<p><strong>Spray:</strong> Apply the spray under the edge of the baseplate as you peel slowly. The spray acts immediately — no waiting required. Support the skin with the other hand as you remove the baseplate.</p>
<p><strong>Wipe:</strong> Slide the wipe between the baseplate and the skin, peeling gradually. Particularly useful for more precise application close to the stoma.</p>

<h5>Additional benefits</h5>
<p>Some adhesive remover products offer additional benefits:</p>
<ul>
<li>Reduce unpleasant odour during pouch changes</li>
<li>Clean adhesive residue from the skin</li>
<li>Soothe mildly irritated skin</li>
</ul>

<h5>Important note</h5>
<p>Allow the skin to dry completely after using an adhesive remover before applying the new baseplate. Residual solvent on the skin reduces the adhesion of the next barrier. Most products dry within 30 to 60 seconds.</p>',
            ],
            [
                'slug'       => 'parastomasna-hernia',
                'date'       => '2025-05-29',
                'title_bg'   => 'Парасторнална херния',
                'excerpt_bg' => 'Какво е парасторнална херния, как да я разпознаете, как да я предотвратите и какви са възможностите за лечение.',
                'content_bg' => '<p>Парасторналната херния е едно от най-честите дългосрочни усложнения след операция за стома — засяга до 50% от хората в рамките на първите две години. Въпреки честотата си, тя е предотвратима при правилна грижа и може да се управлява консервативно в много случаи.</p>

<h5>Какво представлява</h5>
<p>Парасторналната херния е издутина около стомата, причинена от отслабване на коремната мускулатура и съединителна тъкан на место то, където стомата е изведена на повърхността. Коремното съдържимо — обикновено тънко или дебело черво — се промъква през това слабо место и се вижда като изпъкналост около стомата.</p>

<h5>Признаци и симптоми</h5>
<p>Ранното разпознаване е важно. Потърсете ентеростомен терапевт или лекар при:</p>
<ul>
<li>Видима издутина или промяна в контура около стомата</li>
<li>Болка или дискомфорт при вдигане на тежести, кашляне или кихане</li>
<li>Промяна в функцията на стомата — по-трудно изпразване или промяна в отделянията</li>
<li>Затруднение при поставяне на торбичката поради промяна в формата на стомата</li>
<li>Подуване на корема</li>
</ul>

<h5>Профилактика</h5>
<p>Рискът от парасторнална херния може да се намали значително с:</p>
<ul>
<li><strong>Избягване на тежко вдигане</strong> — особено в първите 8–12 седмици след операцията</li>
<li><strong>Укрепване на коремните мускули</strong> — специализирани упражнения, препоръчани от физиотерапевт; стандартните коремни преси не са подходящи</li>
<li><strong>Поддържащ колан</strong> — носенето на специализиран колан за стома осигурява допълнителна подкрепа на коремната стена, особено при физическа активност</li>
<li><strong>Здравословно тегло</strong> — наднорменото тегло увеличава натиска върху коремната стена</li>
<li><strong>Отказ от тютюнопушене</strong> — пушенето забавя зарастването и отслабва съединителната тъкан</li>
<li><strong>Правилна техника при кашляне и кихане</strong> — поддържайте корема с ръка при кашляне или поставете малка възглавничка</li>
</ul>

<h5>Лечение</h5>
<p>Не всяка парасторнална херния изисква операция. При малки хернии без симптоми консервативното лечение — поддържащ колан, контрол на теглото, адаптация на торбичката — е достатъчно. При хернии, причиняващи болка, затруднено управление на стомата или риск от усложнения, хирургичното лечение е опция. Операцията включва укрепване на коремната стена с мрежа; стомата може да се репозиционира или да остане на същото место.</p>

<p>Хернията може да се влоши с времето — не отлагайте консултацията с ентеростомния терапевт при поява на симптоми.</p>',
                'title_en'   => 'Parastomal Hernia',
                'excerpt_en' => 'What a parastomal hernia is, how to recognise one, how to prevent it, and what treatment options are available.',
                'content_en' => '<p>A parastomal hernia is one of the most common long-term complications after stoma surgery — affecting up to 50% of people within the first two years. Despite its frequency, it is preventable with proper care and can often be managed conservatively without surgery.</p>

<h5>What it is</h5>
<p>A parastomal hernia is a bulge around the stoma caused by weakening of the abdominal muscle and connective tissue at the site where the stoma exits the body. Abdominal contents — usually small or large bowel — push through this weak point and appear as a visible protrusion around the stoma.</p>

<h5>Signs and symptoms</h5>
<p>Early recognition is important. Contact your stoma care nurse or doctor if you notice:</p>
<ul>
<li>Visible bulging or change in the contour around the stoma</li>
<li>Pain or discomfort during lifting, coughing, or sneezing</li>
<li>Changes in stoma function — more difficult emptying or change in output</li>
<li>Difficulty fitting the pouch due to change in stoma shape</li>
<li>Abdominal bloating</li>
</ul>

<h5>Prevention</h5>
<p>The risk of parastomal hernia can be significantly reduced by:</p>
<ul>
<li><strong>Avoiding heavy lifting</strong> — particularly in the first 8 to 12 weeks after surgery</li>
<li><strong>Strengthening the abdominal muscles</strong> — through exercises specifically recommended by a physiotherapist; standard sit-ups are not appropriate</li>
<li><strong>Wearing a support belt</strong> — a specialist ostomy support belt provides additional support for the abdominal wall, particularly during physical activity</li>
<li><strong>Maintaining a healthy weight</strong> — excess weight increases pressure on the abdominal wall</li>
<li><strong>Stopping smoking</strong> — smoking delays healing and weakens connective tissue</li>
<li><strong>Correct technique when coughing or sneezing</strong> — support the abdomen with your hand or a small cushion</li>
</ul>

<h5>Treatment</h5>
<p>Not every parastomal hernia requires surgery. For small hernias without symptoms, conservative management — a support belt, weight control, and pouch adaptation — is often sufficient. For hernias causing pain, difficult stoma management, or risk of complications, surgical repair is an option. Surgery involves reinforcing the abdominal wall with mesh; the stoma may be repositioned or remain in place.</p>

<p>Hernias can worsen over time — do not delay consulting your stoma care nurse when symptoms appear.</p>',
            ],
        ];
    }
}
