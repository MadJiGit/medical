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
    name: 'app:update-blog-how-to-use-en',
    description: 'Update "How to Use" blog articles with English translations',
)]
class UpdateBlogHowToUseEnCommand extends Command
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

            $post->setTitleEn($data['title_en']);
            $post->setExcerptEn($data['excerpt_en']);
            $post->setContentEn($data['content_en']);

            $io->text(sprintf('Updated: %s', $data['title_en']));
        }

        $this->em->flush();
        $io->success('All English translations updated.');

        return Command::SUCCESS;
    }

    private function getArticles(): array
    {
        return [
            [
                'slug' => 'kak-pravilno-da-postavite-stomna-torbichka',
                'title_en' => 'How to Apply an Ostomy Pouch Correctly',
                'excerpt_en' => 'Changing your ostomy pouch may feel complicated at first. With the right technique and a little practice, it becomes routine. Here is a step-by-step guide.',
                'content_en' => '<p>It is completely normal to feel unsure during your first few changes. Most people with an ostomy say that after two or three weeks, the whole process happens almost automatically. The key is to follow the right order of steps.</p>

<h5>What you will need</h5>
<ul>
<li>A new pouch or baseplate with pouch</li>
<li>Warm water and a soft cloth, or specialist ostomy wipes</li>
<li>Scissors for trimming (if the baseplate is not pre-cut)</li>
<li>A stoma measuring guide</li>
<li>Hydrocolloid paste or a barrier ring if needed</li>
</ul>

<h5>Step 1: Prepare everything beforehand</h5>
<p>Lay out all your supplies before you remove the old pouch. That way you will not need to search for anything with paste-covered hands or a sticky baseplate in hand.</p>

<h5>Step 2: Remove the old pouch carefully</h5>
<p>Support the skin with one hand and peel slowly from top to bottom. Never pull sharply. If the adhesive sticks stubbornly, dampen the edges slightly with water or use a specialist adhesive remover.</p>

<h5>Step 3: Clean the skin</h5>
<p>Wash the skin around the stoma with warm water and a soft cloth. Do not use soap containing moisturisers, oils, or fragrance, as these leave a residue that prevents the adhesive from sticking. Light bleeding when cleaning the stoma itself is normal, as the tissue has a rich blood supply.</p>

<h5>Step 4: Dry thoroughly</h5>
<p>The skin must be completely dry before you apply the new baseplate. Moisture is the main reason pouches detach prematurely. Pat dry rather than rubbing.</p>

<h5>Step 5: Check the stoma</h5>
<p>Look at the skin around the stoma and the stoma itself. It should be pink to red in colour. If you notice any purple discolouration, darkening, or open sores, contact your doctor or a stoma care nurse.</p>

<h5>Step 6: Cut the opening</h5>
<p>Use the measuring guide to check your stoma size. The opening in the baseplate should be about 2 to 3 mm larger than the stoma, no more. If the gap is larger, the skin around the stoma is left unprotected and you risk irritation.</p>

<h5>Step 7: Apply the baseplate</h5>
<p>If you use paste or a barrier ring, apply it around the opening before sticking the baseplate down. Place the baseplate from the centre outward, pressing evenly without leaving air bubbles. Hold your palm over the baseplate for one to two minutes. The warmth from your hand helps the adhesive activate more effectively.</p>

<h5>When to seek help</h5>
<p>If you have persistent leaks, redness that does not clear up after two or three changes, or pain when changing, consult a stoma care nurse. These specialists deal with exactly these situations every day and can usually identify the problem in a single appointment.</p>',
            ],
            [
                'slug' => 'vidove-stomni-torbichki',
                'title_en' => 'Types of Ostomy Pouches: Which One is Right for You?',
                'excerpt_en' => 'One-piece or two-piece, drainable or closed — choosing the right system is personal and depends on your stoma type, lifestyle, and individual preferences.',
                'content_en' => '<p>When you first see all the available systems in a medical supply store, it can feel overwhelming. That is why it is important to understand the basic differences before deciding what to try.</p>

<h5>One-piece systems</h5>
<p>In a one-piece system, the baseplate and pouch are fused together. You apply them as a single unit and remove everything at once when you change. These systems are thinner, lie flatter against the body, and are less visible under clothing. They suit people who find click mechanisms difficult to manage with their hands, and those who prefer a simpler changing process.</p>

<h5>Two-piece systems</h5>
<p>Here the baseplate and pouch are separate parts connected by a flange or plastic coupling ring. The baseplate can stay in place for three to four days while the pouch is changed more frequently. This is convenient because you avoid peeling the baseplate off every day, which puts less strain on the skin. You can also switch to a smaller pouch for sport or more intimate situations.</p>

<h5>Drainable pouches</h5>
<p>These have an open end that is closed with a clip or velcro after emptying. You empty them as needed rather than replacing them every time they fill. They are suitable for an ileostomy or a colostomy with looser output, as they require fewer full changes.</p>

<h5>Closed pouches</h5>
<p>These have no openable end and are replaced entirely when full. They are used mainly with a colostomy that produces formed output, where emptying is predictable in frequency. Much simpler to handle, though they generate more waste.</p>

<h5>How to choose</h5>
<p>Your stoma type is the starting point. With an ileostomy, drainable pouches are almost always used. With a colostomy, the choice is broader. Your stoma care nurse is the best adviser, especially in the first months. Most manufacturers offer free samples, so you can try different systems before committing to one.</p>

<p>The system that works well for someone else with an ostomy is not necessarily right for you. Body shape, activity level, and stoma type make every situation different.</p>',
            ],
            [
                'slug' => 'kak-da-izmerite-stomata',
                'title_en' => 'How to Measure Your Stoma Correctly',
                'excerpt_en' => 'Accurate stoma measurement is the foundation of a good seal. The size changes after surgery, so measuring is a skill you need to learn early on.',
                'content_en' => '<p>The stoma is not a fixed size, especially in the first months after surgery. It is usually larger immediately after the operation and gradually shrinks over six to eight weeks. If you are using a baseplate cut a month ago, it may already be too large for the current size, leaving unprotected skin exposed.</p>

<h5>What you will need</h5>
<ul>
<li>A stoma measuring guide (provided by the manufacturer or your stoma care nurse)</li>
<li>A marker or pencil</li>
<li>Scissors with rounded tips</li>
<li>A mirror, if that makes it easier</li>
</ul>

<h5>Measuring a round stoma</h5>
<p>Place the measuring guide next to the stoma and find the circle that fits snugly around it without touching it. The opening in the baseplate should be about 2 to 3 mm larger than the base of the stoma, no more. If the opening is too large, the skin around the stoma comes into contact with output and becomes irritated.</p>

<h5>Measuring an oval stoma</h5>
<p>Some stomas are not round but slightly oval. In that case, measure the widest and narrowest points. Transfer both measurements onto the baseplate and cut an oval opening following the outline. If you find it difficult to cut correctly, your stoma care nurse can show you how.</p>

<h5>When is the best time to measure</h5>
<p>Morning is usually best. The stoma is less active and easier to measure. Avoid measuring immediately after eating, when it may be larger than usual.</p>

<h5>How often to measure</h5>
<p>In the first six to eight weeks after surgery, measure at every change because the size shifts as swelling reduces. Once the stoma has settled, check periodically — for example, after a significant change in body weight, or if you notice the pouch starting to leak sooner than usual.</p>

<h5>If you do not have a measuring guide</h5>
<p>In a pinch, you can use a coin or a piece of paper with a cut-out hole to gauge the size. A more lasting solution is to ask your nurse or the manufacturer of your current product for a measuring guide.</p>',
            ],
            [
                'slug' => 'smyana-na-torbichkata-kolko-chesto',
                'title_en' => 'How Often Should You Change Your Pouch?',
                'excerpt_en' => 'Not too rarely, not too often. Finding the right changing rhythm matters for skin health and peace of mind in daily life.',
                'content_en' => '<p>How often you change is something everyone adjusts to themselves over time. There are general guidelines, but the final answer depends on your stoma, the products you use, and your lifestyle.</p>

<h5>The general rule: every 3 to 5 days</h5>
<p>For most people with a colostomy or ileostomy, the baseplate can stay on for three to five days. Manufacturers recommend not exceeding seven days regardless of the product type. With two-piece systems, the pouch is emptied when it is about one-third to one-half full.</p>

<h5>When to change sooner</h5>
<ul>
<li>If you feel irritation or a burning sensation around the stoma</li>
<li>If you notice output or liquid between the baseplate and the skin</li>
<li>If the edges of the baseplate have started to lift</li>
<li>If you notice an unusual odour coming from the baseplate rather than the contents</li>
</ul>

<h5>Do not change too often</h5>
<p>Removing the baseplate daily disrupts the skin\'s protective barrier. The less often you peel off the adhesive, the healthier the skin around your stoma stays. If you find yourself changing every day because of leaks, the cause is usually in the cut size or the product, not the frequency.</p>

<h5>What affects wear time</h5>
<p>Warm and humid weather shortens wear time because perspiration softens the adhesive. Physical activity has the same effect. Diet also plays a role: with a stoma that produces looser output, the pouch fills faster and needs emptying more often.</p>

<h5>Find your own rhythm</h5>
<p>Most people settle on a preferred day and time for changing, usually in the morning before eating. Over time you get a feel for when a change is needed without checking the clock. This rhythm develops with practice and is different for everyone.</p>',
            ],
            [
                'slug' => 'adaptirane-na-osnovata',
                'title_en' => 'Adapting the Baseplate to Your Body Shape',
                'excerpt_en' => 'Uneven skin, scars, or a flush stoma make sealing harder. There are specific solutions for every situation, and most people find them with a little guidance from a specialist.',
                'content_en' => '<p>A flat baseplate works well when the stoma protrudes at least 6 to 7 mm above the skin surface and the surrounding skin is even. When things are not quite like that, leaks become more frequent and most people assume they are doing something wrong. In reality, they simply need a different type of baseplate or some additional accessories.</p>

<h5>Flat baseplate: when it works</h5>
<p>Suitable for a well-protruding stoma with relatively even surrounding skin. It conforms well to the natural movements of the abdomen and is thinner and more comfortable to wear.</p>

<h5>Convex baseplate: when it is needed</h5>
<p>Convex baseplates have a curved shape in the centre that presses the skin around the stoma and helps push the stoma outward. They are recommended when the stoma is flush or barely reaches the skin surface, when the surrounding skin is creased or uneven, and when you have frequent leaks with a flat baseplate. They come in soft and firm versions. Soft convex baseplates are more comfortable for everyday wear.</p>

<h5>Barrier rings and strips</h5>
<p>Thin hydrocolloid rings or strips are placed around the opening of the baseplate before application. They fill small irregularities and creases, create a more secure seal around the stoma, and extend wear time. Many people use them alongside a flat baseplate as a standard measure rather than only when there is a problem.</p>

<h5>Paste</h5>
<p>Hydrocolloid paste fills deeper irregularities, scars, and folds around the stoma. Apply it before the baseplate, let it dry for a minute or two, then stick the baseplate down. When used correctly, it significantly reduces the risk of leaks.</p>

<h5>Mouldable baseplates</h5>
<p>Newer products allow the opening to be shaped with your fingers rather than scissors. The material is flexible and adapts to the shape of the stoma. Many people find this easier than traditional cutting.</p>

<h5>Consult a stoma care nurse</h5>
<p>If you have regular leaks despite accurate measuring and correct application, booking an appointment with a stoma care nurse is the next step. These specialists see situations like yours every day and in most cases find a solution within a single visit.</p>',
            ],
            [
                'slug' => 'kak-da-poiskate-bezplatna-mostra',
                'title_en' => 'How to Request a Free Sample from a Manufacturer',
                'excerpt_en' => 'All major ostomy product manufacturers offer free samples. Try different systems before deciding which one works best for you.',
                'content_en' => '<p>Choosing an ostomy system is personal. A product that works well for someone else may not be right for you because of differences in body shape, stoma type, or lifestyle. That is why it makes sense to try several options before settling on one product long term.</p>

<h5>Why use samples</h5>
<p>When you buy a product without having tried it, you risk it being unsuitable. Pouches and baseplates are not cheap. A free sample lets you test real-world wear for a few days with no financial risk.</p>

<h5>How to request samples</h5>
<p>The three main manufacturers — Coloplast, Hollister, and Convatec — have dedicated patient programmes. You can request samples directly through their websites or by phone. You will usually need to provide your stoma type, approximate stoma size, and a delivery address.</p>

<h5>Your stoma care nurse is the best intermediary</h5>
<p>If you have regular contact with a stoma care nurse, they can obtain samples directly from the manufacturer for you. Many nurses keep a stock for exactly this purpose and can advise which products are appropriate for your specific situation before you even submit a request.</p>

<h5>What to keep in mind when testing</h5>
<ul>
<li>Wear the trial product for at least two to three full changes before evaluating it</li>
<li>Compare products under similar conditions (do not test one in hot summer weather and another in winter)</li>
<li>Note whether you experience leaks, skin irritation, or discomfort during wear</li>
<li>Ask for samples of accessories too: rings, paste, cleansing sprays</li>
</ul>

<h5>Try more than one product</h5>
<p>It is normal to try three or four different systems before finding the one that feels right. Manufacturers know this, which is why they offer samples. Do not rush into a decision simply because the first product "does the job".</p>',
            ],
            [
                'slug' => 'patuvane-s-ostoma',
                'title_en' => 'Travelling with an Ostomy: Practical Tips',
                'excerpt_en' => 'Having an ostomy does not have to limit your desire to travel. With a little preparation you can travel confidently, whether by car, plane, or ship.',
                'content_en' => '<p>Many people with an ostomy travel regularly, including on long international flights. The preparation is slightly different from usual, but no more complicated.</p>

<h5>How many supplies to take</h5>
<p>Take twice the amount you would normally need. Calculate how many baseplates and pouches you use per day, multiply by the number of days, then double it. Ostomy supplies can sometimes be hard to find abroad and the brand you use may not be available everywhere.</p>

<h5>How to split supplies for air travel</h5>
<p>Put half your supplies in your carry-on bag and half in your checked luggage. If your suitcase is lost or delayed, you will have enough to manage. Cutting scissors are permitted in carry-on luggage on most flights as long as the blade is under 10 centimetres. For international flights, check the specific rules of the airline beforehand to avoid surprises.</p>

<h5>Liquids and the 100 ml rule</h5>
<p>Cleansing sprays, paste, and barrier films all fall into the liquids category at airport security. The standard restriction is 100 ml per container in carry-on baggage. Medical supplies above this limit are permitted but must be declared at the checkpoint, where you should expect an additional check.</p>

<h5>At the security checkpoint</h5>
<p>You have the right to pass through without removing or showing your ostomy pouch. If the scanning device triggers an alert from the pouch, you can request a private screening in a separate room. It is advisable to carry a medical card or a letter from your doctor in English explaining the presence of an ostomy device. UOAA offers a free Travel Communication Card that you can download and print.</p>

<h5>More practical travel tips</h5>
<ul>
<li>Empty your pouch before leaving for the airport</li>
<li>Pack a change kit in your carry-on in case you need it during the flight</li>
<li>On long flights, choose an aisle seat for easier access to the toilet</li>
<li>Store supplies away from direct sunlight and heat in the car or hotel room</li>
</ul>

<h5>When travelling internationally</h5>
<p>If you are going away for longer or to a country where you do not speak the language, find the contact details in advance for a local ostomy organisation or a hospital experienced in stoma care. Many national organisations across Europe are connected to the International Ostomy Association and can help in an emergency.</p>',
            ],
        ];
    }
}
