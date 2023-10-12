<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReviewsExport implements  FromCollection, WithHeadings ,ShouldAutoSize , WithStyles
{
    protected $orders;
    
    public function __construct($orders)
    {
        $this->orders = $orders;
    }
    
    public function collection()
    {
        
        $data = [];
        
        foreach ($this->orders as $order) {
            $products = $order->product()->pluck('name')->implode(', ');
            $data[] = [
                'Order ID' => $order->id,
                'Table Number' => $order->table->table_num,
                'Waiter Name' => $order->author,
                'Service Rate' => $order->serviceRate,
                'Feedback' => $order->feedback,
                'Estimated Time For Order' => $order->estimatedForOrder,
                'Time Order' => $order->created_at->format('H:i:s'),
                'Start Preparing' => $order->time_start,
                'End Preparing' => $order->time_end,
                'Time waiter' => $order->time_waiter,
                'from_client_to_kitchen_diff' => Carbon::parse($order->time)->diffInMinutes(Carbon::parse($order->time_end)). ' minute',
                'from_kitchen_to_Waiter_diff' => Carbon::parse($order->time_end)->diffInMinutes(Carbon::parse($order->time_Waiter)). ' minute',
                'from_client_to_Waiter_diff' => Carbon::parse($order->time)->diffInMinutes(Carbon::parse($order->time_Waiter)). ' minute',
                'from_start_to_done_diff' => Carbon::parse($order->time_start)->diffInMinutes(Carbon::parse($order->time_end)). ' minute',
                'Products' => $products,
                'Total Price' => $order->total_price,
                'Branch' => $order->branch->name
            ];
        }
        
        return collect($data);
    }
   
    public function headings(): array
    {
        return [
            'Order ID',
            'Table Number',
            'Waiter Name',
            'Service Rate',
            'Feedback',
            'Estimated Time For Order',
            'Time Order',
            'Start Preparing',
            'End Preparing',
            'Time waiter',
            'from_client_to_kitchen_diff',
            'from_kitchen_to_Waiter_diff',
            'from_client_to_Waiter_diff',
            'from_start_to_done_diff',
            'Products',
            'Total Price',
            'Branch'
        ];

    }
    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '777'],
                ],
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'eebff2',
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
    
        ];

        $sheet->getStyle('A1:Q1')->applyFromArray($styleArray);
        $sheet->getStyle('A1:Q1')->getAlignment()->setHorizontal('center');
        
    }
    
}
