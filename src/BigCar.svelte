<script>
    import { onMount, createEventDispatcher } from "svelte"
    import { storedLogin } from "./stores";

    export let car

    let login = ""
    storedLogin.subscribe((value)=>{
        login = value
    })

    const reserveEvent = new CustomEvent('reserve', { detail: car.id });

    let price = 0
    let sDate
    let fDate
    let closeTimeout

    const dispatch = createEventDispatcher();
    function close(){
        window.clearTimeout(closeTimeout)
        dispatch("closeBigCar")
    }
    function count(){
        let start
        let end
        if(sDate != undefined){
            start = new Date([...sDate.split('-')])
            if(start.getTime() < Date.now()){sDate = undefined}
        }
        if(fDate != undefined){
            end = new Date([...fDate.split('-')])
            if(end.getTime() < Date.now()){fDate = undefined}
        }
        if(sDate != undefined && fDate != undefined){
            if(end.getTime() <= start.getTime()){
                sDate = undefined
                price = 0
            }
            else{
                let days = (end.getTime() - start.getTime())/(1000*3600*24)
                price = car.price * days
            }
        }
        else{
            price = 0
        }
    }

    let sendRes
    async function sendRequest(){
    fetch("http://localhost/Svelte/Svelte/public/backend/handleReservation.php", {
            method:"POST",
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user=${login}&car=${car.id}&startDate=${sDate}&finishDate=${fDate}&cost=${price}`
        }).then(res=>res.text()).then(text=>{
            sendRes = text
            if(sendRes=="OK"){
                window.dispatchEvent(reserveEvent)
                closeTimeout = window.setTimeout(close,500)
            }
        })
    }

</script>

<div on:click={close} class=" fixed top-0 left-0 w-full h-full z-40" style="background: rgba(0, 0, 0, 0.8);"></div>
<div class=" fixed flex flex-col justify-between p-5 top-1/4 md:left-1/4 left-0 md:w-1/2 w-full h-1/2 z-50 bg-gray-600 text-white rounded-lg">
    {#if sendRes}
        <p class={` text-center ${sendRes=="OK"?"bg-green-400":"bg-red-400"}`}>{sendRes}</p>
    {/if}
    <div class=" flex flex-row justify-evenly">
        <div class=" flex flex-col">
            <input type="date" name="sDate" id="sDate" class=" bg-gray-600 border-0" bind:value={sDate} on:change={count}>
        </div>
        <div class=" flex flex-col">
            <input type="date" name="fDate" id="fDate" class=" bg-gray-600 border-0" bind:value={fDate} on:change={count}>
        </div>
    </div>
    <p class=" text-center">CENA: {price}zł</p>
    <p class=" text-center cursor-pointer" on:click={sendRequest}>WYŚLIJ PROŚBĘ</p>
</div>