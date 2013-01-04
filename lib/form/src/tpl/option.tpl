<option
    {if. haslength($id)} id="{$id}"
    value="{$name}"
    {if. haslength($label)} label="{$label}"
    {$disabled}
    {$selected}
    >{$value}</option>
