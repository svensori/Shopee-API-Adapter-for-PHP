<?php 
header('Content-Type: application/json');

class ShopeeOpenPlatform{
	public static $host;
	public static $url;
	public static $link_back;

	protected static $partner_id;
	protected static $partner_key;
	protected static $shop_id;
		
	public static function buildAuthURL(){
		return self::$url."api/v1/shop/auth_partner?id=".self::$partner_id."&token=".hash('sha256', self::$partner_key.self::$link_back)."&redirect=".urlencode(self::$link_back);
	}

	public static function setPartnerID($id){
		if (!is_int($id))
			trigger_error('Partner ID must be integer', E_USER_WARNING);
		else 
			self::$partner_id = $id;
	}

	public static function setShopID($id){
		if (!is_int($id))
			trigger_error('Shop ID must be integer', E_USER_WARNING);
		else 
			self::$shop_id = $id;
	}

	public static function setPartnerKey($key){
		self::$partner_key = $key;
	}

}

class APIService{
	public static function sendRequest(){

	}
}

class ShopeeAPI extends ShopeeOpenPlatform{
	private $api;
	private $header;
	private $request_params = array();

	public function __construct(){
		$this->end_point = array(
			'getItemDetail' 		=> 'api/v1/item/get',
			'getItemList' 			=> 'api/v1/items/get',
			'updateVariationStock' 	=> 'api/v1/items/update_variation_stock',
			'updateVariationPrice' 	=> 'api/v1/items/update_variation_price',
			'getOrderList' 			=> 'api/v1/orders/basics',
			'getShopInfo' 			=> 'api/v1/shop/get',
			'getShopPerformance' 	=> 'api/v1/shop/performance'
		);
	}

	public function getItemDetail($item_id){
		$this->api = $this->end_point[__FUNCTION__];
		$this->request_params = array(
			'item_id' => $item_id
		);
		$this->sendRequest();
	}

	public function getItemList($offset, $entries_per_page){
		$this->api = $this->end_point[__FUNCTION__];
		$this->request_params = array(
			'pagination_offset' => $offset,
			'pagination_entries_per_page' => $entries_per_page
		);
		$this->sendRequest();
	}

	public function getShopInfo(){
		$this->api = $this->end_point[__FUNCTION__];
		$this->sendRequest();
	}

	public function getShopPerformance(){
		$this->api = $this->end_point[__FUNCTION__];
		$this->sendRequest();
	}

	public function updateVariationStock($item_id, $variation_id, $stock){
		$this->api = $this->end_point[__FUNCTION__];
		$this->request_params = array(
			'item_id' => $item_id,
			'variation_id' => $variation_id,
			'stock' => $stock
		);
		$this->sendRequest();
	}

	public function updateVariationPrice($item_id, $variation_id, $price){
		$this->api = $this->end_point[__FUNCTION__];
		$this->request_params = array(
			'item_id' => $item_id,
			'variation_id' => $variation_id,
			'price' => $price
		);
		$this->sendRequest();
	}

	private function createRequestHeader($post_data){
		$header =  array(
			'Host: '.parent::$host,
			'Content-Type: application/json',
			'Content-Length: '.strlen(json_encode($post_data)),
			'Authorization: '.hash_hmac("sha256", parent::$url.$this->api.'|'.json_encode($post_data), parent::$partner_key)
		);
		return $header;
	}

	private function sendRequest(){
		$required = array(
			'partner_id' => parent::$partner_id,
			'shopid' => parent::$shop_id,
			'timestamp' => time()
		);
		$post_fields = array_merge($required, $this->request_params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, parent::$url.$this->api);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->createRequestHeader($post_fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
		curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
		curl_exec($ch);
		curl_close($ch);
	}
}

/*
*Set Credentials
*/
ShopeeOpenPlatform::$host = 'partner.uat.shopeemobile.com';
ShopeeOpenPlatform::$url = 'https://partner.uat.shopeemobile.com/';
ShopeeOpenPlatform::$link_back = ''; //Required
ShopeeOpenPlatform::setPartnerID(''); //Required
ShopeeOpenPlatform::setPartnerKey(''); //Required

/*
*Returns Authorization URL
*echo ShopeeOpenPlatform::buildAuthURL(); //Run this on initial load to get Auth URL where you can get the Shop ID.  
*/

/*
*Set shop ID which came from authorization
*/
// ShopeeOpenPlatform::setShopID(<PUT SHOP_ID HERE W/C CAME FROM AUTH URL>); 

/*
*Instantiate API
*/
// $API = new ShopeeAPI();

// $API->getShopPerformance();

/*
*Use this call to get detail of item
*@param1 - item_id
*$API->getItemDetail(1518982);
*/

/*
*Use this call to get a list of items
*@param1 - pagination_offset
*@param2 - pagination_entries_per_page
*$API->getItemList(0, 100);
*/

/*
*Use this call to get information of shop
*$API->getShopInfo();
*/

/*
*Use this call to update item variation stock
*@param1 - item_id
*@param2 - variation_id (optional)
*@param3 - quantity
*$API->updateVariationStock(1518982, 1346778, 900);
*/


/*
*Use this call to update item variation stock
*@param1 - item_id
*@param2 - variation_id (optional)
*@param3 - price
*$API->updateVariationPrice(1518982, 1346778, 900);
*/
 ?>
