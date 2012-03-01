{def    $content=$attribute.content
        $node_list=$content.node_list
        $sequenceCSS=array('bgdark','bglight')}
{if is_set($attribute_base)|not}
    {def $attribute_base='ContentObjectAttribute'}
{/if}

<table class="list">
    <tr>
        <th scope="col">{'Scope'|i18n('remoteid/update')}</th>
        <th scope="col">{'Remote ID'|i18n('remoteid/update')}</th>
    </tr>
    <tr class="bglight">
        <td>{'Object'|i18n('remoteid/update')}</td>
        <td><input type="text" name="{$attribute_base}_jcremoteid_object_{$attribute.id}" value="{first_set($content.new_object_remote_id,$content.object)}"/></td>
    </tr>
    {if $node_list|count()|eq(0)}
    <tr class="bgdark">
        <td>{'New node'|i18n('remoteid/update')}</td>
        <td><input type="text" name="{$attribute_base}_jcremoteid_nn_{$attribute.id}" value=""/></td>
    </tr>
        {set $sequenceCSS=array('bglight','bgdark')}
    {/if}
    {foreach $content.node_list as $node_id => $remote_id sequence $sequenceCSS as $css}
    <tr class="{$css}">
        <td>{'Node %1'|i18n('remoteid/update','',hash('%1',$node_id))}</td>
        <td><input type="text" name="{$attribute_base}_jcremoteid_node_list_{$attribute.id}[{$node_id}]" value="{first_set($content.new_node_remote_id_list[$node_id],$remote_id)}"/></td>
    </tr>
    {/foreach}
</table>