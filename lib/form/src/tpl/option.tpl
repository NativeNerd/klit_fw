<option
    {if. haslength($id)} id="{$id}"
    value="{$value}"
    {if. haslength($label)} label="{$label}"
    {if. $disabled==true} disabled="disabled"
    {if. $selected==true} selected="selected"
    >{$name}</option>
