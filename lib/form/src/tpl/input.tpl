<input
    {if. haslength($id)} id="{$id}"
    type="{$type}"
    name="{$name}"
    {if. haslength($value)} value="{$value}"
    {$readonly}
    {$maxlength}
    {$disabled}
    {$checked}
/>
