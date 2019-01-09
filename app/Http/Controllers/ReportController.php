<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use OhMyBrew\ShopifyApp\ShopifyApp;
use App\Offer;
use App\Product;

class ReportController extends Controller
{
    /**
     * @var Offer
     */
    protected $offer;
    /**
     * @var Product
     */
    protected $product;

    /**
     * ReportController constructor.
     * @param Offer $offer
     * @param Product $product
     */
    public function __construct(Offer $offer, Product $product)
    {

        $this->offer = $offer;
        $this->product = $product;
    }

    public function getOfferReport($id)
    {
        $shop = \ShopifyApp::shop();
        $totalView = DB::table('report_view')->select('created_at')->where('offer_id', $id)->get()->groupBy(function ($date) {
            //return Carbon::parse($date->created_at)->format('Y'); // grouping by years
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $productAddedTime = DB::table('report_sale')
            ->select('product_id','shop_id','is_purchase','amount')
            ->where('is_purchase',1)
            ->select('report_sale.product_id', DB::raw('count(report_sale.id) as total'),DB::raw('sum(report_sale.amount) as amount'))
            ->where('report_sale.shop_id', $shop->id)
            ->groupBy('report_sale.product_id')->get();
        $listProductName = $this->getProductName();
        $reportViewData = [];
        $purchaseData = [];
        $addCartData = [];
        $amountData = [];
        $listMonth = [];
        $productAddedData = [];
        foreach ($productAddedTime as $value) {
            array_push($productAddedData, ['time' => $value->total,'name' => $listProductName[$value->product_id], 'amount'=>$value->amount]);
        };
        foreach ($totalView as $key => $value) {
            $view = DB::table('report_view')->where('offer_id', $id)->whereMonth('created_at', $key)->count();
            $purchase = DB::table('report_sale')->where('offer_id', $id)->where('is_purchase',1)->whereMonth('created_at', $key)->count();
            $addCart = DB::table('report_sale')->where('offer_id', $id)->whereMonth('created_at', $key)->count();
            $amount = DB::table('report_sale')->where('offer_id', $id)->where('is_purchase',1)->whereMonth('created_at', $key)->sum('amount');
            array_push($reportViewData, $view);
            array_push($purchaseData, $purchase);
            array_push($addCartData, $addCart);
            array_push($amountData, round($amount,2));
            array_push($listMonth, date('F', mktime(0, 0, 0, $key, 10)));
        }
        return view('report.offerdetail')->with(['viewData' => $reportViewData,
            'listMonth' => $listMonth,
            'purchaseData'=>$purchaseData,
            'productAddedData' => $productAddedData,
            'addCartData' => $addCartData,
            'amountData' => $amountData
        ]);
    }

    public function index()
    {
        return view('report.index');
    }

    public function getProductChartData()
    {
        $shop = \ShopifyApp::shop();
        $productAddedTime = DB::table('report_sale')
            ->select('report_sale.product_id', DB::raw('count(report_sale.id) as total'))
            ->where('report_sale.shop_id', $shop->id)
            ->groupBy('report_sale.product_id')->get();
        $productAmount = DB::table('report_sale')
            ->select('report_sale.product_id', DB::raw('sum(report_sale.amount) as total'))
            ->where('report_sale.shop_id', $shop->id)
            ->groupBy('report_sale.product_id')
            ->get();
        $purchaseTime = DB::table('report_sale')
            ->select('product_id','shop_id','is_purchase')
            ->where('is_purchase',1)
            ->select('report_sale.product_id', DB::raw('count(report_sale.id) as total'))
            ->where('report_sale.shop_id', $shop->id)
            ->groupBy('report_sale.product_id')->get();
        $purchaseAmount = DB::table('report_sale')
            ->select('product_id','shop_id','is_purchase')
            ->where('is_purchase',1)
            ->select('report_sale.product_id', DB::raw('sum(report_sale.amount) as total'))
            ->where('report_sale.shop_id', $shop->id)
            ->groupBy('report_sale.product_id')
            ->get();
        $listProductName = $this->getProductName();
        $productAddedData = [];
        $productAmountData = [];
        $purchaseTimeData = [];
        $purchaseAmountData = [];
        foreach ($productAddedTime as $value) {
            $color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            array_push($productAddedData, ['value' => $value->total, 'color' => $color, 'highlight' => $color, 'label' => $listProductName[$value->product_id]]);
        };
        foreach ($productAmount as $value) {
            $color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            array_push($productAmountData, ['value' => $value->total, 'color' => $color, 'highlight' => $color, 'label' => $listProductName[$value->product_id]]);
        };
        foreach ($purchaseTime as $value) {
            $color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            array_push($purchaseTimeData, ['value' => $value->total, 'color' => $color, 'highlight' => $color, 'label' => $listProductName[$value->product_id]]);
        };
        foreach ($purchaseAmount as $value) {
            $color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            array_push($purchaseAmountData, ['value' => round($value->total,2), 'color' => $color, 'highlight' => $color, 'label' => $listProductName[$value->product_id]]);
        };
        return response()->json(['success' => true,
            'product_view' => $productAddedData,
            'product_amount' => $productAmountData,
            'purchase_time'=>$purchaseTimeData,
            'purchase_amount'=>$purchaseAmountData
        ]);
    }

    public function getProductName()
    {
        $shop = \ShopifyApp::shop();
        $allProduct = $shop->api()->rest('GET', "/admin/products.json")->body->products;
        $allInternalProduct = $this->product->all();
        $internalProducts = [];
        foreach ($allInternalProduct as $value) {
            $internalProducts[$value->id] = $value->variant_id;
        }
        $returnData = [];
        foreach ($allProduct as $value) {
            foreach ($value->variants as $item) {
                if (in_array($item->id, $internalProducts)) {
                    $returnData[array_search($item->id, $internalProducts)] = "$value->title - $item->title";
                }
            }
        }
        return $returnData;
    }

    public function getOfferTable(Request $request)
    {
        $shop = \ShopifyApp::shop();
        $offerList = DB::table('offer')
            ->select('offer.id', 'offer.name', 'offer.type', DB::raw('count(report_view.id) as total'))
            ->where('offer.shop_id', $shop->id)
            ->leftJoin('report_view', 'report_view.offer_id', '=', 'offer.id')
            ->groupBy('offer.id')
            ->paginate(5);
        $purchase = DB::table('report_sale')
            ->select('offer_id',DB::raw('count(offer_id) as total'), DB::raw('sum(amount) as amount'))
            ->where('shop_id', $shop->id)
            ->groupBy('offer_id')
            ->get();
        $appendData = [];
        foreach ($purchase as $value){
            $appendData[$value->offer_id] = ['added'=>$value->total,'amount'=>$value->amount];
        }
        $view = view('report.offerTable')->with(['offerList' => $offerList,'append_data'=>$appendData])->render();
        return response()->json(['success' => true, 'view' => $view]);
    }

    public function getDashboard()
    {
        return view('welcome');
    }

    public function getGeneralData(Request $request)
    {
        $shop = \ShopifyApp::shop();
        $currency = $shop->api()->rest('GET', '/admin/shop.json')->body->shop->currency;
        $startDay = \Carbon\Carbon::parse($request->get('start'));
        $endDay = \Carbon\Carbon::parse($request->get('end'));
        $view = DB::table('report_view')
            ->where('shop_id', $shop->id)
            ->whereDate('created_at', '>=', $startDay)
            ->whereDate('created_at', '<=', $endDay)
            ->count();
        $addedToCart = DB::table('report_sale')
            ->where('shop_id', $shop->id)
            ->whereDate('created_at', '>=', $startDay)
            ->whereDate('created_at', '<=', $endDay)
            ->count();
        $revenues = DB::table('report_sale')
            ->select(['tracking_code', 'amount'])
            ->distinct('tracking_code')
            ->whereDate('created_at', '>=', $startDay)
            ->whereDate('created_at', '<=', $endDay)
            ->where('is_purchase', 1)
            ->get();
        $totalRevenue = 0;
        $numberPurchase = 0;
        foreach ($revenues as $value) {
            $totalRevenue += $value->amount;
            $numberPurchase++;
        }
        $conversion = ($numberPurchase / $addedToCart) * 100;
        $highestOffer = $this->getViewByOffer($startDay, $endDay, 'desc');
        $lowestOffer = $this->getViewByOffer($startDay, $endDay, 'asc');
        $listAddedCart = $this->getListAddedCart($startDay, $endDay);
        $highestConversion = $this->getViewByConversion($startDay, $endDay, 'asc', $listAddedCart, $currency);
        $lowestConversion = $this->getViewByConversion($startDay, $endDay, 'desc', $listAddedCart, $currency);
        return response()->json(['view' => $view,
            'lowestView' => $lowestOffer,
            'highestView' => $highestOffer,
            'highestConversion' => $highestConversion,
            'lowestConversion' => $lowestConversion,
            'added_cart' => $addedToCart,
            'revenue' => round($totalRevenue, 2) . " $currency",
            'conversion' => round($conversion, 2) . '%'
        ]);
    }

    public function getViewByOffer($startDate, $endDate, $sort)
    {
        $shop = \ShopifyApp::shop();
        $view = DB::table('report_view')
            ->select('offer.id', 'offer.name', DB::raw('count(report_view.offer_id) as total'))
            ->where('report_view.shop_id', $shop->id)
            ->whereDate('report_view.created_at', '>=', $startDate)
            ->whereDate('report_view.created_at', '<=', $endDate)
            ->groupBy('offer_id')
            ->orderBy('total', $sort)
            ->join('offer', 'report_view.offer_id', '=', 'offer.id')
            ->limit(3)
            ->get();
        $orderViews = [];
        foreach ($view as $value) {
            array_push($orderViews, ['offer_name' => $value->name, 'offer_view' => $value->total, 'id' => $value->id]);
        }
        return $orderViews;
    }

    public function getViewByConversion($startDate, $endDate, $sort, $orderAdded, $currency)
    {
        $shop = \ShopifyApp::shop();
        $purchase = DB::table('report_sale')
            ->select('offer_id', 'is_purchase', 'created_at', 'amount')
            ->where('is_purchase', 1)
            ->where('shop_id', $shop->id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select('offer_id', DB::raw('count(offer_id) as total'), DB::raw('sum(amount) as amount'))
            ->groupBy('offer_id')
            ->get();
        $orderPurchase = [];
        foreach ($purchase as $value) {
            $orderPurchase[$value->offer_id]['total'] = $value->total;
            $orderPurchase[$value->offer_id]['amount'] = $value->amount;
        }
        foreach ($orderAdded as $key => $value) {
            $orderAdded[$key]['conversion'] = round(($orderPurchase[$key]['total'] / $value['offer_added']) * 100, 2);
            $orderAdded[$key]['amount'] = round($orderPurchase[$key]['amount'], 2) . " $currency";
        }
        if ($sort == 'asc') {
            usort($orderAdded, function ($a, $b) {
                return $b['conversion'] <=> $a['conversion'];
            });
        } else {
            usort($orderAdded, function ($a, $b) {
                return $b['conversion'] <=> $a['conversion'];
            });
        }
        return $orderAdded;
    }

    public function getListAddedCart($startDate, $endDate)
    {
        $shop = \ShopifyApp::shop();
        $orderAdded = [];
        $addedCart = DB::table('report_sale')
            ->select('offer.id', 'offer.name', DB::raw('count(report_sale.offer_id) as total'))
            ->where('report_sale.shop_id', $shop->id)
            ->whereDate('report_sale.created_at', '>=', $startDate)
            ->whereDate('report_sale.created_at', '<=', $endDate)
            ->groupBy('offer_id')
            ->join('offer', 'report_sale.offer_id', '=', 'offer.id')
            ->get();
        foreach ($addedCart as $value) {
            $orderAdded[$value->id] = ['offer_name' => $value->name, 'offer_added' => $value->total, 'id' => $value->id];
        }
        return $orderAdded;
    }
}
