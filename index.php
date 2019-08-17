<?php

error_reporting(0);
include $_SERVER['DOCUMENT_ROOT'].'/app/database.php';
$DATABASE = new DATABASE();

// v1.3: universal page renderer
function renederPage( $url )
{
    $sitePath = $_SERVER['SERVER_NAME'];
    global $link;
    global $msg;
    include $_SERVER['DOCUMENT_ROOT']."/views/_head.html";
    include $_SERVER['DOCUMENT_ROOT']."/views/$url.html";
    include $_SERVER['DOCUMENT_ROOT']."/views/_end.html";
}

if ( $_GET['page'] == 'index' ) { renederPage( $_GET['page'] ); }
if ( $_GET['page'] == '404' ) { renederPage( $_GET['page'] ); }

if ( $_GET['page'] == 'getlink' )
{
    // v1.3: link generator remastered
    $link = hash(crc32, date("YmdHis") + rand(1000, 9999) );
    $text = $_POST['text'];
    $created = gmdate('U');
    $lifetime = $_POST['lifetime'];

    $msg = $DATABASE->add_msg($link, $text, $created, $lifetime);

    renederPage( $_GET['page'] );
}

if ( $_GET['page'] == 'msg' )
{
    $link = $_GET['link'];
    renederPage( $_GET['page'] );
}

if ( $_GET['page'] == 'read' )
{
    $msg = $DATABASE->load_msg($_GET['link']);

    if ( !empty($msg) )
    {
        renederPage( $_GET['page'] );
        $DATABASE->delete_msg($_GET['link']);
    }
    else { renederPage( 'null' ); }
}

// deleting trash

$data = $DATABASE->load_all();

if ( $data != null )
{
  foreach ($data as $msg) {
    $now = gmdate('U');
    if ( $msg['created']+$msg['lifetime'] < $now )
    {
      $DATABASE->delete_msg( $msg['link'] );
    }
  }
}

?>
