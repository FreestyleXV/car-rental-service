<script>
import Header from "./Header.svelte";
import Message from "./Message.svelte";

let login = sessionStorage.getItem('login')

let messages = getMessages()

async function getMessages() {
    let res = await fetch("http://localhost/Svelte/Svelte/public/backend/messages.php", {
        method:"POST",
        headers:{
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'login='+login
    })
    let messages = await res.json()
    return messages
}


</script>

<Header/>

<section class="text-gray-600 body-font overflow-hidden">
    <div class="container px-5 py-24 mx-auto flex flex-col-reverse">
        {#await messages}
            loading messages
        {:then messages} 
            {#each messages as message, i}
                <Message message={message} i={i}/>
            {/each}
        {/await}
        
    </div>
</section>