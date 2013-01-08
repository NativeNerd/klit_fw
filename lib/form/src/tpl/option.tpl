<option
    {if. haslength($id)} id="{$id}"
    value="{$name}"
    {if. haslength($label)} label="{$label}"
    {if. $disabled==true} disabled="disabled"
    {if. $selected==true} selected="selected"
    >{$value}</option>
