#!/usr/bin/php
<?php
require_once '/usr/share/php/PhpAmqpLib/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Channel\AbstractChannel;
require_once('mysqlHelp.php.inc'); 
require_once('moviedata.php.inc');
function doLogin ($request)
{
	$login = new loginDB();
	$result = $login->getInfo($request);
	
	if($result) {
		return true ;
	}
	else{
		return false ;
	}

}

function newRegister($request) {
	$register = new loginDB();
	$result = $register->newUser($request);
	if($result){
		return true;
	}
	else
		return false;
}

function searchM($request){
	$moviedb = new movieDB();
	$movie_str = $request['movie'];
	$movie = $moviedb->movieSearch($movie_str);
	return $movie;
}

function newsFeed($request){
	$moviedb = new movieDB();
	$movie_str = $request['upcoming'];
	$movie = $moviedb->upcomingMovies($movie_str);
	return $movie;
}

function requestProcessor($request){
	echo "Request Received".PHP_EOL;
	var_dump($request);
	echo '\n' . 'End Message';
	if(!isset($request['type']))
	{
		return "ERROR: unsupported message type";
	}
	switch($request['type'])
	{
		case "register":
			return newRegister($request);
		case "login":
			return doLogin($request);
		case "searchM":
			return searchM($request);
		case "newsfeed":
			return newsFeed($request);
//	return "Request received and processed!";
	}
}


$connection = new AMQPStreamConnection('192.168.43.125', 5672, 'IT490', '12345', 'Login');
$channel_send = $connection->channel();
$channel_rec = $connection->channel();
$channel_send->queue_declare('login_recieve', false, true, false, false);

echo " [x] Awaiting RPC request\n";
$callback = function($req) {
        
// test code >>>        echo $req->body;
//         $msg = new AMQPMessage($req->body, array('delivery_mode' => 2));
//         global $channel_send;
//         $channel_send->basic_publish($msg, '', 'login_recieve');
//<<< test code         $channel_send->close();
        $n = $req->body;
	$json_message = json_decode($req, true);
	$json_message = json_decode($n, true);
	echo " [.] Received(", $json_message['type'], ")\n";
	$resl = requestProcessor($json_message);
	//echo " [.] Sent(", $resl, ")";
	$results = json_encode($resl, true);
	$msg = new AMQPMessage(
		(string) $results,
		array('correlation_id'=> $req->get('correlation_id'))
	);
        $channel_send->basic_publish($msg, '', 'login_recieve');
	//$req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));
	$req->delivery_info['channel']->basic_ack($req->delivery_info['delivery_tag']);
};

$channel_rec->basic_qos(null, 1, null);
$channel_rec->basic_consume('login_send', '', false, true, false, false, $callback);

while(count($channel_rec->callbacks)){
	$channel_rec->wait();
}

$channel_rec->close();
$connection->close();
?>
