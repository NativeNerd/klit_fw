<input
    {if. haslength($id)} id="{$id}"
    type="{$type}"
    name="{$name}"
    {if. haslength($value)} value="{$value}"
    {if. $readonly==true} readonly="readonly"
    {if. haslength($maxlength)} maxlength="{$maxlength}"
    {if. $disabled==true} disabled="disabled"
    {if. $checked==true} checked="checked"
/>
