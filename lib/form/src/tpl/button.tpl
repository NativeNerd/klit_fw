<button
    type="{$type}"
    {if. haslength($name)} name="{$name}"
    {if. $disabled==true} disabled="disabled"
    >{$value}</button>
