<?php

$config = array(
	"server" => "ws.sxisa.org:",  // your server ip or domain
	"port" => "10240"
);
// prevent the server from timing out
set_time_limit(0);

// include the web sockets server script (the server is started at the far bottom of this file)
require 'class.PHPWebSocket.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	// check if message length is 0
	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}

	//The speaker is the only person in the room. Don't let them feel lonely.
	if ( $message == "{::List}" )
	{
		$i = 0;
		$list = array("list" => array());
		foreach ( $Server->wsClients as $id => $client )
			if ( $id != $clientID )
				$list["list"][$i++] = array(
					'id' => $id,
					'ip' => $ip,
				);
		$Server->wsSend($clientID, json_encode($list));
	}
	else
		//Send the message to everyone but the person who said it
		foreach ( $Server->wsClients as $id => $client )
			if ( $id != $clientID )
				$Server->wsSend($id, json_encode(array(
					'user' => $arrayName = array(
						'id' => $clientID,
						'ip' => $ip,
					),
					'status' => 'sendmsg',
					'msg' => $message,
				)
			));
}

// when a client connects
function wsOnOpen($clientID)
{
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has connected." );

	//Send a join notice to everyone but the person who joined
	foreach ( $Server->wsClients as $id => $client )
		if ( $id != $clientID )
			$Server->wsSend($id, json_encode(array(
				'user' => array(
					'id' => $clientID,
					'ip' => $ip,
				),
				'status' => 'connect',
				'msg' => '',
			)
		));
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has disconnected." );

	//Send a user left notice to everyone in the room
	foreach ( $Server->wsClients as $id => $client )
		$Server->wsSend($id, json_encode(array(
			'user' => $arrayName = array(
				'id' => $clientID,
				'ip' => $ip,
			),
			'status' => 'disconnect',
			'msg' => '',
		)
	));
}

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
$Server->wsStartServer($config["server"], $config["port"]);

?>
