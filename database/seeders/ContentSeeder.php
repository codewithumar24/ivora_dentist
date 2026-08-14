<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Review;
use App\Models\Role;
use App\Models\Subscriber;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\ContactMessage;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->testimonials();
        $this->faqs();
        $this->gallery();
        $this->blog();
        $this->reviews();
        $this->inbox();
    }

    protected function testimonials(): void
    {
        $rows = [
            ['Nadia Hussain', 'Teacher', 'Clear aligners', 5, 'I put off straightening my teeth for eleven years because I assumed it meant metal braces and a year of hiding my mouth. Dr Qureshi showed me the digital plan on the first visit and I was done in seven months.'],
            ['Rehan Malik', 'Software engineer', 'Root canal', 5, 'I have a genuine phobia of dentists. Dr Iqbal talked me through every single step before he did it, and stopped whenever I raised my hand. It was the first appointment I have left without shaking.'],
            ['Sara Ilyas', 'Mother of two', 'Child check-up', 5, 'My six-year-old asked when we could go back. That has never happened anywhere else.'],
            ['Asad Mahmood', 'Retired banker', 'Dental implant', 5, 'Three quotes, three different stories. Ivora was the only one that showed me the x-ray and explained why the other plan would not hold.'],
            ['Mahnoor Zaidi', 'Architect', 'Whitening', 4, 'Careful, unhurried, and no upselling. They actually talked me down from the more expensive option.'],
            ['Bilal Sattar', 'Restaurant owner', 'Wisdom tooth removal', 5, 'Out in twenty minutes, back at work the next day. The follow-up call the same evening was a nice touch.'],
            ['Anum Shafiq', 'Doctor', 'Scale and polish', 5, 'As a clinician myself I am fussy about cross-infection control. Their setup is genuinely excellent.'],
            ['Kashif Nawaz', 'Photographer', 'Veneers', 5, 'They made a wax-up first so I could see the shape before anything was cut. That is how it should be done everywhere.'],
        ];

        foreach ($rows as $i => [$name, $role, $treatment, $rating, $quote]) {
            Testimonial::updateOrCreate(['name' => $name], [
                'role' => $role,
                'treatment' => $treatment,
                'rating' => $rating,
                'quote' => $quote,
                'sort_order' => $i,
                'avatar' => sprintf(
                    'https://randomuser.me/api/portraits/%s/%d.jpg',
                    in_array($name[0], ['N', 'S', 'M', 'A']) && str_contains($role, 'Mother') ? 'women' : (in_array(explode(' ', $name)[0], ['Nadia', 'Sara', 'Mahnoor', 'Anum']) ? 'women' : 'men'),
                    random_int(1, 98)
                ),
            ]);
        }
    }

    protected function faqs(): void
    {
        $rows = [
            ['General', 'Do I need a referral to book?', 'No. Book any treatment directly through the site or by calling the front desk. We only ask for a referral letter if another clinician has already started work you want us to continue.'],
            ['General', 'How early should I arrive?', 'Ten minutes before a first visit so we can take your medical history properly. For a returning appointment, five minutes is plenty.'],
            ['General', 'What happens at a first appointment?', 'A full examination of teeth, gums, bite and soft tissue, digital x-rays if they are needed, then a written treatment plan with prices before anything is booked.'],
            ['Payments', 'Do you offer instalments?', 'Yes, on treatment plans over Rs 50,000. We split it across the course of treatment with no interest — ask the front desk to set it up.'],
            ['Payments', 'Which insurers do you work with?', 'We are on the panel for most major local health insurers. Bring your card and we will check your cover before treatment starts, not after.'],
            ['Payments', 'Can I get an itemised invoice?', 'Every invoice is itemised by default and available as a PDF in your patient portal the moment it is raised.'],
            ['Appointments', 'How do I cancel or move an appointment?', 'From your patient portal, up to four hours before the visit. After that please call us so we can offer the slot to someone else.'],
            ['Appointments', 'Do you see emergencies?', 'Yes. Call the out-of-hours line and we will fit you in the same day wherever possible. Genuine trauma and swelling always take priority over routine work.'],
            ['Appointments', 'Will I get a reminder?', 'An email and an SMS the day before, plus one at the time of booking. You can reply to the SMS to release the slot if plans change.'],
            ['Treatments', 'Does whitening damage enamel?', 'Professionally supervised whitening at the concentrations we use does not soften or thin enamel. Sensitivity for a day or two is common and settles on its own.'],
            ['Treatments', 'Are root canals painful?', 'The procedure itself is done under local anaesthetic and feels much like having a filling. Most of the pain people associate with root canals is the infection beforehand, not the treatment.'],
            ['Treatments', 'How long do implants last?', 'With good hygiene and regular reviews, twenty years and beyond is realistic. Smoking and untreated gum disease are the two things that shorten that most.'],
        ];

        foreach ($rows as $i => [$category, $question, $answer]) {
            Faq::updateOrCreate(['question' => $question], [
                'answer' => $answer,
                'category' => $category,
                'sort_order' => $i,
            ]);
        }
    }

    protected function gallery(): void
    {
        $rows = [
            ['Reception and waiting area', 'Clinic', 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=1200&q=80'],
            ['Treatment room one', 'Clinic', 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1200&q=80'],
            ['Digital imaging suite', 'Clinic', 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=1200&q=80'],
            ['Sterilisation room', 'Clinic', 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=1200&q=80'],
            ['Children\'s room', 'Clinic', 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=1200&q=80'],
            ['Composite bonding, upper anteriors', 'Before & after', 'https://images.unsplash.com/photo-1595003500447-88ec6c6b1b06?w=1200&q=80'],
            ['Aligner case, seven months', 'Before & after', 'https://images.unsplash.com/photo-1601991115814-e7aa6b58d24d?w=1200&q=80'],
            ['Whitening, three shades', 'Before & after', 'https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?w=1200&q=80'],
            ['The team at the 2025 open day', 'Team', 'https://images.unsplash.com/photo-1666214280557-f1b5022eb634?w=1200&q=80'],
            ['Hygiene appointment in progress', 'Team', 'https://images.unsplash.com/photo-1598256989800-fe5f95da9787?w=1200&q=80'],
            ['Consultation room', 'Clinic', 'https://images.unsplash.com/photo-1583324113626-70df0f4deaab?w=1200&q=80'],
            ['Implant planning on screen', 'Team', 'https://images.unsplash.com/photo-1571772996211-2f02c9727629?w=1200&q=80'],
        ];

        foreach ($rows as $i => [$title, $category, $image]) {
            Gallery::updateOrCreate(['title' => $title], [
                'category' => $category,
                'image' => $image,
                'sort_order' => $i,
                'caption' => $title,
            ]);
        }
    }

    protected function blog(): void
    {
        $categories = [
            ['Prevention', 'jade'],
            ['Treatments explained', 'iris'],
            ['Children', 'mint'],
            ['Clinic news', 'pine'],
        ];

        foreach ($categories as [$name, $colour]) {
            PostCategory::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'colour' => $colour]);
        }

        $author = User::where('email', 'owner@ivoradental.com')->first()
            ?? User::whereRelation('role', 'slug', Role::OWNER)->first();

        $posts = [
            [
                'Prevention', 'Flossing is not optional, and here is the boring reason why',
                'Brushing reaches roughly three of the five surfaces on every tooth. The two it misses are where most adult decay actually starts.',
                'A toothbrush, however good, cannot get between two teeth that are touching. That contact point is exactly where plaque sits undisturbed, and it is where the majority of adult interproximal decay begins.

The practical version: any interdental cleaning is better than none. Floss works if you use it properly, but most people do not, and interdental brushes are far more forgiving. Pick the largest size that slides in without force, use it once a day, and do it in front of the television rather than standing over the sink at midnight — the habit sticks far better when it is not competing with sleep.

If your gums bleed for the first week, that is inflammation, not damage. It settles. If it is still bleeding after two weeks of daily cleaning, book a hygiene appointment, because something else is going on.', 5,
            ],
            [
                'Treatments explained', 'What actually happens during a root canal',
                'The reputation comes from the infection, not the appointment. Here is the procedure, step by step, with nothing left out.',
                'People arrive braced for the worst, and almost all of them leave saying it was easier than the fortnight of toothache that preceded it.

The tooth is numbed and isolated with a rubber dam, which keeps the working area dry and stops anything reaching the back of your throat. A small access cavity is made through the biting surface. The infected pulp is removed, and the canals are cleaned and shaped with fine rotary instruments under magnification.

Between visits, if a second appointment is needed, the tooth is dressed with an antibacterial medicament and sealed temporarily. At the final visit the canals are filled with gutta-percha and sealed. Most back teeth then need a crown, because a root-treated molar has lost a lot of structure and is prone to splitting.

Discomfort for two or three days afterwards is normal and responds to ibuprofen. Severe pain, swelling, or a bad taste is not normal — call us.', 6,
            ],
            [
                'Children', 'Your child\'s first dental visit should be boring',
                'The best first appointment involves no instruments at all. A ride in the chair, a count of the teeth, and out.',
                'Bring your child at around their first birthday, or when their first tooth appears, whichever is sooner. Not because there is anything to treat, but because the room needs to become ordinary before it needs to become useful.

A first visit here lasts about ten minutes. The child sits in the chair, usually on a parent\'s lap for the youngest. We count the teeth out loud, look at the gums, and talk to the parent about diet and brushing. That is it.

Two things that help more than anything else: never use the dentist as a threat, and do not tell a child it will not hurt. The first makes the room frightening before they arrive, the second introduces a word they had not thought of.', 4,
            ],
            [
                'Prevention', 'Sensitive teeth: when it is nothing, and when it is not',
                'A twinge with cold water is usually exposed dentine. Lingering pain after the cold has gone is a different problem.',
                'Dentine hypersensitivity is common and rarely serious. Enamel wears at the neck of the tooth, or the gum recedes, and the tubules underneath are exposed. Cold, sweet or acidic things trigger a sharp, brief twinge that stops within a second or two of removing the stimulus.

The fix is usually undramatic: a soft brush, a lighter hand, a desensitising toothpaste used as an ointment rather than a paste — rub it in and do not rinse.

The version to worry about is different in character. Pain that lingers for thirty seconds or more after the cold has gone, wakes you at night, or is hard to localise suggests the pulp is inflamed rather than the dentine irritated. That does not settle on its own. Book an appointment rather than waiting it out.', 5,
            ],
            [
                'Clinic news', 'We have added Saturday surgical lists',
                'From this month, oral surgery runs on the first and third Saturday of every month.',
                'Demand for wisdom tooth and implant appointments has outstripped our weekday theatre time for most of this year, and the honest answer to "when can you fit me in" had drifted past six weeks.

From this month, Dr Ahsan runs a surgical list on the first and third Saturday of each month, 10am to 4pm. Those slots are bookable online in the usual way.

If you are already on the waiting list, the front desk is working through it in order and will call you before the slots go public.', 3,
            ],
            [
                'Treatments explained', 'Aligners versus fixed braces: the honest comparison',
                'Aligners are not always faster, and fixed braces are not always more visible. What actually matters is the movement required.',
                'The marketing around clear aligners has been loud enough that patients now arrive asking for them by name, sometimes for cases where they are the wrong tool.

Aligners are excellent for mild to moderate crowding, spacing, and relapse after previous treatment. They are removable, so hygiene is easier, and there are no emergency appointments for a poking wire. They also demand discipline: twenty-two hours a day, every day, or the plan slips.

Fixed appliances still win for significant rotations, large vertical movements, and cases where compliance is realistically going to be a problem. Ceramic brackets are far less visible than the metal most people picture.

The right answer comes out of the scan and the x-rays, not out of a preference formed before the consultation.', 6,
            ],
        ];

        foreach ($posts as $i => [$category, $title, $excerpt, $body, $minutes]) {
            Post::updateOrCreate(['slug' => Str::slug($title)], [
                'post_category_id' => PostCategory::where('slug', Str::slug($category))->value('id'),
                'author_id' => $author?->id,
                'title' => $title,
                'excerpt' => $excerpt,
                'body' => $body,
                'read_minutes' => $minutes,
                'status' => 'published',
                'views' => random_int(120, 3200),
                'published_at' => now()->subDays(($i + 1) * 9),
                'cover_image' => [
                    'https://images.unsplash.com/photo-1606265752439-1f18756aa8ed?w=1400&q=80',
                    'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1400&q=80',
                    'https://images.unsplash.com/photo-1581585504215-4b7e04ba4dd5?w=1400&q=80',
                    'https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?w=1400&q=80',
                    'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=1400&q=80',
                    'https://images.unsplash.com/photo-1601991115814-e7aa6b58d24d?w=1400&q=80',
                ][$i],
                'meta_description' => $excerpt,
            ]);
        }
    }

    protected function reviews(): void
    {
        $bodies = [
            'Explained everything before starting. No surprises on the invoice either.',
            'Quick, calm and gentle. I have never said that about a dental appointment before.',
            'Ran fifteen minutes late but they called ahead to tell me, which I appreciated.',
            'The hygienist was thorough without being rough. Booked my next one on the way out.',
            'Second opinion that saved me an extraction. Very glad I came.',
        ];

        $completed = Appointment::where('status', Appointment::COMPLETED)
            ->inRandomOrder()->take(40)->get();

        foreach ($completed as $appointment) {
            Review::updateOrCreate(
                ['appointment_id' => $appointment->id, 'patient_id' => $appointment->patient_id],
                [
                    'doctor_id' => $appointment->doctor_id,
                    'rating' => random_int(4, 5),
                    'title' => null,
                    'body' => $bodies[array_rand($bodies)],
                    'is_approved' => random_int(0, 4) > 0,
                    'is_featured' => random_int(0, 6) === 0,
                ]
            );
        }

        foreach (\App\Models\Doctor::all() as $doctor) {
            $doctor->refreshRating();
        }
    }

    protected function inbox(): void
    {
        $messages = [
            ['Zara Kamal', 'zara.kamal@example.com', '+923005550101', 'Aligner consultation', 'Hello — I would like to know how much a full aligner course costs and whether the consultation fee comes off the total if I go ahead.'],
            ['Ahmed Sultan', 'ahmed.sultan@example.com', '+923005550102', 'Emergency — broken tooth', 'I broke a molar on Friday evening. It is not painful but it is very sharp against my tongue. Do you have anything on Monday?'],
            ['Farida Bano', 'farida.bano@example.com', null, 'Insurance panel', 'Are you on the Jubilee Health panel? My employer changed provider in January and I do not want to be caught out again.'],
            ['Hassan Raza', 'hassan.raza@example.com', '+923005550104', 'Parking', 'Is there parking at the Zamzama building or should I plan to park on the street? Coming with my mother who cannot walk far.'],
            ['Sundas Ali', 'sundas.ali@example.com', '+923005550105', 'Records transfer', 'I have been seeing another clinic for two years. What do you need from them before my first appointment with you?'],
        ];

        foreach ($messages as $i => [$name, $email, $phone, $subject, $body]) {
            ContactMessage::updateOrCreate(['email' => $email, 'subject' => $subject], [
                'name' => $name,
                'phone' => $phone,
                'message' => $body,
                'status' => $i < 3 ? 'new' : 'read',
                'created_at' => now()->subDays($i),
            ]);
        }

        foreach (User::patients()->take(14)->get() as $patient) {
            Subscriber::updateOrCreate(['email' => $patient->email], ['name' => $patient->name]);
        }
    }
}
