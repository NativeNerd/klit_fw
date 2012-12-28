{include (_master/header.tpl)}

<h1>Testformular</h1>

{$error}<br />
<br />
{form (:header)}
{label (username)} {form (username)}<br />
{label (password)} {form (password)}<br />
{label (gender)} {form (gender)}<br />
{label (age)} {form (age)}<br />
{form (submit)}
{form (:footer)}

{include (_master/footer.tpl)}