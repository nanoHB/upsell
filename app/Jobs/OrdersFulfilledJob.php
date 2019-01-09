<?php namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class OrdersFulfilledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string $shopDomain The shop's myshopify domain
     * @param object $webhook The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $listTrackingCode = $this->checkTrackingCode();
        DB::table('report_sale')->whereIn('tracking_code',$listTrackingCode)->update(['is_purchase' => 1]);
    }

    public function checkTrackingCode()
    {
        $data = $this->data->line_items;
        $listTrackingCode = [];
        foreach ($data as $value){
            foreach ($value->properties as $item){
                if($item->name == "tracking-code"){
                    array_push($listTrackingCode,$item->value);
                }
            }
        }
        return $listTrackingCode;
    }
}
