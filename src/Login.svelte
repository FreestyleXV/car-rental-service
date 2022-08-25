<script>
    import { createEventDispatcher } from 'svelte';
    import { storedLogin, storedPermissions, storedId } from './stores'
    
    let register = false
    let login = ""
    let mail = ""
    let password = ""
    let message = ""
    let confirm = ""

    const dispatch = createEventDispatcher();

    function changeType() {
        register = !register
        login = ""
        mail = ""
        password = ""
        message = ""
        confirm = ""
    }

    function handleLogin() {
        if(login !="" && password !=""){
            let body = new FormData()
            body.append('login', login)
            body.append('password', password)
            fetch("http://localhost/Svelte/Svelte/public/backend/login.php", {
                method:"POST",
                body:body
            }).then(res => {
                return res.json()
            }).then(text => {
                if(text[0]=="ok"){
                    storedId.set(text[2])
                    storedLogin.set(login)
                    storedPermissions.set(text[1])
                    sessionStorage.setItem("id", text[2])
                    sessionStorage.setItem("login", login)
                    sessionStorage.setItem("permissions", text[1])
                    message = ""
                    dispatch('login', {
                        logged:true
                    });
                    
                }
                else{
                    message = "Niepoprawny login lub hasło"
                }
            })
        }
        else{
            message = "Oba pola nie mogą być puste"
        }
    }

    function handleRegister() {
        if(login == "" || mail == "" || password == "" || confirm == ""){
            message = "Wszystkie pola muszą być wypełnione"
        }
        else if(confirm != password){
            message = "Hasła muszą się zgadzać"
        }
        else{
            let body = new FormData()
            body.append('login', login)
            body.append('mail', mail)
            body.append('password', password)
            fetch("http://localhost/Svelte/Svelte/public/backend/register.php", {
                method:"POST",
                body:body
            }).then(res => {
                return res.json()
            }).then(text => {
                if(text[0]=='ok'){
                    message = ""
                    register = false
                    
                }
                else{
                    message = "Użytkownik o podanym loginie już istnieje"
                }
            })
        }   
    }
</script>


<div class="lg:w-2/6 md:w-1/2 bg-gray-100 rounded-lg p-8 flex flex-col md:ml-auto w-full mt-0 absolute right-0 md:m-5 z-50">
    {#if register == false}
        <h2 class="text-gray-900 text-lg font-medium title-font mb-5">Sign In</h2>
        <div class="relative mb-4">
            <label for="login" class="leading-7 text-sm text-gray-600">Login</label>
            <input bind:value={login} type="text" name="login" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
        </div>
        <div class="relative mb-4">
            <label for="password" class="leading-7 text-sm text-gray-600">Password</label>
            <input bind:value={password} type="password" name="password" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
        </div>
        <button on:click="{handleLogin}" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Button</button>

        {#if message.length > 0}
            <p class="text-xs text-red-600 mt-3">{message}</p>
        {/if}
        <p class="text-xs text-gray-500 mt-3">You don't have an account? <strong class="cursor-pointer" on:click="{changeType}">Sign up!</strong></p>
    {:else}
        <h2 class="text-gray-900 text-lg font-medium title-font mb-5">Sign Up</h2>
        <div class="relative mb-4">
            <label for="login" class="leading-7 text-sm text-gray-600">Login</label>
            <input bind:value={login} type="text" name="login" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
        </div>
        <div class="relative mb-4">
            <label for="mail" class="leading-7 text-sm text-gray-600">Mail</label>
            <input bind:value={mail} type="mail" name="mail" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
        </div>
        <div class="relative mb-4">
            <label for="password" class="leading-7 text-sm text-gray-600">Password</label>
            <input bind:value={password} type="password" name="password" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
        </div>
        <div class="relative mb-4">
            <label for="confirm-password" class="leading-7 text-sm text-gray-600">Confirm Password</label>
            <input bind:value={confirm} type="password" name="confirm-password" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
        </div>
        <button on:click="{handleRegister}" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Button</button>

        {#if message.length > 0}
            <p class="text-xs text-red-600 mt-3">{message}</p>
        {/if}
        <p class="text-xs text-gray-500 mt-3">Already have an account? <strong class="cursor-pointer" on:click="{changeType}">Sign in!</strong></p>
    {/if}
</div>