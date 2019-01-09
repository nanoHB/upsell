<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getSelectFormData(Request $request)
    {
        $shop = \ShopifyApp::shop();
        $filterList = $this->getFilterList($shop);
        $productList = $this->getProductList($shop,$request,'');
        $selectedProduct = $this->getListSelectedProduct($request);
        $totalPage = $this->getTotalPage($shop,$request);
        return response()->json(['success' => true, 'filterList' => $filterList, 'productList' => $productList,'selectedProduct' => $selectedProduct,'totalPage'=>$totalPage]);
    }

    public function getFilterList($shop)
    {
        $filterList = [
            'vendor' => $this->getVendorList($shop),
            'product_type' => $this->getProductType($shop),
            'custom_collection' => $this->getCustomCollection($shop)
        ];
        return $filterList;
    }

    public function getVendorList($shop)
    {
        $request = $shop->api()->rest('GET', '/admin/products.json?fields=vendor');
        $rawVendor = $request->body->products;
        $vendorList = [];
        foreach ($rawVendor as $value) {
            $vendorList[] = $value->vendor;
        }
        return array_unique($vendorList);
    }

    public function getProductType($shop)
    {
        $request = $shop->api()->rest('GET', '/admin/products.json?fields=product_type');
        $rawProductType = $request->body->products;
        $productTypeList = [];
        foreach ($rawProductType as $value) {
            $productTypeList[] = $value->product_type;
        }
        $productTypeList = array_filter($productTypeList);
        return array_unique($productTypeList);
    }

    public function getTotalPage($shop, $request)
    {
        $searchPartent = $this->generateSearchPartent($request);
        $searchPartent = str_replace(substr($searchPartent,strpos($searchPartent,'limit')),'',$searchPartent);
        $totalProduct = $shop->api()->rest('GET', '/admin/products/count.json'.$searchPartent);
        $pageLimit = $request->input('product_limit') ?? 10;
        $totalPage =  $totalProduct->body->count/$pageLimit;
        return ceil($totalPage);
    }

    public function getCustomCollection($shop)
    {
        $request = $shop->api()->rest('GET', '/admin/custom_collections.json');
        $rawCollection = $request->body->custom_collections;
        $collectionForFillter = [];
        foreach ($rawCollection as $key => $value) {
            $collectionForFillter[$key]['id'] = $value->id;
            $collectionForFillter[$key]['title'] = $value->title;
        }
        return $collectionForFillter;
    }

    public function getProductList($shop,$request,$searchPartent)
    {
        $selectedIds = $request->get('selected_product');
        $listVariantId = [];
        if (isset($selectedIds)){
            $listId = explode(',',$selectedIds);
            foreach ($listId as $key => $value){
                $listId[$key] = explode('-',$value);
            }
            foreach ($listId as $value){
                $listVariantId[] = $value[1];
            }
        }
        //Fetch product form shop
        if ($searchPartent == ''){
            $searchPartent = '?product_limit=10&page=1';
        }
        $response = $shop->api()->rest('GET', '/admin/products.json'.$searchPartent);
        $rawProducts = $response->body->products;
        $productDetails = [];
        //Assign data into array
        foreach ($rawProducts as $key => $value) {
            $productDetails[$key]['product_id'] = $value->id;
            $productDetails[$key]['title'] = $value->title;
            $productDetails[$key]['image'] = $value->image->src;
            $productDetails[$key]['count_variant'] = 0;
            foreach ($value->variants as $item) {
                $selected = 'false';
                if(in_array($item->id,$listVariantId)){
                    $selected = 'true';
                }
                $productDetails[$key]['variants'][] = ['variant_id' => $item->id, 'price' => $item->price, 'option' => $item->option1,'selected'=>$selected];
            }
        }
        return $productDetails;
    }

    public function generateSearchPartent($request)
    {
        //generate search partent
        $searchPattern = '?';
        if ($request->input('product_vendor')!='') {
            $searchPattern .= 'vendor=' . $request->input('product_vendor').'&';
        }
        if ($request->input('product_type')!='') {
            $searchPattern .= 'product_type=' . $request->input('product_type').'&';
        }
        if ($request->input('product_collection')!='') {
            $searchPattern .= 'collection_id=' . $request->input('product_collection').'&';
        }
        if ($request->input('product_title')!='') {
            $searchPattern .= 'title=' . $request->input('product_title').'&';
        }
        $searchPattern .= 'limit=' . $request->input('product_limit').'&';
        if ($request->input('page')!='') {
            $searchPattern .= 'page=' . $request->input('page');
        }else{
            $searchPattern .= 'page=1';
        }
        return $searchPattern;
    }

    public function getListSelectedProduct($request)
    {
        $listId = $request->get('selected_product');
        if (!isset($listId)) return [];
        $listId = explode(',',$listId);
        foreach ($listId as $key => $value){
            $listId[$key] = explode('-',$value);
        }
        $listIdString = '';
        $listVariantId = [];
        foreach ($listId as $value){
            $listIdString .= $value[0].',';
            $listVariantId[] = $value[1];
        }
        $shop = \ShopifyApp::shop();
        $response = $shop->api()->rest('GET', '/admin/products.json?ids='.$listIdString);
        $rawProducts = $response->body->products;
        $productDetails = [];
        //Assign data into array
        foreach ($rawProducts as $key => $value) {
            $name = $value->title;
            $image = $value->image->src;
            foreach ($value->variants as $order => $item) {
                if(in_array($item->id,$listVariantId)){
                    $productDetails[$key.$order]['product_id'] = $item->product_id;
                    $productDetails[$key.$order]['name'] = $name;
                    $productDetails[$key.$order]['image'] = $image;
                    $productDetails[$key.$order]['variant_id'] = $item->id;
                    $productDetails[$key.$order]['title'] = $item->title;
                    $productDetails[$key.$order]['price'] = $item->price;
                }
            }
        }
        return $productDetails;
    }

    public function getProductByFilter(Request $request)
    {
        $shop = \ShopifyApp::shop();
        $productList = $this->getProductList($shop,$request,$this->generateSearchPartent($request));
        $totalPage = $this->getTotalPage($shop,$request);
        return response()->json(['success'=>true,'productList'=>$productList,'totalPage'=>$totalPage]);
    }
}
