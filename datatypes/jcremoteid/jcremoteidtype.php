<?php



class jcRemoteIDType extends eZDataType
{

    const DATA_TYPE_STRING = "jcremoteid";

    /*!
      Constructeur
    */
    function __construct()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, "Remote ID" );
    }

    /*!
     Validates input on content object level
     \return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
             the values are accepted or not
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $returnCode = eZInputValidator::STATE_ACCEPTED;

        $inputObjectName = $base.'_jcremoteid_object_'.$contentObjectAttribute->attribute('id');
        $inputNewNodeName = $base.'_jcremoteid_nn_'.$contentObjectAttribute->attribute('id');
        $inputNodeName = $base.'_jcremoteid_node_list_'.$contentObjectAttribute->attribute('id');

        $errors = array();

        //Test object Remote ID
        if (!jcRemoteID::isValidObjectRemoteID($http->postVariable($inputObjectName),$contentObjectAttribute->attribute('contentobject_id'),$errors))
        {
            //Existing node
            $returnCode = eZInputValidator::STATE_INVALID;
        }

        //Test new node (for new objects) Remote ID
        if ($http->hasPostVariable($inputNewNodeName))
        {
            if (!jcRemoteID::isValidNodeRemoteID($http->postVariable($inputNewNodeName),false,$errors,true))
            {
                //Existing node
                $returnCode = eZInputValidator::STATE_INVALID;
            }
        }

        //Test each node remote ID value to make sure that it is not in use
        if (is_array($http->postVariable($inputNodeName)))
        {
            foreach ($http->postVariable($inputNodeName) as $nodeID => $remoteID)
            {
                if (!jcRemoteID::isValidNodeRemoteID($remoteID,$nodeID,$errors))
                {
                    //Existing node
                    $returnCode = eZInputValidator::STATE_INVALID;
                }
            }
        }

        //Update errors list
        $contentObjectAttribute->setValidationError(join(', ',$errors));

        return $returnCode;
    }


    /*!
     Fetches all variables from the object
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        //We store the


        $returnCode = eZInputValidator::STATE_ACCEPTED;

        $inputObjectName = $base.'_jcremoteid_object_'.$contentObjectAttribute->attribute('id');
        $inputNodeName = $base.'_jcremoteid_node_list_'.$contentObjectAttribute->attribute('id');
        $inputNewNodeName = $base.'_jcremoteid_nn_'.$contentObjectAttribute->attribute('id');

        $data_text='0';

        //Test new object ID
        if ($http->postVariable($inputObjectName)!='')
        {
            $data_text=$http->postVariable($inputObjectName);
        }

        $data_text .= '|';

        //Test new node
        //Test new node (for new objects) Remote ID
        if ($http->hasPostVariable($inputNewNodeName))
        {
            $remoteID = $http->postVariable($inputNewNodeName);
            if ($remoteID!='')
            {
                $data_text .= 'NN:'.$remoteID.';';
            }
        }

        //Test each node remote ID value to make sure that it is not in use
        if (is_array($http->postVariable($inputNodeName)))
        {
            foreach ($http->postVariable($inputNodeName) as $nodeID => $remoteID)
            {
                if ($remoteID!='')
                {
                    $data_text .= $nodeID.':'.$remoteID.';';
                }
            }
        }

        $contentObjectAttribute->setAttribute('data_text',$data_text);

        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {

        $result = array('object'=>false,'node_list'=>array());

        $object = $contentObjectAttribute->attribute('object');

        if ($object)
        {
            $result['object']=$object->attribute('remote_id');

            foreach ($object->attribute('assigned_nodes') as $node)
            {
                $result['node_list'][$node->attribute('node_id')]=$node->attribute('remote_id');
            }
        }

        //New Values (from data_text)
        $data_text = $contentObjectAttribute->attribute('data_text');
        if (trim($data_text)!='')
        {

            $mainTab = explode('|',$data_text);

            if ($mainTab[0]!='0' && $mainTab[0]!='')
            {
                $result['new_object_remote_id']=$mainTab[0];
            }

            if (isset($mainTab[1]))
            {
                foreach(explode(';',$mainTab[1]) as $nodeData)
                {
                    $nodeDataTab = explode(':',$nodeData);
                    $result['new_node_remote_id_list'][$nodeDataTab[0]]=$nodeDataTab[1];
                }
            }
        }

        return $result;
    }



    /*!
     Returns the value as it will be shown if this attribute is used in the object name pattern.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return "";
    }

    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable()
    {
        return false;
    }



    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
        $data_text = $contentObjectAttribute->attribute('data_text');
        if (trim($data_text)!='')
        {

            $mainTab = explode('|',$data_text);
            
            if ($mainTab[0]!='0' && trim($mainTab[0])!='' )
            {
                
                $object = $contentObjectAttribute->attribute('object');
                $object->setAttribute('remote_id',$mainTab[0]);
                $object->store();
            }


            if (isset($mainTab[1]))
            {
                foreach(explode(';',$mainTab[1]) as $nodeData)
                {
                    $nodeDataTab = explode(':',$nodeData);
                    $nodeRemoteIDList[$nodeDataTab[0]]=$nodeDataTab[1];
                }
            }

            if (!empty($nodeRemoteIDList))
            {
                if (count($publishedNodes)==1 && isset($nodeRemoteIDList['NN']))
                {
                    //New object only one node, not managing yet multiple assignement at create time
                    $node = $publishedNodes[0];
                    $node->setAttribute('remote_id',$nodeRemoteIDList['NN']);
                    $node->store();
                } else {
                    foreach ($publishedNodes as $node)
                    {
                        if (isset($nodeRemoteIDList[$node->attribute('node_id')]))
                        {
                            $node->setAttribute('remote_id',$nodeRemoteIDList[$node->attribute('node_id')]);
                            $node->store();
                        }
                    }
                }
            }

        }

    }

}

eZDataType::register( jcRemoteIDType::DATA_TYPE_STRING, "jcRemoteIDType" );
?>
