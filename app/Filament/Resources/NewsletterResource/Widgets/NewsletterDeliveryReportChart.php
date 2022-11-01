<?php

namespace App\Filament\Resources\NewsletterResource\Widgets;

use App\Enums\NewsletterStatusEnum;
use Filament\Widgets\DoughnutChartWidget;
use Illuminate\Database\Eloquent\Model;

class NewsletterDeliveryReportChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Delivery Report';

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
                'Valid Sample',
                'Invalid',
            ],
        ];
    }
}
