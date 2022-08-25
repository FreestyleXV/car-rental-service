<script>
    import { onMount, createEventDispatcher, tick } from "svelte"
    

    import { storedLogin, storedPermissions } from "./stores"

    export let car

    let login = ""
    storedLogin.subscribe((value)=>{
        login = value
    })
    let permission = ''
    storedPermissions.subscribe((value)=>{
        console.log(value);
        permission = value
    })

    const dispatch = createEventDispatcher();
    let direction = 1
    let minimenu = document.createElement("div");
    let opened = false
    let hovered = false
    let HTML = document.createElement("div");
    let bounds = {width:0,height:0};
    let indexTimeout
    $: following = car.reservations.length

    let resize = async () => {
        bounds = HTML.getBoundingClientRect()
        if(opened){
            HTML.click()
        }
        else{
            minimenu.style.left = HTML.offsetLeft + "px"
            minimenu.style.top = HTML.offsetTop + "px"
        }
        // let res = await fetch("http://localhost/Svelte/Svelte/public/backend/getReservations.php?id="+car.id)
        // let json = await res.json()
        // following = json.length
    }


    onMount(async ()=> {
        HTML = document.getElementById("car"+car.id)
        minimenu = document.getElementById("carmenu"+car.id)
        let res = await fetch("http://localhost/Svelte/Svelte/public/backend/getReservations.php?id="+car.id)
        let json = await res.json()
        following = json.length

        window.setTimeout(()=>{bounds = HTML.getBoundingClientRect()},20)
        
        window.addEventListener("resize", resize)
        window.addEventListener('filter', resize)
        window.addEventListener('reserve', (e)=>{if(e.detail==car.id){following+=1}})
    })

    function mEnter(e){
        hovered = true
        if(!opened){
            minimenu.style.display = "block"
            minimenu.style.zIndex = 1
            this.style.zIndex = 2
        }
    }

    function mLeave(e){
        hovered = false
        if(!opened){
            minimenu.style.display = "none"
            minimenu.style.zIndex = -1
            this.style.zIndex = 0
        }
    }

    function mClick(e){
        window.clearTimeout(indexTimeout)
        opened = true
        this.style.zIndex = 2
        minimenu.style.zIndex = 1
        direction = e.clientX<window.innerWidth/2?1:-1
        window.setTimeout(()=>{
            if(minimenu.offsetLeft==this.offsetLeft && minimenu.offsetTop==this.offsetTop){
                if(bounds.width<window.innerWidth/2){
                    minimenu.style.left = ((this.offsetLeft + bounds.width*direction)-1) + "px"
                }
                else{
                    minimenu.style.top = (this.offsetTop + bounds.height) + "px"
                }
            }
            else{
                if(bounds.width<window.innerWidth/2){
                    minimenu.style.left = this.offsetLeft + "px"
                }
                else{
                    minimenu.style.top = this.offsetTop + "px"
                }
            }
        })
        indexTimeout = window.setTimeout(()=>{
            this.style.zIndex = 0
            minimenu.style.zIndex = 4
            if(minimenu.offsetLeft==this.offsetLeft && minimenu.offsetTop==this.offsetTop){opened=false}
            if(car.available && login != "" && car.uStatus == false && permission!='deactive'){
                if(opened){
                    minimenu.getElementsByClassName("isAvailable")[0].style.width = "25%";
                    minimenu.getElementsByClassName("isAvailable")[0].innerHTML = "";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.width = "75%";
                    minimenu.getElementsByClassName("rsrvBtn")[0].innerHTML = "ZAREZERWUJ";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.color = "rgba(255,255,255,1)";

                }
                else{
                    minimenu.getElementsByClassName("isAvailable")[0].style.width = "100%";
                    minimenu.getElementsByClassName("isAvailable")[0].innerHTML = "DOSTĘPNY";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.width = 0;
                    minimenu.getElementsByClassName("rsrvBtn")[0].innerHTML = "";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.color = "rgba(255,255,255,0)";
                }
            }
            else if(car.available && login != "" && car.uStatus == 'p'){
                if(opened){
                    minimenu.getElementsByClassName("isAvailable")[0].style.width = "50%";
                    minimenu.getElementsByClassName("isAvailable")[0].innerHTML = "";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.width = "50%";
                    minimenu.getElementsByClassName("rsrvBtn")[0].innerHTML = "ANULUJ";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.color = "rgba(255,255,255,1)";

                }
                else{
                    minimenu.getElementsByClassName("isAvailable")[0].style.width = "100%";
                    minimenu.getElementsByClassName("isAvailable")[0].innerHTML = "OCZEKIWANIE";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.width = 0;
                    minimenu.getElementsByClassName("rsrvBtn")[0].innerHTML = "";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.color = "rgba(255,255,255,0)";
                }
            }
            else if(login != "" && car.uStatus == 'a'){
                if(opened){
                    minimenu.getElementsByClassName("isAvailable")[0].style.width = "10%";
                    minimenu.getElementsByClassName("isAvailable")[0].innerHTML = "";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.width = "45%";
                    minimenu.getElementsByClassName("rsrvBtn")[0].innerHTML = "QR";
                    minimenu.getElementsByClassName("oddajBtn")[0].innerHTML = "ZWRÓC";
                    minimenu.getElementsByClassName("oddajBtn")[0].style.width = "45%";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.color = "rgba(255,255,255,1)";
                    minimenu.getElementsByClassName("oddajBtn")[0].style.color = "rgba(255,255,255,1)";

                }
                else{
                    minimenu.getElementsByClassName("isAvailable")[0].style.width = "100%";
                    minimenu.getElementsByClassName("isAvailable")[0].innerHTML = "WYNAJĘTO";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.width = 0;
                    minimenu.getElementsByClassName("rsrvBtn")[0].innerHTML = "";
                    minimenu.getElementsByClassName("rsrvBtn")[0].style.color = "rgba(255,255,255,0)";
                    minimenu.getElementsByClassName("oddajBtn")[0].style.color = "rgba(255,255,255,0)";
                    minimenu.getElementsByClassName("oddajBtn")[0].innerHTML = "";
                    minimenu.getElementsByClassName("oddajBtn")[0].style.width = 0;
                }
            }
            
        },400)
    }

    function openCar(){
        dispatch('openCar', {
            id: car.id,
        })
    }

    async function cancelCar(){
        let cancelRes = car.reservations.findIndex(el=>{if((el.status=='p'||el.status=='a') && el.user==login){return el}})
        if(car.reservations[cancelRes].status=='a'){
            car.available=true
        }
        await fetch("http://localhost/Svelte/Svelte/public/backend/cancelReservation.php",{
            method:"POST",
            headers:{
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id='+car.reservations[cancelRes].id
        })
        
        car.reservations.splice(cancelRes, 1)
        car.uStatus=false
        car = car
    }

    function showCode(){
        let res = car.reservations.find(el=>{if(el.status=='a' && el.user==login){return el}})
        dispatch('code', {code:res.id+login})
    }

    function filterBy(category, filter){
        dispatch('filterBy', {
            category,
            filter
        })
        HTML.click()
    }


</script>

<div id={"car"+car.id} on:mouseenter={mEnter} on:mouseleave={mLeave} on:click={mClick} class="px-4 md:w-1/3 w-full sm:mb-4 mb-6 cursor-pointer">
    <div class="rounded-lg w-full h-64 overflow-hidden">
        <img alt="content" class="object-cover object-center h-full w-full" src={`http://localhost/svelte/svelte/public/assets/cars/${car.id}.jpg`} draggable="false">
    </div>
</div>
<div id={"carmenu"+car.id} class=" text-left duration-300 absolute bg-gray-600 rounded-lg text-white cursor-default" style={`transition-property: left, top; width: ${bounds.width-3}px; height: ${bounds.height}px; top: ${HTML.offsetTop}px; left: ${HTML.offsetLeft}px; z-index: ${opened?1:-1}; display: ${(opened || hovered)?"block":"none"};`}>
    <div class=" flex flex-col justify-between p-4 h-full">
        <div class=" flex flex-row justify-between">
            <p>Marka: <strong class=" cursor-pointer hover:underline" on:click={()=>{filterBy("brand", car.brand)}}>{car.brand}</strong> </p>
            <div class=" flex flex-row justify-end">
                <img class=" w-7 inline mx-2" src="http://localhost/svelte/svelte/public/assets/watchSVG.svg" alt="followed">
                <p class=" align-middle">{following}</p>
            </div>
        </div>
        <p>Model: <strong class=" cursor-pointer hover:underline" on:click={()=>{filterBy("model", car.model)}}>{car.model}</strong></p>
        <p>Rok produkcji: <strong class=" cursor-pointer hover:underline" on:click={()=>{filterBy("year", car.year)}}>{car.year}</strong></p>
        <p>Cena najmu: <strong class=" cursor-pointer hover:underline" on:click={()=>{filterBy("price", car.price)}}>{car.price}zł</strong></p>
        {#if car.uStatus}
            {#if car.uStatus == 'p'}
                <div class="w-full flex flex-row" >
                    <div class=" w-full text-center rounded-xl bg-yellow-400 duration-200 isAvailable">OCZEKIWANIE</div>
                    <div on:click={cancelCar} class=" w-0 text-center rounded-xl inline-block bg-red-400 duration-200 cursor-pointer rsrvBtn" style="transition-property: width,color; color: rgba(255,255,255,0)"></div>
                </div>
            {:else if car.uStatus == 'a'}
                <div class="w-full flex flex-row" >
                    <div class=" w-full text-center rounded-xl text-gray-600 bg-white duration-200 isAvailable">WYNAJĘTO</div>
                    <div on:click={showCode} class=" w-0 text-center rounded-xl inline-block bg-green-400 duration-200 cursor-pointer rsrvBtn" style="transition-property: width,color; color: rgba(255,255,255,0)"></div>
                    <div on:click={cancelCar} class=" w-0 text-center rounded-xl inline-block bg-green-800 duration-200 cursor-pointer oddajBtn" style="transition-property: width,color; color: rgba(255,255,255,0)"></div>
                </div>
                
            {:else if car.uStatus == 'o'}
                <div class=" w-full text-center rounded-xl bg-red-400 isAvailable">PRZETRZYMANO</div>
            {/if}
        {:else}
            {#if car.available}
                <div class="w-full flex flex-row duration-200" >
                    <div class=" w-full text-center rounded-xl inline-block bg-green-400 duration-200 isAvailable" style="transition-property: width;">DOSTĘPNY</div>
                    <div on:click={openCar} class=" w-0 text-center rounded-xl inline-block bg-blue-400 duration-200 cursor-pointer rsrvBtn" style="transition-property: width,color; color: rgba(255,255,255,0)"></div>
                </div>
            {:else}
                <div class=" w-full text-center rounded-xl bg-red-400 isAvailable">NIEDOSTĘPNY</div>
            {/if}
        {/if}
        
    </div>
</div>