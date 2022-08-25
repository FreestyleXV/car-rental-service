<script>
import { onMount } from "svelte";

import Header from "./Header.svelte";
import { storedPermissions } from "./stores";

let reservations = []
let editRes = false
let error = false



let permission = ""
storedPermissions.subscribe(val=>{
    console.log(val, "sdiyfgdiu");
    permission = val
})

onMount(async ()=>{
    console.log($storedPermissions)
    let reservationsFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getReservations.php")
    let reservationsFetchList = await reservationsFetchRes.json()
    console.log(reservationsFetchList, "asdasds");
    console.log("ziemniaki")
    reservations = reservationsFetchList
})

async function acceptReservation(id, car){
    await fetch("http://localhost/Svelte/Svelte/public/backend/acceptReservation.php",{
        method:"POST",
        headers:{
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id='+id+'&car='+car
    })
    let reservationsFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getReservations.php")
    let reservationsFetchList = await reservationsFetchRes.json()
    reservations = reservationsFetchList

}

async function cancelReservation(id){
    await fetch("http://localhost/Svelte/Svelte/public/backend/cancelReservation.php",{
        method:"POST",
        headers:{
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id='+id
    })
    reservations.splice(reservations.findIndex(el=>el.id==id),1)
    reservations = reservations
}

async function handleEdit(){
    if(editRes){
        let startDate = new Date([...editRes.startDate.split("-")])
        let finishDate = new Date([...editRes.finishDate.split("-")])
        if(finishDate.getTime() <= startDate.getTime()){
            error = "niepoprawna data"
        }
        else{
            error = false
            await fetch("http://localhost/Svelte/Svelte/public/backend/editReservation.php",{
                method:"POST",
                headers:{
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id='+editRes.id+'&date='+editRes.finishDate+'&status='+editRes.status
            })
            let reservationsFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getReservations.php")
            let reservationsFetchList = await reservationsFetchRes.json()
            reservations = reservationsFetchList
            document.getElementById('edit').style.display="none"
            editRes = false
        }
    }
}

async function editReservation(reservation, e){
    editRes = reservation
    let editMenu = document.getElementById("edit")
    editMenu.style.display = "block"
    editMenu.style.top = (e.clientY-20)+"px"
    document.getElementById("edit").style.left = (e.clientX-20)+"px"

}

async function sort(category){
    reservations = reservations.sort((a,b)=>{

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
{#if permission == "mod"}
{#await reservations}
    loading
{:then reservations}
    <table>
        <tr>
            <th on:click={()=>{sort('id')}} class=" hover:underline cursor-pointer">ID</th>
            <th on:click={()=>{sort('startDate')}} class=" hover:underline cursor-pointer">Data od</th>
            <th on:click={()=>{sort('finishDate')}} class=" hover:underline cursor-pointer">Data do</th>
            <th on:click={()=>{sort('user')}} class=" hover:underline cursor-pointer">Użytkownik</th>
            <th on:click={()=>{sort('car')}} class=" hover:underline cursor-pointer">ID auta</th>
            <th on:click={()=>{sort('carBrand')}} class=" hover:underline cursor-pointer">Marka auta</th>
            <th on:click={()=>{sort('carModel')}} class=" hover:underline cursor-pointer">Model auta</th>
            <th on:click={()=>{sort('status')}} class=" hover:underline cursor-pointer">Status</th>
            <th>Action</th>
        </tr>
    
    {#each reservations as reservation}
        <tr>
            <td class=" p-2">{reservation.id}</td>
            <td class=" p-2">{reservation.startDate}</td>
            <td class=" p-2">{reservation.finishDate}</td>
            <td class=" p-2">{reservation.user}</td>
            <td class=" p-2">{reservation.car}</td>
            <td class=" p-2">{reservation.carBrand}</td>
            <td class=" p-2">{reservation.carModel}</td>
            {#if reservation.status=="p"}
                <td class=" p-2 bg-yellow-300">PENDING</td>
            {:else if reservation.status=="a"}
                <td class=" p-2 bg-green-300">ACTIVE</td>
            {:else if reservation.status=="o"}
                <td class=" p-2 bg-red-300">OVERDUE</td>
            {/if}
            <td class=" flex flex-row justify-center">
                {#if reservation.status=="p"}
                    <img on:click={()=>{acceptReservation(reservation.id, reservation.car)}} class=" w-9 cursor-pointer" src="http://localhost/svelte/svelte/public/assets/ok.svg" alt="ok">
                {:else}
                    <div class=" w-9"></div>
                {/if}
                <img on:click={()=>{cancelReservation(reservation.id)}} class=" w-9 cursor-pointer" src="http://localhost/svelte/svelte/public/assets/x.svg" alt="x">
                <img on:click={(e)=>{editReservation(reservation, e)}} class=" w-9 cursor-pointer" src="http://localhost/svelte/svelte/public/assets/edit.svg" alt="edit">
            </td>
        </tr>
    {/each}

    </table>
    <div id="edit" class=" absolute z-50 bg-gray-600 text-white p-3" style="display: none;">
        <p class=" text-center">EDYTUJ {editRes.id}</p>
        <input class=" border-0 bg-gray-600 text-right" type="date" bind:value={editRes.startDate} disabled>
        <input class=" border-0 bg-gray-600 text-right" type="date" bind:value={editRes.finishDate}>
        <p class=" text-center flex justify-around"><strong on:click={handleEdit} class=" cursor-pointer hover:bg-gray-900">ZATWIERDŹ</strong> <strong  class=" cursor-pointer hover:bg-gray-900" on:click={()=>{document.getElementById("edit").style.display="none"; editRes=false}}>ANULUJ</strong></p>
        {#if error}
            <p class="text-center bg-red-400">{error}</p>
        {/if}
    </div>
{/await}
{/if}

