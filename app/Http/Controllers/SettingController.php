<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Config;
use OhMyBrew\ShopifyApp\ShopifyApp;

class SettingController extends Controller
{
    const CONFIG_TITLE = 'general_title';
    const CONFIG_DESCRIPTION = 'general_description';
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var ShopifyApp
     */
    protected $shop;

    /**
     * SettingController constructor.
     * @param Config $config
     * @param ShopifyApp $shop
     */
    public function __construct(Config $config,ShopifyApp $shop)
    {
        $this->config = $config;
        $this->shop = $shop;
    }

    public function index()
    {
        $shop = $this->shop->shop();
        $configModel = $this->config;
        $title = $configModel->where(['path'=>self::CONFIG_TITLE,'shop_id'=>$shop->id])->first()->value ?? '';
        $description = $configModel->where(['path'=>self::CONFIG_DESCRIPTION,'shop_id'=>$shop->id])->first()->value ?? '';
        return view('config.index')->with(['title'=>$title,'description'=>$description]);
    }

    public function save(Request $request)
    {
        $title = $request->input('offer_title');
        $description = $request->input('offer_description');
        $shop = $this->shop->shop();
        $configModel = $this->config;
        $titleModel = $configModel->where(['path'=>self::CONFIG_TITLE,'shop_id'=>$shop->id])->first() ?? new Config;
        $titleModel->fill(['path'=>self::CONFIG_TITLE,'shop_id'=>$shop->id,'value'=>$title])->save();
        $descriptionModel = $configModel->where(['path'=>self::CONFIG_DESCRIPTION,'shop_id'=>$shop->id])->first() ?? new Config;
        $descriptionModel->fill(['path'=>self::CONFIG_DESCRIPTION,'shop_id'=>$shop->id,'value'=>$description])->save();
        return back()->with('status','Your settings have been successfully saved');
    }
}
