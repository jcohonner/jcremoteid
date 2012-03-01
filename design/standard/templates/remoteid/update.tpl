<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

    <h1>{'Update Remote ID for %1'|i18n('remoteid/update','',hash('%1',$node.name|wash()))}</h1>


    {if $errors|count()|gt(0)}
        <ul>
        {foreach $errors as $error}
            <li>{$error}</li>
        {/foreach}
        </ul>
    {/if}


    <form action={concat('remoteid/update/',$node.node_id)|ezurl()} method="POST">
        <table class="list">
            <tr>
                <th scope="col">{'Scope'|i18n('remoteid/update')}</th>
                <th scope="col">{'Remote ID'|i18n('remoteid/update')}</th>
            </tr>
            <tr class="bglight">
                <td>{'Object'|i18n('remoteid/update')}</td>
                <td><input type="text" name="jcremoteid_object" value="{first_set($new_object_remote_id,$node.object.remote_id)}"  size="70"/></td>
            </tr>

            {foreach $node.object.assigned_nodes as $node sequence array('bgdark','bglight') as $css}
            <tr class="{$css}">
                <td>{'Node %1'|i18n('remoteid/update','',hash('%1',$node.node_id))}</td>
                <td><input type="text" name="jcremoteid_node_list[{$node.node_id}]" value="{first_set($new_node_remote_id_list[$node.node_id],$node.remote_id)}" size="70"/></td>
            </tr>
            {/foreach}
        </table>
        <input class="button" type="submit" name="UpdateRemoteID" value="{'Update'|i18n('remoteid/update')}"/>
    </form>


</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

