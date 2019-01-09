<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * @var Product
     */
    protected $product;
    /**
     * @var Offer
     */
    protected $offer;

    /**
     * TrackingController constructor.
     * @param Product $product
     * @param Offer $offer
     */
    public function __construct(Product $product, Offer $offer)
    {
        $this->product = $product;
        $this->offer = $offer;
    }

    /**
     * tracking when product added to cart
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackingAddToCartOffer(Request $request)
    {
        $productId = $request->input('product_id');
        $domain = $request->input('domain');
        $triggerType = $request->input('type');
        $addedVariant = $request->input('added_variant');
        $shopModel = config('shopify-app.shop_model');
        $shop = $shopModel::withTrashed()->firstOrCreate(['shopify_domain' => $domain]);
        $variantData = $shop->api()->rest('GET', "/admin/variants/$addedVariant.json");
        $variantPrice = $variantData->body->variant->price;
        try {
            $listProductId = $this->getProductsByShopifyId($productId);
            $variantProductId = $this->product->where('variant_id', $addedVariant)->first()->id;
            $listOfferAvailable = $this->getOfferListByProductList($listProductId, $triggerType, 'product-detail');
            $listTrackingOffer = $this->getListTrackingOffer($listOfferAvailable, $variantProductId);
            if (empty($listTrackingOffer)) return response()->json(['success'=>false,'error'=>'Not have offer']);
            $trackingCode = $this->insertReportSale($listTrackingOffer, $variantProductId, $shop->id,$variantPrice);
            return response()->json(['success'=>true,'tracking_code'=>$trackingCode]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['success'=>false]);
        }

    }

    /**
     * get list product id (one shopify id have many varriant) from shopify id
     * @param string $shopifyId
     * @return array
     */
    public function getProductsByShopifyId($shopifyId)
    {
        $productModel = $this->product;
        $productInternalId = $productModel->where('shopify_id',$shopifyId)->get();
        $listProduct = [];
        foreach ($productInternalId as $value){
            $listProduct[] = $value->id;
        }
        return $listProduct;
    }

    /**
     * Get Offer list from list product id
     * @param array $listProductId
     * @return array
     */
    public function getOfferListByProductList($listProductId,$triggerType,$triggerPlace)
    {
        $currentTime = Carbon::now();
        $offerModel = $this->offer;
        $listTrigger = DB::table('trigger_collection')->whereIn('product_id',$listProductId)->get();
        $listOfferId = [];
        foreach ($listTrigger as $value){
            $listOfferId[] = $value->offer_id;
        }
        $offerAvailble = $offerModel
            ->whereIn('id',$listOfferId)
            ->where('type',$triggerType)
            ->where('trigger_place',$triggerPlace)
            ->where('start_day','<=',$currentTime)
            ->where('end_day','>=',$currentTime)
            ->where('active',1)
            ->get();
        $offerAvailbleId = [];
        foreach ($offerAvailble as $value){
            $offerAvailbleId[] = $value->id;
        }
        return $offerAvailbleId;
    }

    /**
     * @param array $listOfferId
     * @param string $variantProductId
     * @return array
     */
    public function getListTrackingOffer($listOfferId, $variantProductId)
    {
        $listOffer = DB::table('offer_collection')
            ->whereIn('offer_id',$listOfferId)
            ->where('product_id',$variantProductId)->get();
        $listTrackingOffer = [];
        foreach ($listOffer as $value){
            array_push($listTrackingOffer,$value->offer_id);
        }
        return $listTrackingOffer;
    }

    /**
     * insert into report sale table and return tracking code
     * @param $trackingOffer
     * @param $variantProductId
     * @param $shopId
     * @param $variantPrice
     * @return string
     */
    public function insertReportSale($trackingOffer, $variantProductId, $shopId, $variantPrice)
    {
        $insertData = [];
        $trackingCode = base64_encode(Carbon::now().str_random(4));
        foreach ($trackingOffer as $value){
            array_push($insertData,['offer_id'=>$value,
                'product_id'=>$variantProductId,
                'shop_id'=>$shopId,
                'tracking_code'=>$trackingCode,
                'amount'=>$variantPrice,
                'is_purchase'=>0,
                'created_at'=>Carbon::now()]);
        }
        DB::table('report_sale')->insert($insertData);
        return $trackingCode;
    }
}
