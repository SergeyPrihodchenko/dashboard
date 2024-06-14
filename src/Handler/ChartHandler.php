<?php

namespace App\Handler;

use Symfony\UX\Chartjs\Builder\ChartBuilder;
use Symfony\UX\Chartjs\Model\Chart;

class ChartHandler
{
    private Chart $chart;
    private bool $isOptioned = false;

    public function __construct()
    {
        $chartBuilder = new ChartBuilder();
        $this->chart = $chartBuilder->createChart(Chart::TYPE_LINE);
    }

    public function getChart() : Chart
    {
        if (!$this->isOptioned) {
            $this->setChartOptions(
                $this->getChartDefaultOptions()
            );
        }

        return $this->chart;
    }

    public function setChartData(array $labels, array $datasets) : void
    {
        $this->chart->setData([
           'labels' => $labels,
           'datasets' => $datasets
        ]);
    }

    public function setChartOptions(array $options) : void
    {
        $this->isOptioned = true;
        $this->chart->setOptions($options);
    }

    private function getChartDefaultOptions()
    {
        return [
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
            'plugins' => [
                'zoom' => [
                    'zoom' => [
                        'wheel' => ['enabled' => true],
                        'pinch' => ['enabled' => true],
                        'mode' => 'x',
                    ],
                ],
            ],
        ];
    }
}
