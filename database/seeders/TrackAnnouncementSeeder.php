<?php

namespace Database\Seeders;

use App\Models\Track;
use Illuminate\Database\Seeder;

class TrackAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title' => 'Segums pēc lietus',
                'body' => 'Pēc nesenajām lietavām trases segums ir ļotislapjš un mīksts',
                'is_pinned' => false,
                'active_days' => 2,
            ],
            [
                'title' => 'Trase uz laiku slēgta',
                'body' => 'Trase apkopes darbu dēļ pašlaik ir slēgta. Paziņosim, tiklīdz tā atkal būs pieejama.',
                'is_pinned' => true,
                'active_days' => 3,
            ],
            [
                'title' => 'Trases līdzināšanas darbi',
                'body' => 'Šodien trase slēgta līdzināšanas darbu dēļ.',
                'is_pinned' => true,
                'active_days' => 1,
            ],
            [
                'title' => 'Trase sagatavota treniņiem',
                'body' => 'Trases segums ir sakopts, un trase ir pieejama treniņiem.',
                'is_pinned' => false,
                'active_days' => 4,
            ],
            [
                'title' => 'Mainīgi seguma apstākļi',
                'body' => 'Atsevišķās trases vietās segums ir mīksts.',
                'is_pinned' => false,
                'active_days' => 2,
            ],
            [
                'title' => 'Rezervēta trase',
                'body' => 'Trasē plānots kopīgs treniņš. Pirms došanās ceļā sazinieties par pieejamību.',
                'is_pinned' => false,
                'active_days' => 1,
            ],
            [
                'title' => 'Trasē veiktas izmaiņas',
                'body' => 'Lūdzu, ievērojiet trases marķējumus un norādes.',
                'is_pinned' => false,
                'active_days' => null,
            ],
        ];

        foreach (Track::whereNotNull('user_id')->orderBy('slug')->get() as $track) {
            if ($track->announcements()->exists()) {
                continue;
            }

            $announcementCount = random_int(0, 1);
            $selectedTemplates = $templates;
            shuffle($selectedTemplates);

            foreach (array_slice($selectedTemplates, 0, $announcementCount) as $template) {
                $publishedAt = now()->subMinutes(random_int(0, 1440));

                $track->announcements()->create([
                    'user_id' => $track->user_id,
                    'title' => $template['title'],
                    'body' => $template['body'],
                    'published_at' => $publishedAt,
                    'expires_at' => $template['active_days'] === null
                        ? null
                        : now()->addDays($template['active_days']),
                    'is_pinned' => $template['is_pinned'],
                ]);
            }
        }
    }
}
