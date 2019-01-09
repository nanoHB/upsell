<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Offer;
use App\Product;
use Illuminate\Support\Facades\App;
use OhMyBrew\ShopifyApp\ShopifyApp;

class OfferController extends Controller
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
     * OfferController constructor.
     * @param Product $product
     * @param Offer $offer
     */
    public function __construct(Product $product, Offer $offer)
    {
        $this->product = $product;
        $this->offer = $offer;
    }

    public function new()
    {
        return view('offer.new');
    }

    public function edit($id)
    {
        $offerModel = $this->offer;
        $offer = $offerModel->find($id);
        $offerProduct = $this->convertOfferCollectionToString($id);
        $triggerProduct = $this->convertTriggerCollectionToString($id);
        return view('offer.edit')->with(['offer'=>$offer,'offerProduct' => $offerProduct,'triggerProduct'=>$triggerProduct,'id'=>$id]);
    }

    public function offerEdit(Request $request)
    {
        $shop = \ShopifyApp::shop();
        $offerData = [
            'trigger_place' => $request->input('trigger_place'),
            'type' => $request->input('offer_type'),
            'name' => $request->input('name'),
            'title' => $request->input('offer_title'),
            'description' => $request->input('offer_description') ?? '',
            'shop_id' => $shop->id,
            'start_day' => \Carbon\Carbon::parse($request->input('start-time')),
            'end_day' => \Carbon\Carbon::parse($request->input('end-time'))
        ];
        try {
            $offerModel = $this->offer->find($request->input('id'));
            $offerModel->fill($offerData)->save();
            $triggerData = $this->handleDataList($request->input('trigger_products'));
            $offerData = $this->handleDataList($request->input('offer_products'));
            $this->insertProduct($triggerData, $offerData, $shop);
            $this->editOfferCollection($request->input('id'), $offerData, $shop);
            $this->edittriggerCollection($request->input('id'), $triggerData, $shop);
            return redirect('/offer/list');
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect()->back()->withException($ex);
        }
    }

    /**
     * return list order product
     * @param $id
     * @return string
     */
    public function convertOfferCollectionToString($id)
    {
        $offerList = DB::table('offer')->select('offer.id','offer_collection.product_id','product.shopify_id','product.variant_id')
            ->join('offer_collection','offer.id','=','offer_collection.offer_id')
            ->join('product','offer_collection.product_id','=','product.id')
            ->where('offer.id',$id)->get();
        $offerString = '';
        foreach ($offerList as $value){
            $offerString .= $value->shopify_id.'-'.$value->variant_id.',';
        }
        $offerString = rtrim($offerString,',');
        return $offerString;
    }

    public function convertTriggerCollectionToString($id)
    {
        $triggerList = DB::table('offer')->select('offer.id','trigger_collection.product_id','product.shopify_id','product.variant_id')
            ->join('trigger_collection','offer.id','=','trigger_collection.offer_id')
            ->join('product','trigger_collection.product_id','=','product.id')
            ->where('offer.id',$id)->get();
        $triggerString = '';
        foreach ($triggerList as $value){
            $triggerString .= $value->shopify_id.'-'.$value->variant_id.',';
        }
        $triggerString = rtrim($triggerString,',');
        return $triggerString;
    }

    /**
     * render list offer view
     * @return $this
     */
    public function list()
    {
        $shop = \ShopifyApp::shop();
        $offerModel = $this->offer;
        $listOffer = $offerModel->where('shop_id',$shop->id)->paginate(10);
        return view('offer.list')->with(compact('listOffer'));
    }

    /**
     * render list offer table
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getTable(Request $request)
    {
        $shop = \ShopifyApp::shop();
        $offerModel = $this->offer;
        $wherePattern = ['shop_id'=>$shop->id];
        if($request->input('active_filter') != 'all'){
            $wherePattern['active'] = $request->input('active_filter');
        }
        if($request->input('offer_type') != ''){
            $wherePattern['type'] = $request->input('offer_type');
        }
        if($request->input('name') != ''){
            $wherePattern['name'] = $request->input('name');
        }
        try {
            $listOffer = $offerModel->where($wherePattern)->paginate($request->input('page_limit'));
            $view = view('offer.table')->with(compact('listOffer'))->render();
            return response()->json(['success' => true, 'view' => $view]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'error'=>$e]);
        }
    }

    /**
     * change offer status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $status = 0;
        if($request->get('status') == 'true'){
            $status = 1;
        }
        $offerId = $request->get('id');
        try {
            $offerModel = $this->offer->find($offerId);
            $offerModel->active = $status;
            $offerModel->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'error' => $e]);
        }
    }

    public function deleteOffer(Request $request)
    {
        $offerId = $request->get('id');
        try {
            $offerModel = $this->offer->find($offerId);
            $offerModel->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'error' => $e]);
        }
    }

    /**
     * Create offer
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function offerCreate(Request $request)
    {
        $shop = \ShopifyApp::shop();
        $offerData = [
            'trigger_place' => $request->input('trigger_place'),
            'type' => $request->input('offer_type'),
            'name' => $request->input('name'),
            'title' => $request->input('offer_title'),
            'description' => $request->input('offer_description') ?? '',
            'shop_id' => $shop->id,
            'start_day' => \Carbon\Carbon::parse($request->input('start-time')),
            'end_day' => \Carbon\Carbon::parse($request->input('end-time'))
        ];
        try {
            $offerModel = $this->offer;
            $offerModel->fill($offerData)->save();
            $triggerData = $this->handleDataList($request->input('trigger_products'));
            $offerData = $this->handleDataList($request->input('offer_products'));
            $this->insertProduct($triggerData, $offerData, $shop);
            $this->insertTriggerCollection($offerModel->id, $triggerData, $shop);
            $this->insertOfferCollection($offerModel->id, $offerData, $shop);
            return redirect('/offer/list');
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect()->back()->withException($ex);
        }
    }

    /**
     * handle list variant and product id from string
     * @param $string
     * @return array
     */
    public function handleDataList($string)
    {
        $rawArray = explode(',',$string);
        $data = [];
        foreach($rawArray as $value){
            $data[] = explode('-',$value);
        }
        return $data;
    }

    /**
     * @param array $triggerData
     * @param array $offerData
     * @param ShopifyApp $shop
     */
    public function insertProduct($triggerData, $offerData, $shop)
    {
        $product = $this->product;
        $list_variant = $product->select('variant_id')->where('shop_id',$shop->id)->get();
        $list_exist_varriant = [];
        foreach ($list_variant as $value){
            $list_exist_varriant[] = $value->variant_id;
        }
        $data = [];
        $mergeData = array_merge($triggerData,$offerData);
        foreach ($mergeData as $value){
            if (!in_array($value[1],$list_exist_varriant)){
                $data[] = ['shopify_id'=>$value[0],'variant_id' => $value[1],'shop_id' => $shop->id,'created_at' => \Carbon\Carbon::now(),'updated_at' => \Carbon\Carbon::now()];
                $list_exist_varriant[] = $value[1];
            }
        }
        DB::table('product')->insert($data);
    }

    /**
     * @param array $offerId
     * @param array $triggerData
     * @param ShopifyApp $shop
     */
    public function insertTriggerCollection($offerId, $triggerData, $shop)
    {
        $productModel = $this->product;
        $variantIds = [];
        foreach ($triggerData as $value){
            $variantIds[] = $value[1];
        }
        $listProductIds = $productModel->select('id')->where('shop_id',$shop->id)->whereIn('variant_id',$variantIds)->get();
        $insertData = [];
        foreach ($listProductIds as $value){
            $insertData[] = ['offer_id' => $offerId,'product_id'=>$value->id];
        }
        DB::table('trigger_collection')->insert($insertData);
    }

    /**
     * @param array $offerId
     * @param array $offerData
     * @param ShopifyApp $shop
     */
    public function insertOfferCollection($offerId, $offerData, $shop)
    {
        $productModel = $this->product;
        $variantIds = [];
        foreach ($offerData as $value){
            $variantIds[] = $value[1];
        }
        $listProductIds = $productModel->select('id')->where('shop_id',$shop->id)->whereIn('variant_id',$variantIds)->get();
        $insertData = [];
        foreach ($listProductIds as $value){
            $insertData[] = ['offer_id' => $offerId,'product_id'=>$value->id];
        }
        DB::table('offer_collection')->insert($insertData);
    }

    public function editOfferCollection($offerId, $offerData, $shop)
    {
        $productModel = $this->product;
        $variantIds = array_map(function ($item){
            return $item[1];
        },$offerData);
        $listProductIds = $productModel->select('id')->where('shop_id',$shop->id)->whereIn('variant_id',$variantIds)->get();
        $listOldProduct = DB::table('offer_collection')->where('offer_id',$offerId)->get();
        $oldProduct = [];
        $productIds = [];
        $insertData = [];
        foreach ($listProductIds as $value){
            $productIds[] = $value->id;
        }
        foreach ($listOldProduct as $value){
            $oldProduct[] = $value->product_id;
        }
        $listRemove = array_diff($oldProduct,$productIds);
        $listInsert = array_diff($productIds,$oldProduct);
        foreach ($listInsert as $value){
            $insertData[] = ['offer_id' => $offerId, 'product_id' => $value];
        }
        DB::table('offer_collection')->where('offer_id',$offerId)->whereIn('product_id',$listRemove)->delete();
        DB::table('offer_collection')->insert($insertData);
    }

    public function editTriggerCollection($offerId, $triggerData, $shop)
    {
        $productModel = $this->product;
        $variantIds = array_map(function ($item){
            return $item[1];
        },$triggerData);
        $listProductIds = $productModel->select('id')->where('shop_id',$shop->id)->whereIn('variant_id',$variantIds)->get();
        $listOldProduct = DB::table('trigger_collection')->where('offer_id',$offerId)->get();
        $oldProduct = [];
        $productIds = [];
        $insertData = [];
        foreach ($listProductIds as $value){
            $productIds[] = $value->id;
        }
        foreach ($listOldProduct as $value){
            $oldProduct[] = $value->product_id;
        }
        $listRemove = array_diff($oldProduct,$productIds);
        $listInsert = array_diff($productIds,$oldProduct);
        foreach ($listInsert as $value){
            $insertData[] = ['offer_id' => $offerId, 'product_id' => $value];
        }
        DB::table('trigger_collection')->where('offer_id',$offerId)->whereIn('product_id',$listRemove)->delete();
        DB::table('trigger_collection')->insert($insertData);
    }
}
