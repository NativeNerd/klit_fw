{% include '_master/header.tpl' %}

<h1>{{ test }}</h1>

<form action="" method="post" id="login">
	<input type="text" name="benutzer" /><br>
	<input type="password" name="pw" /><br>
	<input type="submit" value="einloggen" >
</form>

{% include '_master/footer.tpl' %}
