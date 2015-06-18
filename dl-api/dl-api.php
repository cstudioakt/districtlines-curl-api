<?php
	
	class DLApi {
		private $apiKey = '';
		private $activeCategory = null;
		private $cacheLocation;
		public $cdnURL = 'https://d3eum8lucccgeh.cloudfront.net/designs/';
		public $products = null;
		public $categories = null;
		public $_config = array(
			'apiKey' => '',
			'activeCategory' => null
		);
		
		public function __construct($params = array()) {
			$this->cacheLocation = dirname(__FILE__).'/cache/';
			
			$params = array_merge($this->_config, $params);
			
			$this->apiKey = $params['apiKey'];
			
			if( !isset($this->apiKey) || empty($this->apiKey) ) {
				echo 'You must supply an API Key.';
				die();
			}
			
			$this->activeCategory = $params['activeCategory'];
		}
		
		public function run() {
			$this->products = $this->getProducts();
			$this->categories = $this->getCategories();
		}
	
		private function qCurl($params) {
			if( $this->checkCache($params) ) {
				return json_decode($this->getFromCache($params));
			}
			
			$ch = curl_init();    // initialize curl handle
			$paramsQuery = http_build_query($params);
			curl_setopt($ch, CURLOPT_URL, 'http://www.districtlines.com/api/main.php'); // set url to post to
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
			curl_setopt($ch, CURLOPT_TIMEOUT, 5); // times out after 5s
			curl_setopt($ch, CURLOPT_POST, 1); // set POST method
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsQuery); // add POST fields
			$result = curl_exec($ch); // run the whole process
			curl_close($ch);
			
			$this->saveToCache($params, $result);
			return json_decode($result);
		}
		
		public function getProducts() {
			$curl_params = array(
				'api_key' 	=> $this->apiKey,
				'action' 	=> 'products',
				'o_sort' 	=> 'ASC'
			);
			
			return $this->qCurl($curl_params);
		}
		
		public function getCategories() {
			$curl_params = array(
				'api_key' 	=> $this->apiKey,
				'action' 	=> 'categories',
				'o_sort' 	=> 'ASC'
			);
			
			return $this->qCurl($curl_params);
		}
		
		private function checkCache($params) {
			if( !isset($params) || !is_array($params) ) {
				return false;
			}
			
			$md5_checksum = self::getChecksum($params);
			$file = $this->cacheLocation . $md5_checksum . '.txt';

			if( file_exists($file) && $this->checkFileAge($file) ) {
				return true;
			} else {
				return false;
			}
		}
		
		private function getFromCache($params) {
			$md5_checksum = self::getChecksum($params);
			
			return file_get_contents($this->cacheLocation . $md5_checksum . '.txt');
		}
		
		private function saveToCache($params = null, $result = null) {
			if( !isset($params) || !isset($result) ) {
				error_log('Unable to save the cache file. $results || $params were null.');
				return false;
			}
			
			if( count($result) < 1 ) {
				error_log('Unable to save the cache file. Result was not correct.');
				return false;
			}
			if( is_object($result) ) {
				$result = json_encode($result);
			}
			
			$md5_checksum = self::getChecksum($params);
			$fileName = $md5_checksum.'.txt';
			
			$file = fopen(dirname(__FILE__) . '/cache/' . $fileName, 'w+');
			
			
			if( !fwrite($file, $result) ) {

				error_log('Cache file was not saved. Failed during file_put_contents.');
			}
			
			fclose($file);
			
		}
		
		private static function checkFileAge($file) {
			if( time() - filemtime($file) > 300 ) {
				return false;
			} else {
				return true;
			}
		}
		
		private static function getChecksum($params) {
			return md5(serialize($params));
		}
	
		public static function truncate($string, $length, $stopanywhere=false) {
			//truncates a string to a certain char length, stopping on a word if not specified otherwise.
			if (strlen($string) > $length) {
				//limit hit!
				$string = substr($string,0,($length -3));
				
				if ($stopanywhere) {
					//stop anywhere
					$string .= '...';
				} else{		
					//stop on a word.
					$string = substr($string,0,strrpos($string,' ')).'...';
				}
			}
			return $string;
		}
	}