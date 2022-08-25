<script>
    import { onMount } from "svelte";
    
    import Header from "./Header.svelte";
import { storedPermissions } from "./stores";
    
    let users = []

    console.log($storedPermissions)

    let permissions = ""
    storedPermissions.subscribe(val=>{
        permissions = val
    })
    
    onMount(async ()=>{
        let usersFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getUsers.php")
        let usersFetchList = await usersFetchRes.json()
        console.log(usersFetchList);
        users = usersFetchList
    })
    
    async function editUser(id, permission){
        await fetch("http://localhost/Svelte/Svelte/public/backend/editUser.php",{
            method:"POST",
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id='+id+'&permission='+permission
        })
        let usersFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getUsers.php")
        let usersFetchList = await usersFetchRes.json()
        users = usersFetchList
    
    }
    
    async function sort(category){
        users = users.sort((a,b)=>{
    
            if(category=="id" || category=='car'){
                return a[category]-b[category]
            }
            else if(category=="status"){
                if(a[category]=='o'){
                    return -1
                }
                else if(b[category]=='o'){
                    return 1
                }
                else{
                    return a[category].localeCompare(b[category])
                }
            }
            else{
                return a[category].localeCompare(b[category])
            }
        })
    }
    
    </script>
    
    <Header/>
    
{#if permissions == "admin"}
    {#await users}
        loading
    {:then users}
        <table>
            <tr>
                <th on:click={()=>{sort('id')}} class=" hover:underline cursor-pointer">ID</th>
                <th on:click={()=>{sort('name')}} class=" hover:underline cursor-pointer">Nazwa</th>
                <th on:click={()=>{sort('mail')}} class=" hover:underline cursor-pointer">Mail</th>
                <th on:click={()=>{sort('permissions')}} class=" hover:underline cursor-pointer">Uprawnienia</th>
                <th>Action</th>
            </tr>
        {#each users as user}
            <tr>
                <td class=" p-2">{user.id}</td>
                <td class=" p-2">{user.name}</td>
                <td class=" p-2">{user.mail}</td>
                {#if user.permissions=="mod"}
                    <td class=" p-2 bg-yellow-300">MOD</td>
                {:else if user.permissions=="user"}
                    <td class=" p-2 bg-green-300">USER</td>
                {:else if user.permissions=="admin"}
                    <td class=" p-2 bg-red-300">ADMIN</td>
                {:else if user.permissions=="deactive"}
                    <td class=" p-2 bg-gray-300">INACTIVE</td>
                {/if}
                <td class=" flex-row">
                    {#if user.permissions=='user'}
                        <svg on:click={()=>{editUser(user.id, "mod")}} class=" mx-auto cursor-pointer" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>
                    
                    
                        <svg on:click={()=>{editUser(user.id, "deactive")}} class=" mx-auto cursor-pointer" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11 21.883l-6.235-7.527-.765.644 7.521 9 7.479-9-.764-.645-6.236 7.529v-21.884h-1v21.883z"/></svg>


                    {:else if user.permissions=='mod'}
                        <svg on:click={()=>{editUser(user.id, "user")}} class=" mx-auto cursor-pointer" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11 21.883l-6.235-7.527-.765.644 7.521 9 7.479-9-.764-.645-6.236 7.529v-21.884h-1v21.883z"/></svg>
                    {:else if user.permissions=='deactive'}
                        <svg on:click={()=>{editUser(user.id, "user")}} class=" mx-auto cursor-pointer" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>
                    {:else}
                        <p class=" text-center">NONE</p>
                    {/if}
                </td>
            </tr>
        {/each}
        </table>
    {/await}
{/if}