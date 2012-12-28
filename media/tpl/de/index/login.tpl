{include (_master/header.tpl)}

    Das sind deine Formulardaten:<br />
    <br />
    <b>Dein Name:</b> {$name}<br />
    <b>Dein Passwort:</b> {$password}<br />
    <b>Dein Geschlecht:</b> {$gender}<br />
    {if. haslength($age)} <b>Dein Alter:</b> {$age}<br />

{include (_master/footer.tpl)}