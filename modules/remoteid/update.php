<?php

$Module = $Params['Module'];
$NodeID = $Params['NodeID'];
$node   = false;

$tpl = eZTemplate::factory();


if ($node = eZContentObjectTreeNode::fetch($NodeID))
{

    if ($Module->currentAction()=='UpdateRemoteID')
    {
        //tests values
        $inputObjectName = 'jcremoteid_object';
        $inputNodeListName = 'jcremoteid_node_list';

        $http = new eZHTTPTool();
        $errors = array();
        $object = $node->attribute('object');

        if ($http->hasPostVariable($inputObjectName)
                && jcRemoteID::isValidObjectRemoteID($http->postVariable($inputObjectName),$object->attribute('id'),$errors))
        {
            $object->setAttribute('remote_id',$http->postVariable($inputObjectName));
            $object->store();
        }

        if ($http->hasPostVariable($inputNodeListName)
                && is_array($http->postVariable($inputNodeListName)))
        {
            $remoteIDNodeList = $http->postVariable($inputNodeListName);

            foreach ($object->attribute('assigned_nodes') as $node_item)
            {
                $node_item_id=$node_item->attribute('node_id');
                if (isset($remoteIDNodeList[$node_item_id])
                        && jcRemoteID::isValidNodeRemoteID($remoteIDNodeList[$node_item_id], $node_item_id,$errors))
                {
                    $node_item->setAttribute('remote_id',$remoteIDNodeList[$node_item_id]);
                    $node_item->store();
                    
                    if ($node_item_id==$NodeID)
                    {
                        //update $node for template if errors
                        $node=$node_item;
                    }
                }
            }
        }

        //If ok -> send data
        if (empty($errors))
        {
            //Clear cache of this object
            eZContentCacheManager::clearContentCache($object->attribute('id'));
            //Redirect to node
            $Module->redirectTo('content/view/full/'.$NodeID);
            return;
        }

        $tpl->setVariable('errors', $errors);
        $tpl->setVariable('new_object_remote_id', $http->postVariable($inputObjectName));
        $tpl->setVariable('new_node_remote_id_list', $remoteIDNodeList);
    }

} else {
    //Wrong parameters    
}

$tpl->setVariable('node',$node);
$tpl->setVariable('error_list',$errors);
$Result['content']=$tpl->fetch('design:remoteid/update.tpl');


?>
