<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OhMyBrew\ShopifyApp\ShopifyApp;
use Carbon\Carbon;
use App\Product;
use App\Offer;
use App\Config;

class WidgetController extends Controller
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
     * @var Config
     */
    protected $config;

    /**
     * WidgetController constructor.
     * @param Product $product
     * @param Offer $offer
     * @param Config $config
     */
    public function __construct(Product $product, Offer $offer, Config $config)
    {

        $this->product = $product;
        $this->offer = $offer;
        $this->config = $config;
    }

    /**
     * get widget for product detail
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidget(Request $request)
    {
        //list variable
        $domain = $request->get('domain');
        $productId = $request->get('product_id');
        $shopModel = config('shopify-app.shop_model');
        $triggerType = $request->get('type');
        $shop = $shopModel::withTrashed()->firstOrCreate(['shopify_domain' => $domain]);
        //handle product data
        $listProductId = $this->getProductsByShopifyId($productId);
        $offerAvailbleId = $this->getOfferListByProductList($listProductId,$triggerType,'product-detail');
        //check product have offer
        if(empty($offerAvailbleId)){
            return response()->json(['success' =>  false]);
        }
        $this->insertReportView($offerAvailbleId,$shop);
        $widgetTitlte = $this->getWidgetTitle($offerAvailbleId,$shop);
        $widgetDescription = $this->getWidgetDescription($offerAvailbleId,$shop);
        $listRenderProduct = $this->getListProductToRender($offerAvailbleId);
        //render query pattern
        $queryPattern = '?ids=';
        foreach ($listRenderProduct['shopify_id'] as $value){
            $queryPattern .= $value.',';
        }
        try {
            //get product data
            $request = $shop->api()->rest('GET', "/admin/products.json" . $queryPattern);
            $data = $request->body->products;
            //render view
            $view = view('widget.widgetUpsell')
                ->with(['data' => $data,
                    'listVariant' => $listRenderProduct['variant_id'],
                    'title'=>$widgetTitlte,
                    'description'=>$widgetDescription,
                    'type'=>$triggerType])
                ->render();
            return response()->json(['success' => true, 'view' => $view]);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['success' =>  false]);
        }
    }

    public function getCartWidget(Request $request)
    {
        //list variable
        $domain = $request->get('domain');
        $triggerType = $request->get('type');
        $lineItems = $request->input('items');
        $shopModel = config('shopify-app.shop_model');
        $shop = $shopModel::withTrashed()->firstOrCreate(['shopify_domain' => $domain]);
        //handle product data
        $variantIds = [];
        foreach ($lineItems as $item){
            array_push($variantIds,$item['variant_id']);
        }
        $listProductId = $this->getProductsByVariantId($variantIds);
        $offerAvailbleId = $this->getOfferListByProductList($listProductId,$triggerType,'cart');
        //check product have offer
        if(empty($offerAvailbleId)){
            return response()->json(['success' =>  false]);
        }
        $this->insertReportView($offerAvailbleId,$shop);
        $widgetTitlte = $this->getWidgetTitle($offerAvailbleId,$shop);
        $widgetDescription = $this->getWidgetDescription($offerAvailbleId,$shop);
        $listRenderProduct = $this->getListProductToRender($offerAvailbleId);
        //render query pattern
        $queryPattern = '?ids=';
        foreach ($listRenderProduct['shopify_id'] as $value){
            $queryPattern .= $value.',';
        }
        try {
            //get product data
            $request = $shop->api()->rest('GET', "/admin/products.json" . $queryPattern);
            $data = $request->body->products;
            //render view
            $view = view('widget.widget')->with(['data' => $data,
                'listVariant' => $listRenderProduct['variant_id'],
                'title'=>$widgetTitlte,
                'description'=>$widgetDescription])
                ->render();
            return response()->json(['success' => true, 'view' => $view]);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['success' => false,'error'=>$e]);
        }

    }

    /**
     * @param array $listOffer
     * @param ShopifyApp $shop
     * @return string
     */
    public function getWidgetTitle($listOffer, $shop)
    {
        if (sizeof($listOffer) > 1 ){
            $titleModel = $this->config->where(['path'=>SettingController::CONFIG_TITLE,'shop_id'=>$shop->id])->first()->value ?? '';
            return $titleModel;
        }
        $offerModel = $this->offer->find($listOffer[0]);
        return $offerModel->title;
    }

    /**
     * @param array $listOffer
     * @param ShopifyApp $shop
     * @return string
     */
    public function getWidgetDescription($listOffer, $shop)
    {
        if (sizeof($listOffer) > 1 ){
            $descriptionModel = $this->config->where(['path'=>SettingController::CONFIG_DESCRIPTION,'shop_id'=>$shop->id])->first()->value ?? '';
            return $descriptionModel;
        }
        $offerModel = $this->offer->find($listOffer[0]);
        return $offerModel->description;
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
     * get list product id (one shopify id have many varriant) from list variant ids
     * @param array $variantIds
     * @return array
     */
    public function getProductsByVariantId($variantIds)
    {
        $productModel = $this->product;
        $productInternalId = $productModel->whereIn('variant_id',$variantIds)->get();
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
     * get list product to render widget
     * @param array $offerId
     * @return array
     */
    public function getListProductToRender($offerId)
    {
        $productModel = $this->product;
        $listOfferProduct = DB::table('offer_collection')->whereIn('offer_id',$offerId)->get();
        $listOfferProductId = [];
        foreach ($listOfferProduct as $value){
            $listOfferProductId[] = $value->product_id;
        }
        $listRawVisibleProduct = $productModel->whereIn('id',$listOfferProductId)->get();
        $listShopifyProduct = [];
        $listVariantProduct = [];
        foreach ($listRawVisibleProduct as $value){
            $listShopifyProduct[] = $value->shopify_id;
            $listVariantProduct[] = $value->variant_id;
        }
        return ['shopify_id'=>$listShopifyProduct,'variant_id'=>$listVariantProduct];
    }

    /**
     * insert into report view table each time widget is loaded
     * @param array $offerId
     * @param ShopifyApp $shop
     */
    public function insertReportView($offerId, $shop)
    {
        $insertData = [];
        $currentDate = Carbon::now();
        foreach ($offerId as $value){
            array_push($insertData,['offer_id'=>$value,'shop_id'=>$shop->id,'created_at'=>$currentDate]);
        }
        DB::table('report_view')->insert($insertData);
    }
}
