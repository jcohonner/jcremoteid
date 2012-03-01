{def $content=$attribute.content}

<table class="list">
    <tr>
        <th scope="col">{'Scope'|i18n('remoteid/update')}</th>
        <th scope="col">{'Remote ID'|i18n('remoteid/update')}</th>
    </tr>
    <tr class="bglight">
        <td>{'Object'|i18n('remoteid/update')}</td>
        <td>{$content.object}</td>
    </tr>
    {foreach $content.node_list as $node_id => $remote_id sequence array('bgdark','bglight') as $css}
    <tr class="{$css}">
        <td>{'Node %1'|i18n('remoteid/update','',hash('%1',$node_id))}</td>
        <td>{$remote_id}</td>
    </tr>
    {/foreach}
</table>