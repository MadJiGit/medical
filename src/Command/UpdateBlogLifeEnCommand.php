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
    name: 'app:update-blog-life-en',
    description: 'Update "Life with Ostomy" blog articles with EN translations',
)]
class UpdateBlogLifeEnCommand extends Command
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
        $io->success('All Life with Ostomy articles updated with EN translations.');

        return Command::SUCCESS;
    }

    private function getArticles(): array
    {
        return [
            // Article 1 — BG content already exists, only EN needed
            [
                'slug'       => 'zhivota-sled-operatsia-pyrvite-sedmitsi',
                'title_en'   => 'Life After Surgery — The First Weeks',
                'excerpt_en' => 'The adjustment period is difficult but temporary. Here is what to expect physically and emotionally in the weeks after your ostomy surgery.',
                'content_en' => '<p>The first weeks after ostomy surgery are both physically and emotionally demanding. It is entirely normal to feel confused, sad, overwhelmed, or anxious — these reactions are experienced by almost everyone who goes through this operation. Understanding what lies ahead makes the early period easier to navigate.</p>

<h5>Physical adjustment</h5>
<p>Your body needs time to recover from major surgery. In the first six to eight weeks:</p>
<ul>
<li><strong>Stoma size changes</strong> — the stoma will be swollen immediately after surgery and gradually shrinks to its permanent size over approximately six weeks. Measure regularly and adjust your baseplate opening accordingly.</li>
<li><strong>Irregular output</strong> — the bowel needs time to settle into a new routine. Unpredictable timing and consistency in the early weeks is completely normal.</li>
<li><strong>Fatigue</strong> — tiredness after major surgery is expected and can persist for several weeks. Rest when your body asks for it and do not push beyond your current capacity.</li>
<li><strong>Wound healing</strong> — pain and discomfort around the incision diminish gradually. Follow your surgeon\'s instructions about activity restrictions while the wound heals.</li>
</ul>

<h5>Emotional adjustment</h5>
<p>Many people describe a period of grief following ostomy surgery — mourning a sense of "normality" that has changed. This is a healthy and recognised psychological response. The UOAA and stoma care nurses consistently report that this adjustment phase, while difficult, resolves for the vast majority of people within weeks to months as practical confidence builds.</p>
<p>What helps during this period:</p>
<ul>
<li>Talking openly with people close to you rather than withdrawing</li>
<li>Connecting with others who have been through the same experience — ostomy support groups provide a perspective that clinical professionals cannot</li>
<li>Focusing on small practical milestones rather than the long term</li>
<li>Seeking professional psychological support early if distress is severe or persistent</li>
</ul>

<h5>Practical priorities in the first weeks</h5>
<ol>
<li>Learn the pouch change routine thoroughly before leaving hospital — do not go home until you feel confident performing it independently</li>
<li>Wear loose clothing without a waistband pressing on the stoma</li>
<li>Keep backup supplies within reach at all times — at home, in your bag, in the car</li>
<li>Write down the contact number of your stoma care nurse and use it if anything concerns you</li>
<li>Attend follow-up appointments — the stoma care nurse assesses your technique, equipment fit, and skin condition and makes adjustments that significantly reduce problems</li>
</ol>

<p>Remember: millions of people worldwide live full, active lives with an ostomy. The adjustment takes time, but it comes — and it comes sooner than most people expect in those difficult early days.</p>',
            ],
            // Articles 2–6 — BG content to be added in a separate command
            [
                'slug'       => 'kak-da-kazhete-na-blizkite',
                'title_en'   => 'How to Tell the People You Love',
                'excerpt_en' => 'Talking to your partner, children, and friends about your ostomy can feel daunting. Here is how to approach it.',
                'content_en' => '<p>One of the most common concerns people have after ostomy surgery is not the practical management of the pouch — it is the conversation with people they care about. When to tell someone, what to say, and how much to share are deeply personal decisions. There is no single right approach, but there are principles that consistently make these conversations go better.</p>

<h5>You decide who knows and when</h5>
<p>Disclosure is entirely your choice. You are not obligated to tell anyone beyond those directly involved in your care. Many people find that telling close family and friends early — while still in hospital or shortly after — reduces the mental weight of keeping a secret and allows their support network to be genuinely helpful. Others prefer to manage privately until they feel confident, then share selectively. Both approaches are valid.</p>

<h5>Talking to a partner</h5>
<p>Concern about a partner\'s reaction is one of the most significant emotional burdens people carry after ostomy surgery. Research consistently shows that partners\' actual responses are far more accepting than people fear in advance. Most partners describe their primary concern as their loved one\'s health and wellbeing — not the pouch.</p>
<p>Practical suggestions:</p>
<ul>
<li>Choose a calm moment without time pressure, not when either of you is tired or stressed</li>
<li>Be straightforward — explain what the ostomy is, why it was necessary, and what daily life looks like</li>
<li>Give your partner time to ask questions and process the information</li>
<li>If intimacy feels uncertain, acknowledge it directly — most couples find their way through this with open communication and patience</li>
</ul>

<h5>Talking to children</h5>
<p>Children handle this kind of information better than adults usually expect, particularly when it is explained calmly and matter-of-factly. Adapting the explanation to the child\'s age helps:</p>
<ul>
<li><strong>Young children (under 7)</strong>: "I had an operation and now my body works a little differently. I have a special bag that collects waste. I am fine and it doesn\'t hurt."</li>
<li><strong>Older children and teenagers</strong>: a more complete explanation is appropriate, including that the surgery was necessary and has made the parent healthier</li>
</ul>
<p>Children tend to follow the emotional lead of adults. A calm, matter-of-fact tone communicates that this is manageable, which is the most important message.</p>

<h5>Talking to friends and colleagues</h5>
<p>You may choose to tell close friends and say nothing to acquaintances — or the reverse. A simple explanation works well: "I had surgery and I now use a medical pouch." You do not need to elaborate beyond what you are comfortable sharing. Most people respond with curiosity and care rather than the discomfort that is often feared.</p>

<h5>When reactions are difficult</h5>
<p>Occasionally, someone responds with discomfort, inappropriate questions, or awkwardness. This is almost always about their own unfamiliarity with medical topics rather than any reflection on you. The UOAA and Coloplast both note that negative reactions from close relationships are uncommon — and that the vast majority of people who feared a difficult response found the opposite. If you are struggling with how to approach specific relationships, a stoma care nurse or ostomy support group can offer practical guidance from lived experience.</p>',
            ],
            [
                'slug'       => 'vrashane-kam-rabotata',
                'title_en'   => 'Returning to Work After Surgery',
                'excerpt_en' => 'When and how to return to work, what adjustments to request, and how to manage the working day with an ostomy.',
                'content_en' => '<p>Most people with an ostomy return to work successfully — including to physically demanding jobs. The timeline and practical adjustments depend on the type of work, the individual\'s recovery, and the nature of the surgery. Planning ahead reduces the anxiety that often surrounds this transition.</p>

<h5>When can you return?</h5>
<p>Recovery timelines vary, but general guidelines are:</p>
<ul>
<li><strong>Office and sedentary work</strong>: typically four to six weeks after surgery, once fatigue has resolved and the pouch routine is established</li>
<li><strong>Active or standing roles</strong> (retail, teaching, healthcare): six to eight weeks, depending on physical stamina and wound healing</li>
<li><strong>Heavy manual labour or lifting</strong>: eight to twelve weeks minimum, and a phased return is strongly recommended; heavy lifting permanently increases the risk of parastomal hernia</li>
</ul>
<p>Your surgeon and stoma care nurse are the right people to advise on your specific situation. A phased return — starting with part-time hours and building up — is easier on the body and often reduces anxiety about the first days back.</p>

<h5>Practical workplace adjustments</h5>
<p>A few simple adjustments make the working day significantly more comfortable:</p>
<ul>
<li><strong>Toilet access</strong>: identify where the nearest accessible toilet is and establish that you can use it without restriction. For most office environments this is not an issue; in settings with controlled toilet access, a brief conversation with a manager or HR resolves it quickly.</li>
<li><strong>Time for pouch emptying</strong>: emptying takes approximately five minutes and is done as needed, typically every four to six hours for an ileostomy and less frequently for a colostomy. This is manageable within normal break patterns for most roles.</li>
<li><strong>A small supply kit</strong>: keep a discreet pouch at your desk or in a locker with spare supplies — a spare pouch, wipes, disposal bag, and a change of underwear. Most people never need this at work, but having it removes background anxiety entirely.</li>
<li><strong>Clothing</strong>: structured clothing without a tight waistband pressing on the stoma is more comfortable for long workdays</li>
</ul>

<h5>Disclosure at work</h5>
<p>You are under no obligation to tell your employer or colleagues about your ostomy. The decision is entirely personal. Many people manage their ostomy entirely privately and their colleagues are never aware. If your role involves physical requirements that need adjustment, a conversation with HR — without necessarily using the word "ostomy" — about a temporary medical restriction is usually sufficient. The NHS and UOAA both note that most employers are more accommodating than employees expect when approached professionally.</p>

<h5>If difficulties arise</h5>
<p>If fatigue, anxiety, or physical demands make the return to work harder than expected, this is common and addressable. Speaking to your GP about a longer phased return, or adjusting working hours temporarily, is a legitimate and frequently used option. Your stoma care nurse can also provide a supporting letter for occupational health if adjustments to your role are genuinely needed.</p>',
            ],
            [
                'slug'       => 'intimnost-i-vzaimootnoshenia',
                'title_en'   => 'Intimacy and Relationships with an Ostomy',
                'excerpt_en' => 'Intimacy after ostomy surgery is possible and common. Practical advice for maintaining closeness and navigating new relationships.',
                'content_en' => '<p>Concerns about intimacy and body image are among the most significant emotional challenges after ostomy surgery — and among the least openly discussed. The practical reality, well documented in patient research by Hollister and Coloplast, is that the great majority of people do return to satisfying intimate relationships. Getting there often requires time, open communication, and adjusting expectations during recovery.</p>

<h5>Physical recovery first</h5>
<p>Sexual activity is generally not recommended for six to eight weeks after surgery, while the abdominal wound heals and physical stamina returns. Your surgeon will advise on the specific timeline for your case. Some procedures — particularly those involving the rectum — can cause temporary or permanent changes to sexual function, which your surgeon should discuss with you directly before and after the operation.</p>

<h5>Body image and confidence</h5>
<p>Changes in body image are normal and expected after any significant surgery. Many people describe an initial period of self-consciousness about the pouch. This typically diminishes as the practical routine becomes habitual and confidence builds. What helps:</p>
<ul>
<li>Familiarising yourself with your pouch and how it sits under different clothing</li>
<li>Choosing underwear or intimacy wraps designed for ostomy wearers — these provide coverage and security and are widely available</li>
<li>Recognising that your partner\'s perception of your body is, in almost all cases, far more accepting than your own self-assessment in the early weeks</li>
</ul>

<h5>Practical preparation for intimacy</h5>
<ul>
<li>Empty the pouch beforehand — a flatter, lighter pouch is more comfortable and provides greater confidence</li>
<li>Ensure the baseplate seal is secure — changing shortly before if needed</li>
<li>Pouch covers or wraps keep the pouch close to the body and reduce movement; many people find these increase comfort and confidence significantly</li>
<li>Avoid positions that put direct pressure on the stoma or cause the baseplate to lift</li>
<li>Wearing a fitted vest or intimate wrap over the pouch is a simple and effective option</li>
</ul>

<h5>Communicating with a partner</h5>
<p>Honest, calm communication is more effective than avoidance. Telling a partner what is comfortable, what feels uncertain, and what you need — even before physical intimacy resumes — removes the unspoken anxiety that both people often carry. Most partners describe their primary concern as their loved one\'s wellbeing, not the pouch. The conversation, once started, is almost always less difficult than expected.</p>

<h5>New relationships</h5>
<p>When to tell a new partner about an ostomy is a question many people spend considerable time on. There is no rule. The UOAA advises that disclosure before physical intimacy — when you feel the relationship has enough trust — generally works better than discovery. Most people find that a matter-of-fact, brief explanation is received far better than the anxiety they had built around the conversation. Partners who respond badly to a calm, honest disclosure are showing something about themselves, not about you.</p>

<h5>When to ask for help</h5>
<p>If concerns about intimacy or body image are significantly affecting your quality of life or relationship, a stoma care nurse, psychologist, or couples counsellor with experience in chronic illness can help. This is not an unusual request — it is a recognised and important part of full recovery from ostomy surgery.</p>',
            ],
            [
                'slug'       => 'psihologicheska-podkrepa',
                'title_en'   => 'Psychological Support — Where to Find Help',
                'excerpt_en' => 'Emotional wellbeing matters as much as physical recovery. Support groups, counsellors, and online communities for people with an ostomy.',
                'content_en' => '<p>The emotional impact of ostomy surgery is well documented and significant. Depression, anxiety, social withdrawal, and body image distress are common — not signs of weakness or failure to cope, but recognised psychological responses to major physical change. Getting the right support early makes a measurable difference to long-term quality of life.</p>

<h5>What you might experience</h5>
<p>It is normal to feel a broad range of difficult emotions in the weeks and months after surgery:</p>
<ul>
<li><strong>Grief</strong> — a sense of loss for your previous body and way of life. This is a recognised grief response and resolves with time and support for most people.</li>
<li><strong>Anxiety</strong> — about leaks, odour, social situations, intimacy, or work. This is almost universally reported in the early period and decreases significantly as practical confidence builds.</li>
<li><strong>Depression</strong> — persistent low mood, loss of interest in activities, and difficulty imagining a full life ahead. This is more serious and warrants professional support.</li>
<li><strong>Social withdrawal</strong> — avoiding situations due to fear of embarrassment. This is very common early on and becomes a significant problem if it persists long-term.</li>
</ul>
<p>Research cited by the American Cancer Society shows that people who access psychological support early recover quality of life faster than those who manage alone.</p>

<h5>Your stoma care nurse</h5>
<p>The stoma care nurse is often the most accessible and most valuable source of support in the early period. Concerns about leaks or skin problems that are causing anxiety are practical problems with practical solutions — and resolving them removes much of the underlying stress. Do not hesitate to contact your stoma care nurse if you are struggling. This is a core part of their role, not an imposition.</p>

<h5>Ostomy support groups</h5>
<p>Peer support from people living with an ostomy provides something that healthcare professionals cannot: direct, lived experience. Hearing from someone who has navigated the same fears and found their way through is consistently described by patients as one of the most reassuring things in recovery.</p>
<ul>
<li><strong>UOAA (United Ostomy Associations of America)</strong> — maintains a directory of affiliated support groups across the United States and resources for online connection: ostomy.org</li>
<li><strong>Hollister and Coloplast</strong> both offer peer support programmes connecting new ostomy patients with trained volunteers who have lived experience</li>
<li><strong>Online communities</strong> — forums and social media groups for people with an ostomy are active and supportive; they are particularly valuable for people who do not have local group access or prefer anonymity</li>
</ul>

<h5>Professional psychological support</h5>
<p>If depression, severe anxiety, or social withdrawal is significantly affecting daily life — particularly beyond three to four months after surgery — professional support from a psychologist or counsellor is appropriate and effective. Cognitive behavioural therapy (CBT) has a strong evidence base for health-related anxiety and adjustment disorders. Ask your GP for a referral, or ask your stoma care nurse — many specialist centres have access to clinical psychologists as part of the multidisciplinary team.</p>

<h5>What does not help</h5>
<p>Isolation and avoidance consistently make psychological adjustment worse. Staying at home, refusing social invitations, and avoiding situations that feel uncertain prevents the confidence-building experiences that are the main driver of long-term adjustment. The goal is gradual re-engagement — not forcing yourself into overwhelming situations, but not letting avoidance shrink your world either.</p>',
            ],
            [
                'slug'       => 'ostoma-i-sotsialen-zhivot',
                'title_en'   => 'Ostomy and Social Life — Without Limits',
                'excerpt_en' => 'Restaurants, events, travel, and beaches — with the right preparation, life continues fully. Practical advice for confident social living.',
                'content_en' => '<p>Social confidence after ostomy surgery develops gradually — but it does develop. The fear of social situations that most people experience in the early weeks is not a permanent condition. It is the normal anxiety that precedes experience, and it diminishes with each situation successfully navigated. The practical reality, consistently reported by people with long-term ostomies, is that the restrictions on social life are far fewer than anticipated.</p>

<h5>Eating out</h5>
<p>There are no foods that are universally forbidden with an ostomy, but some choices are more predictable than others. In the early months, getting to know your own dietary patterns makes eating out easier. Practical steps:</p>
<ul>
<li>Empty the pouch before leaving home and identify where the toilet is when you arrive</li>
<li>Choose foods you have already established are well tolerated for meals where you want predictability</li>
<li>Gas-producing foods (carbonated drinks, beer, some vegetables) are manageable at home but worth moderating at formal dinners or events</li>
<li>With time, most people find they can eat freely and socially without restriction</li>
</ul>

<h5>Social events and gatherings</h5>
<p>Carrying a small, discreet supply kit — a spare pouch, wipes, and disposal bag in a cosmetic bag — removes the background anxiety that many people describe. Knowing that any situation can be managed, even if a leak occurs, means most people stop thinking about it. Within a year of surgery, the majority of people report that the ostomy no longer meaningfully affects their social life.</p>

<h5>Beaches and swimming pools</h5>
<p>Swimming with an ostomy is safe and widely practised. Water does not damage the stoma. Practical preparation:</p>
<ul>
<li>Empty the pouch before entering the water</li>
<li>Waterproof tape strips around the baseplate edges provide extra security for extended swimming</li>
<li>One-piece swimsuits, swim shorts with a higher waist, or dedicated ostomy swimwear conceal the pouch effectively — many people report that no one notices anything</li>
<li>After swimming, pat the area dry and check the seal</li>
</ul>

<h5>Travel</h5>
<p>Air travel with an ostomy is straightforward with basic preparation. Security scanners may detect the pouch, but a brief explanation to security staff is usually sufficient — many airports have private screening available on request. For all travel:</p>
<ul>
<li>Carry at least twice your usual supply of ostomy materials in carry-on luggage — not checked bags, which can be lost</li>
<li>A medical letter from your doctor explaining the ostomy and the necessity of carrying supplies is useful for security and customs, particularly in countries where you do not speak the language</li>
<li>Research toilet access at your destination; in most European and North American cities, access is not a significant problem</li>
<li>For long flights, empty the pouch before boarding and plan pouch emptying during the flight rather than waiting</li>
</ul>

<h5>Building confidence</h5>
<p>Confidence in social situations comes from accumulated experience, not from preparation alone. The first time back at a restaurant, the first swim, the first long journey — each one demonstrates that the situation is manageable and reduces anxiety for the next. People who engage with their normal social life earlier, despite the discomfort of initial uncertainty, consistently report faster adjustment than those who wait until they feel "ready." Ready tends to come after the experience, not before it.</p>',
            ],
        ];
    }
}
