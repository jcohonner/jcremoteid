<?php


$Module = array('name'=>'RemoteID');

$ViewList = array();
$ViewList['update']= array( 'script'=>'update.php',
                            'params'=>array('NodeID'),
                            'single_post_actions'=>
                                        array('UpdateRemoteID'
                                                => 'UpdateRemoteID'));




?>
