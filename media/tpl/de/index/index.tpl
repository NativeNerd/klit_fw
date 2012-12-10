{include (_master/header.tpl)}

Index

{foreach ($test)}
    {$.key} => {$.value:time}
{/foreach}

Index end

{include (_master/footer.tpl)}