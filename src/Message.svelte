<script>

    export let message
    export let i

    let big = message.text.length>100?true:false
    let wrap = true

    function click(){
        wrap = !wrap
        message.read = true
    }
    function mEnter(){
        if(big){
            this.style.backgroundColor = "rgb(209, 213, 219)"
            this.style.color = "white"
        }
    }
    function mLeave(){
        if(big){
            this.style.color = "black"
            this.style.backgroundColor = "white"
        }
    }
    
</script>

<div on:click={click} on:mouseenter={mEnter} on:mouseleave={mLeave} class={" flex flex-row justify-start border-b-2 pt-2 pb-1 cursor-"+(big?"pointer":"default")+" select-none transition-colors duration-200"}>
    <div class=" ml-2 w-10 ">
        {#if !message.read}
            <img src='./assets/rysunek.svg' alt="NEW"/>
        {/if}
    </div>
    <div class=" mx-4">
        <p class=" text-center">MESSAGE {i+1}</p>
        <p class=" text-center"> {message.date} </p>
    </div>
    <div class=" w-10/12">
        <h2>{message.title}</h2>
        {#if big}
            <p class=" break-all mb-2">{wrap?(message.text.slice(0, 100)+"..."):message.text}</p>
        {:else}
            <p class=" mb-2">{message.text}</p>
        {/if}
    </div>
</div>