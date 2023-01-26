<?php

namespace App\Filament\Resources\NewsletterResource\Widgets;

use App\Enums\NewsletterStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\DoughnutChartWidget;
use function PHPUnit\Framework\containsIdentical;

class NewsletterSpamReportChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Spam Report';

    protected static ?array $options = [
        'tooltips' => [
            'callbacks' => [
                'label' => 'showToolTip'
            ],
        ],
    ];
    
    public ?Model $record = null;

    protected function getPollingInterval(): ?string
    {
        if ($this->record->status === NewsletterStatusEnum::Finished->name) {
            return null;
        }

        return '15s';
    }
     

    protected function getData(): array
    {
        $data = [0, 0];

        $this->record->emails->each(function ($email) use (&$data) {
            if (filled($email->pivot->status) && $email->pivot->status !== 'skipped') {
                $statusIndex = $email->pivot->found_at_mailbox === 'spam'
                    ? 1 : 0;

                $landedIn = $email->pivot->found_at_mailbox;
                    
                if($landedIn != 'Not found') {
                    $data[$statusIndex]++;
                }
            }
        });

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => [
                        '#10b981',
                        '#eab308',
                    ]
                ],
            ],
            'labels' => [
                'Healthy',
                'Spam',
            ],
        ];
    }
}
