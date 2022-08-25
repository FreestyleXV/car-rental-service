<script>
    import { onMount } from "svelte";


    import Login from "./Login.svelte";
    import { storedLogin, storedPermissions, storedId } from "./stores";
    let id = sessionStorage.getItem('Id')
    let login = sessionStorage.getItem("login")
    let permissions = sessionStorage.getItem("permissions")
    let openLogin = false
    const loginEvent = new Event('login')

    function logout(){
        sessionStorage.clear();
        login="";
        permissions="";
        storedId.set("")
        storedLogin.set("")
        storedPermissions.set("")
        let href = location.href
        href = href.split("#")
        if(href.length > 1){
            href.pop()
        }
        href = href.join("#")
        location.href = href
    }

    onMount(async ()=>{
        storedId.update(()=>id==null?"":id)
        storedLogin.update(()=>login==null?"":login)
        storedPermissions.update(()=>permissions==null?"":permissions)
    })
</script>


<header class="text-gray-600 body-font">
    <div class="flex flex-wrap p-5 flex-col md:flex-row">
        <a href="./" class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
            <span class="ml-3 text-xl">Rent-a-Car</span>
        </a>
        <nav class="md:ml-auto md:mr-auto flex flex-wrap items-center text-base justify-center">
            <a href="./#/cars" class="mr-5 hover:text-gray-900">Cars</a>
            <a href="http://localhost/svelte/svelte/public/assets/rules.pdf" target="blank" class="mr-5 hover:text-gray-900">Rules</a>
            {#if permissions=="user"}
                <a href="./#/messages" class="mr-5 hover:text-gray-900">Messages</a>
            {:else if permissions=="mod"}
                <a href="./#/reservations" class="mr-5 hover:text-gray-900">Pending Reservations</a>
                <a href="./#/messages" class="mr-5 hover:text-gray-900">Messages</a>
            {:else if permissions=="admin"}
                <a href="./#/users" class="mr-5 hover:text-gray-900">Moderators</a>
                <a href="./#/messages" class="mr-5 hover:text-gray-900">Messages</a>
            {/if}
        </nav>
        {#if login}
            <button on:click="{logout}" class="inline-flex items-center bg-gray-100 border-0 py-1 px-3 focus:outline-none hover:bg-gray-200 rounded text-base mt-4 md:mt-0">Logout
                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
                    <path d="M5 12h14M11 5l7 7-7 7"></path>
                </svg>
            </button>
        {:else}
            <button on:click="{()=>{openLogin = !openLogin}}" class="inline-flex items-center bg-gray-100 border-0 py-1 px-3 focus:outline-none hover:bg-gray-200 rounded text-base mt-4 md:mt-0">Login
                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
                    <path d="M5 12h14M11 5l7 7-7 7"></path>
                </svg>
            </button>
        {/if} 
    </div>
</header>
{#if openLogin}
    <Login on:login={()=>{openLogin=false; login=sessionStorage.getItem("login"); permissions=sessionStorage.getItem("permissions"); window.dispatchEvent(loginEvent)}}/>
{/if}