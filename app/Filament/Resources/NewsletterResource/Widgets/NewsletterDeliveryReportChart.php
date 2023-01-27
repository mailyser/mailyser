<?php

namespace App\Filament\Resources\NewsletterResource\Widgets;

use App\Enums\NewsletterStatusEnum;
use Filament\Widgets\DoughnutChartWidget;
use Illuminate\Database\Eloquent\Model;

class NewsletterDeliveryReportChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Mailbox Distribution';

    protected static string $view = 'filament::widgets.chart-widget-custom';
    /*
    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];
    */
    public ?Model $record = null;

    protected function getPollingInterval(): ?string
    {
        if ($this->record->status === NewsletterStatusEnum::Finished->name) {
            return null;
        }

        return '15s';
    }

    /**
     * What mailboxes emails landed in.
     */
    protected function getData(): array
    {
        $data = [];

        $this->record->emails->each(function ($email) use (&$data) {
            if ($email->pivot->status === 'scanned') {
                $landedIn = $email->pivot->found_at_mailbox;

                if (! isset($data[$landedIn])) {
                    $data[$landedIn] = 0;
                }

                $data[$landedIn]++;
            }
        });

        if(isset($data["Not found"]))
            unset($data["Not found"]);
        
        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => array_values(array_map(function () {
                        return '#'
                            . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT)
                            . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT)
                            . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
                    }, $data))
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

//    protected function getData(): array
//    {
//        $data = [0, 0];
//
//        $this->record->emails->each(function ($email) use (&$data) {
//            if ($email->pivot->status) {
//                $statusIndex = $email->pivot->status !== 'skipped'
//                    ? 0 : 1;
//
//                $data[$statusIndex]++;
//            }
//        });
//
//        return [
//            'datasets' => [
//                [
//                    'data' => $data,
//                    'backgroundColor' => [
//                        '#10b981',
//                        '#eab308',
//                    ]
//                ],
//            ],
//            'labels' => [
//                'Valid Sample',
//                'Invalid',
//            ],
//        ];
//    }
}
