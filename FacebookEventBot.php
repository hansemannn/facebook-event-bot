<?php 

require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';

class FacebookEventBot {
	const FACEBOOK_GRAPH_VERSION = 'v2.9';

	protected $userId;

	protected $appId;
	
	protected $appSecret;
	
	protected $accessToken;
	
	private $fb;
	
	public function __construct(array $config = []) {
		
		if (!isset($config['userId']) || 
			!isset($config['appId']) || 
			!isset($config['appSecret']) || 
			!isset($config['accessToken']) || 
			!isset($config['pageId']) ||
			!isset($config['matchingEvent'])) {
			throw new Exception('Required parameters userId, appId, appSecret, accessToken, matchingEvent or pageId missing!');
		}
		
		$this->appId = $config['appId'];
		$this->appSecret = $config['appSecret'];
		$this->accessToken = $config['accessToken'];
		$this->pageId = $config['pageId'];
		$this->matchingEvent = $config['matchingEvent'];
		
		$this->fb = new Facebook\Facebook([
		  'app_id' => $this->appId,
		  'app_secret' => $this->appSecret,
		  'default_graph_version' => static::FACEBOOK_GRAPH_VERSION,
		]);
	}
	
	public function attendAllEvents() {
		try {
			$response = $this->fb->get('/' . $this->pageId . '/events', $this->accessToken);
		  	$body = $response->getDecodedBody()['data'];
		  	$matching = array();
			$result = array();
		  
		  	foreach ($body as $value) {
				if (strpos($value['name'], $this->matchingEvent) !== false) {
					$matching[] = $value;
			  	}
		  	}
		     
		  	foreach ($matching as $event) {
				$item = array('title' => $event['name'], 'date' => $event['start_time']);
								
				if ($this->isAttending($event['id'], $userId) === false) {
					$attending = $this->fb->post('/'.$event['id'].'/attending', array(), $this->accessToken);		
					$item['new'] = true;
				} else {
					$item['new'] = false;
				}	
				
				$result[] = $item;
		  	}  
			
			return $result;
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		  	echo 'Graph returned an error: ' . $e->getMessage();
		  	return array();
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			return array();
		}
	}
	
	private function isAttending($_eventId) {
		$attending = $this->fb->get('/' . $_eventId . '/attending', $this->accessToken);
		$attendees = $attending->getDecodedBody()['data'];
	    	
		foreach ($attendees as $attendee) {
			if ($attendee['id'] == $this->userId) {
				return true;
			}
		}
			
		return false;
	}
}
