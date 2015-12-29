$ = jQuery.noConflict();



function add_input(a,b)
{
	b = b+1;
	var data = "<p>Previous Link <input type='url' required name='user_redirects["+b+"][pre_link]'> Next Link <input type='url' required name='user_redirects["+b+"][next_link]'> <input type='button'  value='add' onclick='add_input(this,"+b+")'> <input type='button'  value='remove' onclick='remove_input(this)'> </p>";
	$(a).parent().after(data);
}


function remove_input(a)
{
	$(a).parent().remove();
}