<?php

namespace App\Command;

use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-blog-care-en',
    description: 'Update "Care & Hygiene" blog articles with BG content (where missing) and EN translations',
)]
class UpdateBlogCareEnCommand extends Command
{
    public function __construct(
        private BlogPostRepository $postRepository,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $articles = $this->getArticles();

        foreach ($articles as $data) {
            $post = $this->postRepository->findOneBy(['slug' => $data['slug']]);
            if (!$post) {
                $io->warning(sprintf('Post "%s" not found, skipping.', $data['slug']));
                continue;
            }

            if (!empty($data['content_bg'])) {
                $post->setContentBg($data['content_bg']);
            }

            $post->setTitleEn($data['title_en']);
            $post->setExcerptEn($data['excerpt_en']);
            $post->setContentEn($data['content_en']);

            $io->text(sprintf('Updated: %s', $data['title_en']));
        }

        $this->em->flush();
        $io->success('All Care & Hygiene articles updated.');

        return Command::SUCCESS;
    }

    private function getArticles(): array
    {
        return [
            // Article 8 — BG content already exists, only EN needed
            [
                'slug'       => 'grizha-za-kozha-okolo-stomata',
                'content_bg' => '',
                'title_en'   => 'Skin Care Around the Stoma',
                'excerpt_en' => 'The skin around the stoma is sensitive and requires special care. Learn which products are suitable and how to keep the peristomal area healthy.',
                'content_en' => '<p>The skin around the stoma — known as peristomal skin — is in constant contact with adhesive materials and output. Up to 75% of people with an ostomy experience peristomal skin complications at some point. Proper care is essential for preventing problems and keeping the pouch sealed securely.</p>

<h5>Why peristomal skin care matters</h5>
<p>Irritated or damaged peristomal skin makes the baseplate harder to seal, causes discomfort, and leads to leaks. Preventing skin breakdown is far easier than treating it. Many people who experience frequent leaks find that addressing the skin condition first resolves the underlying problem.</p>

<h5>Suitable products</h5>
<ul>
<li><strong>Alcohol-free skin cleansers</strong> — designed specifically for peristomal skin, they clean without leaving adhesion-disrupting residue</li>
<li><strong>Skin barrier films</strong> — create a thin protective layer between the skin and the adhesive, reducing wear over time</li>
<li><strong>Hydrocolloid paste or barrier rings</strong> — fill uneven skin areas to improve the seal and prevent output from tracking under the baseplate</li>
<li><strong>Absorbent powder</strong> — for moist or weeping skin; always seal with a barrier film before applying the baseplate</li>
</ul>

<h5>What to avoid</h5>
<ul>
<li>Creams and lotions containing oils or moisturisers — these leave a residue that prevents the adhesive from bonding properly</li>
<li>Baby wipes or household wipes with alcohol, fragrance, or lanolin</li>
<li>Rubbing or scrubbing the skin — pat dry gently instead</li>
<li>Soap with added moisturisers or conditioning agents</li>
</ul>

<h5>When to seek help</h5>
<p>No skin irritation around the stoma is "normal." If redness does not clear within two or three pouch changes, or if you notice open sores, raised bumps, or a rash following the exact outline of the baseplate, contact a stoma care nurse. These signs point to specific causes — irritant contact, allergic reaction, or fungal infection — each of which has a targeted treatment.</p>',
            ],
            // Articles 9–14 — BG content missing, both BG and EN needed
            [
                'slug'       => 'kak-da-predotvratite-drazneneto',
                'content_bg' => '<p>Дразненето на кожата около стомата е едно от най-честите оплаквания при хора с остома. То варира от лека зачервеност до болезнена, увредена кожа — и почти винаги има конкретна и поправима причина. Разпознаването на тригерите позволява да реагирате, преди дразненето да се задълбочи.</p>

<h5>Най-честите причини</h5>
<ul>
<li><strong>Контакт с отделянията</strong> — най-честата причина. Ако отвърстието на основата е прекалено голямо, отделянията достигат до кожата около стомата и предизвикват химическо дразнене. Пролуката между основата и стомата трябва да е не повече от 2–3 мм.</li>
<li><strong>Реакция към адхезива</strong> — при някои хора с времето се развива чувствителност към лепилото. Ако зачервеността следва точно контура на основата, това е вероятната причина.</li>
<li><strong>Механично увреждане</strong> — бързото или прекалено честото отлепване на основата отстранява повърхностния слой на кожата. Винаги отлепвайте бавно, като поддържате кожата с другата ръка.</li>
<li><strong>Задържана влага под основата</strong> — потта или непълното подсушаване след измиване създават влажна среда, която отслабва кожата и благоприятства развитието на гъбична инфекция.</li>
</ul>

<h5>Практически мерки за превенция</h5>
<ul>
<li>Измервайте стомата редовно — особено в първите месеци след операцията — и изрязвайте основата точно по размер</li>
<li>Използвайте спрей или кърпичка за отстраняване на адхезив преди отлепването — никога не дърпайте суха основа</li>
<li>Уверете се, че кожата е напълно суха преди поставянето на новата основа</li>
<li>Нанасяйте защитен филм като бариера между кожата и лепилото</li>
<li>При силно изпотяване обмислете основи с по-дълъг живот или колан, за да намалите триенето и движението</li>
</ul>

<h5>Ако дразненето вече е налице</h5>
<p>Нанесете абсорбираща пудра върху мократа или раздразнена кожа, след което запечатайте с защитен филм преди поставянето на новата основа. Това позволява на кожата да започне да се възстановява, без да прекъсвате нормалното носене. Ако дразненето се влошава или не се подобрява в рамките на една седмица, потърсете съвет от ентеростомен терапевт.</p>',
                'title_en'   => 'How to Prevent Skin Irritation Around the Stoma',
                'excerpt_en' => 'Redness and itching around the stoma are common but preventable. Here are proven methods for protecting your skin.',
                'content_en' => '<p>Skin irritation around the stoma is one of the most common issues people with an ostomy face. It ranges from mild redness to painful raw skin, and it almost always has an identifiable and fixable cause. Understanding the most frequent triggers helps you act before the irritation becomes serious.</p>

<h5>The most common causes</h5>
<ul>
<li><strong>Output contact with the skin</strong> — the most frequent cause. If the opening in the baseplate is too large, output reaches the peristomal skin and causes chemical irritation. The gap between the baseplate opening and the stoma edge should be no more than 2 to 3 mm.</li>
<li><strong>Adhesive reaction</strong> — some people develop sensitivity to the adhesive over time. If redness follows the exact outline of the baseplate, this is likely the cause.</li>
<li><strong>Mechanical damage</strong> — peeling the baseplate off too quickly or too frequently removes the surface layer of skin. Always remove the baseplate slowly while supporting the skin with the other hand.</li>
<li><strong>Moisture trapped under the baseplate</strong> — sweat or incomplete drying after washing creates a humid environment that weakens the skin and promotes bacterial or fungal growth.</li>
</ul>

<h5>Practical prevention steps</h5>
<ul>
<li>Measure your stoma regularly, especially in the first months after surgery, and cut the baseplate opening precisely</li>
<li>Use an adhesive remover spray or wipe to dissolve the adhesive before peeling — never pull a dry baseplate off</li>
<li>Make sure the skin is completely dry before applying the new baseplate</li>
<li>Use a skin barrier film as a protective layer between the skin and the adhesive</li>
<li>If you sweat heavily, consider an extended-wear baseplate or a belt to reduce movement and friction</li>
</ul>

<h5>If irritation is already present</h5>
<p>Apply absorbent powder to weeping or moist skin, then seal it with a barrier film before fitting the new baseplate. This allows the skin to begin healing while still wearing the appliance normally. If the irritation worsens or does not improve within a week, seek advice from a stoma care nurse. No skin irritation around the stoma is "normal" — there is always a specific cause and a specific solution.</p>',
            ],
            [
                'slug'       => 'pochistvane-i-higiena-pri-smyana',
                'content_bg' => '<p>Начинът, по който почиствате перистомалната кожа при всяка смяна, влияе пряко върху нейното здраве и върху това колко добре ще залепне следващата основа. Процесът е прост, но детайлите имат значение.</p>

<h5>От какво се нуждаете</h5>
<ul>
<li>Топла вода и мека кърпа или марля — най-ефективният и щадящ метод за почистване</li>
<li>Специализиран почистващ препарат за перистомална кожа, ако обикновената вода не е достатъчна</li>
<li>Суха марля или мека кърпа за подсушаване</li>
<li>Новата основа и торбичка</li>
</ul>

<h5>Стъпки при смяна</h5>
<p>Преди отлепването изпразнете торбичката, ако е пълна. При отлепването поддържайте кожата с едната ръка и бавно отлепвайте основата отгоре надолу. Използвайте спрей за отстраняване на адхезив — прави процеса по-лесен и по-нетравматичен за кожата.</p>

<p>След отстраняването почистете кожата с влажна кърпа с кръгови движения от стомата навън. Изплакнете добре, за да не останат следи от препарат. Ако използвате сапун, изберете такъв без овлажнители, масла или парфюм — дори малко количество остатъчен продукт намалява адхезията.</p>

<p>Подсушете кожата с мека кърпа. Не търкайте — попивайте. Кожата трябва да е напълно суха — влагата под основата е една от основните причини за преждевременно отлепване. При влажно време или след физическа активност изчакайте допълнителна минута кожата да изсъхне на въздух преди поставянето на новата основа.</p>

<h5>Самата стома</h5>
<p>Почиствайте около стомата внимателно. Лекото кървене от стомалната тъкан е нормално — стомата има богато кръвоснабдяване и реагира на допир. Това не е повод за притеснение, освен ако кървенето не е обилно или не спира. Никога не поставяйте нищо в отвора на стомата.</p>

<h5>Честота</h5>
<p>Няма нужда да почиствате по-обстойно или по-често от нормалната смяна. Прекомерното почистване, особено с агресивни продукти, премахва естествения защитен слой на кожата и увеличава чувствителността. Почиствайте обстойно при всяка планирана смяна — това е достатъчно.</p>',
                'title_en'   => 'Cleaning and Hygiene During Pouch Changes',
                'excerpt_en' => 'Proper cleaning at every change is the foundation of healthy skin and long-lasting pouch adhesion.',
                'content_en' => '<p>How you clean the peristomal skin at each change has a direct effect on skin health and how well the next baseplate adheres. The process is straightforward, but the details matter.</p>

<h5>What you need</h5>
<ul>
<li>Warm water and a soft cloth or gauze — the most effective and skin-friendly cleaning method</li>
<li>A stoma-specific cleanser if plain water is not sufficient</li>
<li>Dry gauze or a soft towel for patting the skin dry</li>
<li>Your replacement baseplate and pouch</li>
</ul>

<h5>Step by step</h5>
<p>Empty the pouch before removal if it contains liquid output. When removing the baseplate, support the skin with one hand and peel slowly from top to bottom. Use an adhesive remover spray or wipe — it makes removal easier and less traumatic to the skin. Never pull a dry baseplate off.</p>

<p>Wipe the peristomal skin gently using a damp cloth with circular motions from the stoma outward. Rinse thoroughly to remove all cleanser residue. If you use soap, choose one without moisturisers, oils, or fragrance — even a thin invisible film left on the skin reduces adhesive lifespan and increases leak risk.</p>

<p>Pat the skin dry with a soft cloth. Do not rub. The skin must be completely dry before applying the new baseplate — moisture underneath is one of the most common causes of early detachment. In humid conditions or after physical activity, allow an extra minute for the skin to air-dry.</p>

<h5>The stoma itself</h5>
<p>Clean gently around the stoma opening. Light bleeding from the stoma tissue is normal — the stoma has a rich blood supply and reacts to contact. This is not a cause for concern unless bleeding is heavy or does not stop. Never insert anything into the stoma opening.</p>

<h5>Frequency</h5>
<p>There is no benefit to cleaning more often or more thoroughly than during a normal scheduled change. Over-cleaning, particularly with harsh products, strips the natural protective barrier of the skin and increases sensitivity. Clean well at every change — that is enough.</p>',
            ],
            [
                'slug'       => 'kakvi-produkti-ne-tryabva-da-izpolzvate',
                'content_bg' => '<p>Много ежедневни продукти за лична хигиена, напълно безопасни за нормалната кожа, са проблематични около стомата. Причината не е токсичност — а адхезия. Всеки продукт, оставящ остатък върху кожата, намалява залепването на основата и води до течове.</p>

<p>Основното правило: <strong>по-малкото е по-добре.</strong> Топлата вода е достатъчна за почистване в повечето случаи.</p>

<h5>Продукти, които трябва да избягвате</h5>
<ul>
<li><strong>Сапуни и душ гелове с овлажнители</strong> — оставят тънък филм от кондиционери върху кожата, който пречи на лепилото да се свърже. Използвайте само обикновен, непарфюмиран сапун или само топла вода.</li>
<li><strong>Кремове и лосиони за тяло</strong> — всяко емолиентно средство, нанесено върху перистомалната кожа, дори в малки количества, ще наруши адхезията. Нанасяйте лосиони само на останалите части на тялото.</li>
<li><strong>Бебийски кърпички и бебийско масло</strong> — съдържат овлажнители и масла. Бебийските кърпички са особено неподходящи за подготовка на кожата преди поставяне на основа.</li>
<li><strong>Алкохол и антисептични разтвори</strong> — изсушават и дразнят кожата при многократна употреба и увреждат кожната бариера.</li>
<li><strong>Талк и обикновени пудри за тяло</strong> — оставят прашист слой, намаляващ адхезията. Използвайте само специализирана абсорбираща пудра за стоми и задължително я запечатайте с защитен филм преди поставянето на основата.</li>
<li><strong>Кремообразни противогъбични препарати</strong> — никога не нанасяйте кремообразни антимикотици под основата — унищожават адхезията. При нужда използвайте само пудра (не крем) и отстранете излишъка преди поставянето.</li>
<li><strong>Слънцезащитни кремове преди поставяне на основата</strong> — нанасяйте слънцезащитен крем само след като основата вече е поставена, върху откритата кожа.</li>
</ul>

<h5>Практическо правило</h5>
<p>Ако даден продукт е предназначен да остави кожата мека, гладка или овлажнена — почти сигурно ще повлияе на адхезията. Пазете перистомалната зона чиста и без допълнителни продукти. При съмнение относно конкретен продукт, питайте ентеростомния терапевт преди употреба.</p>',
                'title_en'   => 'Products You Should Never Use Around Your Stoma',
                'excerpt_en' => 'Some creams, oils, and cleaning products damage the adhesive layer. Find out which ones to avoid and why.',
                'content_en' => '<p>Many everyday personal care products that are perfectly safe for normal skin are problematic around a stoma. The issue is not toxicity — it is adhesion. Any product that leaves a residue on the skin will reduce how well the baseplate sticks, leading to leaks and skin exposure.</p>

<p>The guiding principle from stoma care specialists is: <strong>less is better.</strong> Plain warm water is sufficient for cleaning in most cases.</p>

<h5>Products to avoid</h5>
<ul>
<li><strong>Moisturising soaps and shower gels</strong> — these leave a thin film of conditioning agents on the skin that prevents the adhesive from bonding. Use plain, fragrance-free soap without oils or lotions, or warm water alone.</li>
<li><strong>Body lotions and creams</strong> — any emollient applied to the peristomal skin, even sparingly, will interfere with adhesion. Apply lotions to other areas of the body and keep them away from the baseplate zone.</li>
<li><strong>Baby wipes and baby oil</strong> — contain conditioning agents and oils. Baby wipes are particularly unsuitable for preparing peristomal skin before applying a new baseplate.</li>
<li><strong>Alcohol and antiseptic solutions</strong> — dry out and irritate the skin with repeated use and damage the skin barrier over time.</li>
<li><strong>Talcum powder and body powder</strong> — create a dusty layer that reduces adhesive effectiveness. Use only stoma-specific absorbent powder when needed, and always seal it with a barrier film before fitting the baseplate.</li>
<li><strong>Antifungal cream</strong> — never apply cream-form antifungal products under the pouching system as they destroy adhesion. Antifungal powder (not cream) is acceptable when medically needed, with excess brushed off before applying the barrier.</li>
<li><strong>Sunscreen applied before the pouch</strong> — apply sunscreen only after the baseplate is already in place, onto exposed skin around the pouch.</li>
</ul>

<h5>A practical rule</h5>
<p>If a product is designed to leave the skin soft, smooth, or moisturised, it will almost certainly affect adhesion. If you are unsure about a specific product, ask your stoma care nurse before using it near the baseplate area.</p>',
            ],
            [
                'slug'       => 'grizha-za-stomata-pri-toplo-vreme',
                'content_bg' => '<p>Топлото време и активният летен начин на живот са напълно съвместими с наличието на остома. Предизвикателствата, които жегата носи — основно повишено изпотяване и желание за плуване — имат практически решения, които повечето хора откриват бързо след първото лято след операцията.</p>

<h5>Изпотяване и адхезия</h5>
<p>Потта е една от основните причини основите да се отлепят по-рано през лятото. Влагата се натрупва под ръбовете на основата и постепенно разхлабва уплътнението. Ето какво помага:</p>
<ul>
<li>Поставяйте основата в хладна, суха среда — изчакайте потта да се успокои след физическа активност преди смяна</li>
<li>Нанасяйте защитен филм върху перистомалната кожа преди поставянето — създава по-стабилна повърхност за лепилото</li>
<li>Обмислете основи с продължен живот — проектирани с адхезиви, устойчиви на влага</li>
<li>Колан за остома осигурява допълнителна опора и намалява движението на основата при горещи, активни дни</li>
</ul>

<h5>Плуване</h5>
<p>Плуването с остома е безопасно. Водата не уврежда стомата. Практическата задача е уплътнението да остане здраво по време и след престоя във водата. Изпразнете торбичката преди влизане в басейна или морето. Много хора установяват, че водоустойчива лента по ръбовете на основата осигурява достатъчна допълнителна сигурност. След плуването подсушете внимателно — не търкайте. Ако уплътнението изглежда нарушено след продължително плуване, сменете по-рано от обичайното.</p>

<h5>Хидратация</h5>
<p>Хората с илеостома трябва да са особено внимателни с приема на течности в горещо време — обемът на отделянията нараства с жегата и физическата активност. Пийте повече вода от обичайното и включете електролити при обилно изпотяване или спортуване. Следете цвета на урината — бледожълта означава добра хидратация; тъмна означава, че трябва да пиете повече.</p>

<h5>Слънце и облекло</h5>
<p>Нанасяйте слънцезащитен крем само след поставянето на основата — никога не го нанасяйте върху перистомалната кожа преди залепването. Избягвайте прекалено прилепнало облекло, което може да ограничи потока в торбичката. При слънчево греене покрийте торбичката с лека кърпа или носете бански, покриващ зоната.</p>',
                'title_en'   => 'Caring for Your Stoma in Hot Weather',
                'excerpt_en' => 'Summer brings specific challenges — sweating, sun, and swimming. Here is how to manage them confidently.',
                'content_en' => '<p>Warm weather and an active summer lifestyle are entirely compatible with having a stoma. The main challenges heat brings — increased sweating and the desire to swim — have practical solutions that most people discover quickly after their first summer post-surgery.</p>

<h5>Sweating and adhesion</h5>
<p>Sweat is one of the main reasons baseplates detach earlier in summer. Moisture accumulates under the edges and gradually loosens the seal. Several things help:</p>
<ul>
<li>Apply the baseplate in a cool, dry environment — wait until sweating has settled after physical activity before changing</li>
<li>Use a skin barrier film before fitting the baseplate — it creates a more stable surface for the adhesive</li>
<li>Consider extended-wear baseplates, which use adhesives designed to resist moisture better than standard versions</li>
<li>An ostomy belt provides additional support and reduces baseplate movement during hot, active days</li>
</ul>

<h5>Swimming</h5>
<p>Swimming with an ostomy is safe. Water does not harm the stoma. Empty the pouch before entering the water and verify the seal is secure. For extra security, waterproof tape strips around the baseplate edges are effective for swimming. After swimming, pat the baseplate dry carefully — do not rub. If the seal feels compromised after a long swim, change earlier than your usual schedule. Always swim with a pouch in place — this is required for sanitary reasons.</p>

<h5>Hydration</h5>
<p>People with an ileostomy in particular need to monitor fluid intake carefully in hot weather, as output volume increases with heat and physical activity. Drink more than usual and include electrolyte replacement if sweating heavily. Pale straw-coloured urine means good hydration; dark amber means drink more.</p>

<h5>Sun and clothing</h5>
<p>Apply sunscreen only after the baseplate is already fitted — never apply it to peristomal skin before the barrier goes on, as it will prevent adhesion. Avoid excessively tight clothing that restricts output flow into the pouch. When sunbathing, cover the pouch with a light cloth or wear a swimsuit that covers the area.</p>',
            ],
            [
                'slug'       => 'dieta-i-hidratatsia',
                'content_bg' => '<p>Няма универсална диета за хора с остома. Какво работи зависи от вида на остомата, времето след операцията и индивидуалната поносимост. Независимо от това, съществуват общи принципи, полезни за повечето хора — особено в месеците непосредствено след операцията.</p>

<h5>Основни принципи</h5>
<ul>
<li>Въвеждайте нови храни постепенно — по една наведнъж, в малки количества. Изчакайте 12–24 часа, за да наблюдавате реакцията, преди да въведете следваща нова храна</li>
<li>Яжте по-малки порции по-често — 5–6 пъти на ден вместо 3 големи хранения</li>
<li>Дъвчете добре — особено важно при илеостома за предотвратяване на запушвания</li>
<li>Избягвайте обилно хранене вечерта, за да ограничите нощните отделяния</li>
</ul>

<h5>При илеостома</h5>
<p>Дебелото черво е заобиколено или отстранено, затова течностите и електролитите не се реабсорбират напълно. Отделянията са течни или пастообразни.</p>

<p><strong>Хидратация:</strong> Пийте 8–10 чаши (около 2 литра) течности дневно. Пийте на малки глътки през деня — не на едно. При горещо или при спорт добавете спортни напитки или орален рехидратиращ разтвор за заместване на електролити.</p>

<p><strong>Храни, сгъстяващи отделянията:</strong> бял ориз, паста, картофено пюре, бял хляб, зрял банан, овесени ядки, извара, ябълково пюре, сирене.</p>

<p><strong>Храни с риск от запушване (въвеждайте внимателно):</strong> сурова ябълка с кожа, целина, царевица, гъби, ядки и семена, пуканки, сушени плодове, грозде. При запушване — коремни спазми, подуване около стомата, липса на отделяния и гадене — потърсете спешна помощ незабавно. Не приемайте лаксативи.</p>

<h5>При колостома</h5>
<p>Дебелото черво е частично запазено, затова отделянията са по-оформени. <strong>Хидратация:</strong> минимум 2 литра течности дневно. При запек — увеличете приема на вода и фибри (трици, пресни плодове и зеленчуци, пълнозърнести продукти). При диария — яжте сгъстяващи храни (ориз, банан, варен морков) и избягвайте сурови плодове, подправки и мазни храни.</p>

<h5>Газове и миризма (при двата вида)</h5>
<p>Избягвайте газирани напитки, бира, дъвка и преглъщане на въздух. Зеле, лук, чесън, броколи и аспержи засилват газовете. Торбичките с въглероден филтър намаляват проблема значително.</p>',
                'title_en'   => 'Diet and Hydration with an Ostomy',
                'excerpt_en' => 'What you eat directly affects the volume and consistency of output. What to eat, what to limit, and how to stay well hydrated.',
                'content_en' => '<p>There is no single universal ostomy diet. What works depends on the type of ostomy, how long ago the surgery was, and individual tolerance. That said, there are general principles most people find helpful — particularly in the months immediately after the operation.</p>

<h5>General principles for all ostomies</h5>
<ul>
<li>Introduce new foods one at a time, in small portions, and wait 12 to 24 hours before trying another new food</li>
<li>Eat smaller, more frequent meals — five or six smaller meals rather than three large ones</li>
<li>Chew food thoroughly — this is especially important with an ileostomy to prevent blockages</li>
<li>Avoid large meals in the evening to limit nighttime output</li>
</ul>

<h5>With an ileostomy</h5>
<p>The large intestine is bypassed, so fluids and electrolytes are not fully reabsorbed. Output is liquid or paste-like, and the key risks are dehydration and food blockages.</p>

<p><strong>Hydration:</strong> Drink 8 to 10 glasses (approximately 2 litres) of fluid per day. Sip throughout the day rather than drinking large amounts at once. During exercise in heat, use sports drinks or oral rehydration solutions to replace electrolytes. Warning signs of dehydration include dark amber urine, dizziness, muscle cramps, and dry mouth — seek medical attention if these appear.</p>

<p><strong>Foods that thicken output:</strong> white rice, pasta, mashed potato, white bread, ripe banana, oat flakes, cottage cheese, applesauce, cheese.</p>

<p><strong>Foods with high blockage risk</strong> (introduce very carefully, chew well): raw apple with skin, celery, corn, mushrooms, nuts and seeds, popcorn, dried fruit, grapes. If there is no output for 6 hours together with cramping, nausea, or swelling around the stoma, seek emergency care immediately. Never take laxatives.</p>

<h5>With a colostomy</h5>
<p>The large intestine is partially present, so output is more formed. Aim for at least 2 litres of fluid per day. For constipation, increase water intake and eat more fibre. For loose stools, eat thickening foods (white rice, banana, boiled carrot) and avoid raw fruit, spicy food, and fat until stools firm up.</p>

<h5>Gas and odour (both ostomy types)</h5>
<p>Avoid carbonated drinks, beer, chewing gum, and swallowing air. Cabbage, onion, garlic, broccoli, and asparagus increase gas. Pouches with integrated charcoal filters manage odour effectively.</p>',
            ],
            [
                'slug'       => 'sport-i-fizicheska-aktivnost',
                'content_bg' => '<p>Повечето хора с остома могат да се върнат към спорт и физическа активност след възстановителния период след операцията. Стомата сама по себе си не пречи на физическото натоварване — с правилна подготовка, спортът е безопасен, полезен и напълно нормален.</p>

<h5>Кога да започнете</h5>
<p>Лекото ходене обикновено е разрешено в рамките на дни след операцията. По-интензивни активности — бягане, плуване, фитнес, колоездене — са възможни обичайно след 6 до 8 седмици, когато коремната рана е зараснала. Винаги следвайте конкретните указания на вашия хирург относно времето за възобновяване на различните активности.</p>

<h5>Безопасни спортове</h5>
<ul>
<li>Плуване — водата не уврежда стомата; повечето хора плуват комфортно с остома</li>
<li>Колоездене и ходене</li>
<li>Бягане</li>
<li>Йога и пилатес — с адаптации за избягване на силно интраабдоминално налягане в ранните месеци</li>
<li>Отборни спортове като футбол, баскетбол и тенис — повечето хора се връщат към тях без ограничения</li>
</ul>

<h5>Контактни спортове и тежко вдигане</h5>
<p>При контактни спортове — бойни изкуства, ръгби, хокей — носете защита за стомата (специализирана предпазна плочка), поставена под дрехите. Тя значително намалява риска от директен удар. При силово натоварване в първата година след операцията нараства рискът от парастомална херния — издутина около стомата, причинена от отслабване на коремната стена. Консултирайте се с ентеростомния терапевт относно поддържащ колан преди възобновяването на тежки тренировки.</p>

<h5>Профилактика на парасторналната херния</h5>
<p>Парасторналната херния засяга до 50% от хората в рамките на първата година след операцията. Основни мерки за профилактика: укрепване на мускулите на корема (работете с физиотерапевт за безопасни упражнения), поддържайте здравословно тегло и спрете тютюнопушенето. При кашляне, кихане или вдигане поддържайте правилна техника и избягвайте задържане на дъха.</p>

<h5>Практически съвети</h5>
<ul>
<li>Изпразнете торбичката преди тренировка</li>
<li>Носете прилепнало функционално облекло (лайкра шорти, атлетически колан) над торбичката</li>
<li>Пийте достатъчно течности — особено важно при илеостома и горещо</li>
<li>Носете резервни консумативи при тренировки далеч от дома</li>
</ul>',
                'title_en'   => 'Sport and Physical Activity with an Ostomy',
                'excerpt_en' => 'An ostomy is not a barrier to an active life. Which sports are safe, how to protect yourself, and what to watch out for.',
                'content_en' => '<p>Most people with an ostomy can return to sport and physical activity after the recovery period following surgery. The stoma itself does not prevent exercise — with the right preparation, physical activity is safe, beneficial, and entirely normal.</p>

<h5>When to start</h5>
<p>Light walking is usually possible within days of surgery. More demanding activity — running, swimming, gym work, cycling — is typically possible after six to eight weeks, once the abdominal wound has healed. Always follow your surgeon\'s specific guidance about when to resume different activities, as this varies depending on the type of surgery and individual recovery.</p>

<h5>Sports that are generally safe</h5>
<ul>
<li>Swimming — water does not harm the stoma, and most people find swimming comfortable and enjoyable with an ostomy</li>
<li>Cycling and walking</li>
<li>Running</li>
<li>Yoga and Pilates — with modifications to avoid strong intra-abdominal pressure in the early months</li>
<li>Team sports such as football, basketball, and tennis — most people return to these without lasting restrictions</li>
</ul>

<h5>Contact sports and heavy lifting</h5>
<p>Contact sports carry a risk of direct impact to the stoma. A stoma guard worn under clothing significantly reduces this risk and is recommended for martial arts, rugby, and similar sports. Heavy lifting in the first year after surgery increases the risk of a parastomal hernia — a bulge around the stoma caused by weakening of the abdominal wall. Consult your stoma care nurse about a support belt before returning to weightlifting or heavy physical work.</p>

<h5>Parastomal hernia prevention</h5>
<p>Parastomal hernia affects up to 50% of people within the first year after stoma creation. Key prevention measures include strengthening the abdominal muscles (work with a physiotherapist on exercises specifically designed for hernia prevention), maintaining a healthy weight, and stopping smoking. When coughing, sneezing, or lifting, use correct technique and avoid holding your breath.</p>

<h5>Practical tips</h5>
<ul>
<li>Empty the pouch before exercise to reduce weight and the risk of leaks</li>
<li>Wear fitted supportive clothing (Lycra shorts, athletic waistband) over the pouch</li>
<li>Stay well hydrated — especially important with an ileostomy and in hot weather</li>
<li>Carry spare supplies when exercising away from home</li>
</ul>',
            ],
        ];
    }
}
