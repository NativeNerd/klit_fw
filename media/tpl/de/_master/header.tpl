<html>
    <head>
        <title>{{ title }}</title>
         <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
         <script>
         	$(document).ready(function(){
				$('#login').submit(function(){
					var s =  $( this ).serializeArray();
					submitForm( s );
					return false;
				});
         	});
         	
         	function submitForm( s ){
	
				$.ajax({
					type: "POST",
					url: "",
					data: s
				}).done(function( msg ) {
					var myData = jQuery.parseJSON( msg );
					if( myData.success ){
						alert( myData.message );
					}else{
						alert( myData.message );
					}
						
				});
				
			}
			         	
         </script>
    </head>
    <body>