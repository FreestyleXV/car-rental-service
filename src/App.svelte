<script>
	import Tailwindcss from './Tailwindcss.svelte';
	import Router from "svelte-spa-router"

	import Home from './Home.svelte'
import Cars from './Cars.svelte';
import Messages from './Messages.svelte';
import Users from './Users.svelte';
import Reservation from './Reservation.svelte';
</script>

<Tailwindcss />
<Router routes={{
	'/cars': Cars,
	'/messages': Messages,
	'/users': Users,
	'/reservations': Reservation,
	'/home':Home,
	'/': Home,
	'*':Home,
}} />
