<?php

namespace App\Filament\Resources\NewsletterResource\Widgets;

use App\Enums\NewsletterStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\DoughnutChartWidget;

class NewsletterSpamReportChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Spam Report';

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
            if ($email->pivot->status) {
                $statusIndex = $email->pivot->status !== 'spam'
                    ? 0 : 1;

                $data[$statusIndex]++;
            }
        });

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => [
                        'green',
                        'orange',
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
