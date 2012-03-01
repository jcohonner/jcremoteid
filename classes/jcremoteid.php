<?php
/**
 * Utility class for jcRemoteID extension
 * Remote ID validation
 * @author Jérôme Cohonner
 */
class jcRemoteID
{

    /**
     * Validate Remote ID text (not empty and no unallowed characters)
     * @param string $remoteID
     * @param array $errors
     * @param boolean $allowBlank
     * @return boolean
     */
    static public function isValidRemoteIDText($remoteID,&$errors=array(),$allowBlank=false)
    {

        if (!$allowBlank && empty($remoteID))
        {
            $errors[]=ezpI18n::tr('remoteid/update','Remote ID cannot be blank');
            return false;
        }
        
        //Not allowed for practical reason only : "|;:"
        //$errors[]=ezpI18n::tr('remoteid/update','unallowed characters : "| ; :"');
        return true;
    }

    /**
     * test if the new object remote id is available (or already used by this object)
     * @param string $remoteID
     * @param int $currentObjectID
     * @param array $errors
     * @return boolean
     */
    static public function isValidObjectRemoteID($remoteID, $currentObjectID=false, &$errors=array())
    {

        
        if (!self::isValidRemoteIDText($remoteID,$errors))
        {                
            return false;
        }

        if ($existingObject = eZContentObject::fetchByRemoteID($remoteID, false))
        {
            if ($existingObject['id']!=$currentObjectID)
            {
                $errors[]=ezpI18n::tr('remoteid/update','Object Remote ID %1 already used by object %2','',array('%1'=>$remoteID,'%2'=>$existingObject['id']));
                eZDebug::writeDebug('Object Remote ID '.$remoteID.' used by object '.$existingObject['id'],'',__CLASS__);
                return false;
            }
        }
        
        return true;
    }

    /**
     * test if the new node remote id is available (or already used by this node)
     * @param string $remoteID
     * @param int $currentNodeID
     * @param array $errors
     * @param boolean $allowBlank
     * @return boolean
     */
    static public function isValidNodeRemoteID($remoteID,$currentNodeID=false,&$errors=array(),$allowBlank=false)
    {

        if (!self::isValidRemoteIDText($remoteID,$errors,$allowBlank))
        {
            return false;
        }

        if ($existingNode = eZContentObjectTreeNode::fetchByRemoteID($remoteID, false))
        {
            $existingNodeIDList = array();
            if (isset($existingNode['node_id']))
            {
                $existingNodeIDList[]=$existingNode['node_id'];
            } else {
                foreach ($existingNode as $node)
                {
                    $existingNodeIDList[]=$node['node_id'];
                }
            }
            

            if (!in_array($currentNodeID,$existingNodeIDList))
            {
                $errors[]=ezpI18n::tr('remoteid/update','Node Remote ID %1 already used by node %2','',array('%1'=>$remoteID,'%2'=>join(', ',$existingNodeIDList)));
                eZDebug::writeDebug('Node Remote ID '.$remoteID.' used by node '.join(', ',$existingNodeIDList),'',__CLASS__);
                return false;
            }
        }
        
        return true;
    }


}

?>
